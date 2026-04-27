<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;

class ClientDashboardController extends Controller
{
    public function index()
    {
        return view('dashboard.client.index');
    }
}
