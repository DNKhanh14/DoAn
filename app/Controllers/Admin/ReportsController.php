<?php

namespace App\Controllers\Admin;

use App\Models\Expense;
use App\Models\Hr;
use App\Models\Order;
use App\Models\Report;

class ReportsController extends AdminController
{
    public function index(): void
    {
        $pageTitle = 'Báo cáo';
        $from = $_GET['from'] ?? date('Y-m-d');
        $to = $_GET['to'] ?? date('Y-m-d');
        $tab = $_GET['tab'] ?? 'revenue';
        $report = new Report();
        $expenseModel = new Expense();
        $message = $_GET['msg'] ?? null;
        if ($message === 'expense_added') {
            $message = 'Đã thêm chi phí.';
        }

        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['add_expense'])) {
            $expenseModel->add(
                test_input($_POST['category']),
                parse_vnd_input($_POST['amount'] ?? 0),
                $_POST['expense_date'],
                test_input($_POST['note'] ?? '')
            );
            $redirectParams = [
                'tab' => 'cashflow',
                'from' => $_POST['from'] ?? $from,
                'to' => $_POST['to'] ?? $to,
                'msg' => 'expense_added',
            ];
            if (!empty($_POST['cf_type'])) {
                $redirectParams['cf_type'] = $_POST['cf_type'];
            }
            if (!empty($_POST['cf_payment'])) {
                $redirectParams['cf_payment'] = $_POST['cf_payment'];
            }
            $this->redirect(admin_route('reports', $redirectParams));
        }

        $upgradeRequired = salon_upgrade_required();
        $revenue = $upgradeRequired ? 0 : $report->revenueBetween($from, $to);
        $expenses = $upgradeRequired ? 0 : $expenseModel->totalBetween($from, $to);
        $profit = $revenue - $expenses;

        $invoiceSummary = $upgradeRequired
            ? ['count' => 0, 'revenue' => 0, 'paid' => 0, 'debt' => 0]
            : $report->invoiceSummary($from, $to);
        $orders = $upgradeRequired ? [] : $report->ordersBetween($from, $to);
        $staffCommissions = $upgradeRequired ? [] : $report->employeeCommissions($from, $to);
        $incomeTx = $upgradeRequired ? [] : $report->incomeTransactions($from, $to);
        $expenseTx = $upgradeRequired ? [] : $report->expenseTransactions($from, $to);
        $topCustomers = $upgradeRequired ? [] : $report->topCustomers($from, $to);
        $taxGroupBy = $_GET['tax_group'] ?? 'day';
        $taxRate = max(0, min(100, (float) ($_GET['tax_rate'] ?? 10)));
        $taxStats = $upgradeRequired
            ? ['rows' => [], 'total_revenue' => 0, 'total_tax' => 0, 'total_orders' => 0]
            : $report->taxStatistics($from, $to, $taxGroupBy, $taxRate);
        $inventory = $upgradeRequired ? [] : $report->inventorySummary();

        $staffTotal = array_sum(array_column($staffCommissions, 'commission_est'));

        $cfType = $_GET['cf_type'] ?? '';
        $cfPayment = $_GET['cf_payment'] ?? '';
        $cashflowRows = $this->buildCashflowRows($incomeTx, $expenseTx);
        $cashflowRows = $this->filterCashflowRows($cashflowRows, $cfType, '', $cfPayment);

        $this->adminView('reports/index', [
            'pageTitle' => $pageTitle,
            'from' => $from,
            'to' => $to,
            'tab' => $tab,
            'revenue' => $revenue,
            'expenses' => $expenses,
            'profit' => $profit,
            'invoiceSummary' => $invoiceSummary,
            'orders' => $orders,
            'staffCommissions' => $staffCommissions,
            'staffTotal' => $staffTotal,
            'incomeTx' => $incomeTx,
            'expenseTx' => $expenseTx,
            'topCustomers' => $topCustomers,
            'taxStats' => $taxStats,
            'taxGroupBy' => $taxGroupBy,
            'taxRate' => $taxRate,
            'inventory' => $inventory,
            'message' => $message,
            'cfType' => $cfType,
            'cfPayment' => $cfPayment,
            'cashflowRows' => $cashflowRows,
        ]);
    }

    public function orderDetail(): void
    {
        $pageTitle = 'Chi tiết hóa đơn';
        $id = (int) ($_GET['id'] ?? 0);
        $orderModel = new Order();
        $order = $orderModel->find($id);

        if (!$order) {
            $this->redirect(admin_route('reports', ['tab' => 'revenue']));
        }

        $items = $orderModel->getItems($id);
        $hr = new Hr();

        foreach ($items as &$item) {
            $item['commission'] = $hr->commissionForItem($item);
        }
        unset($item);

        $code = $order['ma_don'] ?? ('HD' . str_pad((string) $order['ma_don_hang'], 6, '0', STR_PAD_LEFT));
        $pageTitle = 'Chi tiết hoá đơn #' . $code;

        $this->adminView('reports/order', [
            'pageTitle' => $pageTitle,
            'order' => $order,
            'items' => $items,
            'orderCode' => $code,
            'from' => $_GET['from'] ?? date('Y-m-d', strtotime($order['ngay_tao'])),
            'to' => $_GET['to'] ?? date('Y-m-d', strtotime($order['ngay_tao'])),
            'tab' => $_GET['tab'] ?? 'revenue',
        ]);
    }

    /** @param array<int, array<string, mixed>> $incomeTx */
    /** @param array<int, array<string, mixed>> $expenseTx */
    private function buildCashflowRows(array $incomeTx, array $expenseTx): array
    {
        $rows = [];
        foreach ($incomeTx as $tx) {
            $cust = trim(($tx['ten'] ?? '') . ' ' . ($tx['ho_dem'] ?? ''));
            $code = $tx['ma_don'] ?? ('HD' . str_pad((string) $tx['ma_don_hang'], 6, '0', STR_PAD_LEFT));
            $methodKey = $tx['phuong_thuc_thanh_toan'] ?? 'cash';
            $rows[] = [
                'type' => 'THU',
                'date' => $tx['ngay_tao'],
                'code' => $code,
                'order_id' => (int) $tx['ma_don_hang'],
                'person' => $cust !== '' ? $cust : 'Khách vãng lai',
                'category' => 'Bán hàng',
                'reason' => 'Thanh toán hóa đơn #' . $code,
                'method' => payment_method_label($methodKey),
                'method_key' => $methodKey,
                'amount' => (float) $tx['total'],
                'is_income' => true,
            ];
        }
        foreach ($expenseTx as $ex) {
            $note = (string) ($ex['note'] ?? '');
            $methodKey = 'cash';
            if (preg_match('/\[pm:([a-z_]+)\]/', $note, $m)) {
                $methodKey = $m[1];
            }
            $person = '—';
            if (preg_match('/NV:\s*([^.\[]+)/u', $note, $m)) {
                $person = trim($m[1]);
            }
            $rows[] = [
                'type' => 'CHI',
                'date' => $ex['ngay_chi'] . ' 00:00:00',
                'code' => 'CP' . str_pad((string) $ex['ma_chi_phi'], 5, '0', STR_PAD_LEFT),
                'order_id' => 0,
                'person' => $person,
                'category' => $ex['category'],
                'reason' => preg_replace('/\s*\[pm:[a-z_]+\]\s*$/', '', $note) ?: $ex['category'],
                'method' => payment_method_label($methodKey),
                'method_key' => $methodKey,
                'amount' => (float) $ex['amount'],
                'is_income' => false,
            ];
        }
        usort($rows, fn($a, $b) => strtotime($b['date']) <=> strtotime($a['date']));

        return $rows;
    }

    /** @param array<int, array<string, mixed>> $rows */
    private function cashflowCategoryOptions(array $rows): array
    {
        $cats = [];
        foreach ($rows as $row) {
            $cat = $row['category'] ?? '';
            if ($cat !== '' && !in_array($cat, $cats, true)) {
                $cats[] = $cat;
            }
        }
        sort($cats);

        return $cats;
    }

    /** @param array<int, array<string, mixed>> $rows */
    private function filterCashflowRows(array $rows, string $type, string $category, string $payment): array
    {
        return array_values(array_filter($rows, function (array $row) use ($type, $category, $payment) {
            if ($type !== '' && ($row['type'] ?? '') !== $type) {
                return false;
            }
            if ($category !== '' && ($row['category'] ?? '') !== $category) {
                return false;
            }
            if ($payment !== '' && ($row['method_key'] ?? '') !== $payment) {
                return false;
            }

            return true;
        }));
    }
}
