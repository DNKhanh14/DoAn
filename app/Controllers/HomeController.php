<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ServiceCategory;

class HomeController extends Controller
{
    public function index(): void
    {
        $categoryModel = new ServiceCategory();
        $categories = $categoryModel->getAllWithServices();

        $this->view('home/index', [
            'categories' => $categories,
        ]);
    }
}
