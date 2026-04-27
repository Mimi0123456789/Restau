<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;

class EmployeDashboardController extends Controller
{
    public function index()
    {
        return view('dashboard.employe.index');
    }
}
