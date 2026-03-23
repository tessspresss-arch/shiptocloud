<?php

namespace App\Http\Controllers;

class AdminDashboardController extends Controller
{
    public function index(DashboardModernController $dashboardModernController)
    {
        return $dashboardModernController->index();
    }
}
