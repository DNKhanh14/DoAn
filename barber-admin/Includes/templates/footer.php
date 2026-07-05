
</div>
<footer class="sticky-footer bg-white">
    <div class="container my-auto">
        <div class="copyright text-center my-auto">
            <span>Bản quyền &copy; Hệ thống Barber  <?= date('Y') ?></span>
        </div>
    </div>
</footer>
</div>
</div>

<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Đăng xuất?</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">Bạn có chắc muốn kết thúc phiên làm việc?</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Hủy</button>
                <a class="btn btn-primary" href="index.php?route=logout">Đăng xuất</a>
            </div>
        </div>
    </div>
</div>

<script src="Design/js/jquery.min.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="Design/js/bootstrap.bundle.min.js"></script>
<script src="Design/js/sb-admin-2.min.js"></script>
<script src="Design/js/main.js"></script>
<script src="Design/js/validation.js"></script>
<script src="Design/js/money-input.js"></script>
<?php if (!empty($extraJs)): ?>
<script src="<?= htmlspecialchars($extraJs) ?>"></script>
<?php endif; ?>
</body>
</html>
