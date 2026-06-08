<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Customer dashboard
        return view('dashboard.customer');
    }

    public function adminIndex()
    {
        // Admin dashboard
        return view('dashboard.admin');
    }
}
