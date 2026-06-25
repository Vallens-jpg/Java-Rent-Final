<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil semua pesanan yang sedang aktif, pending, atau rejected milik user yang login
        $rentals = \App\Models\Rental::with('car')
            ->where('user_id', auth()->id())
            ->whereIn('status', ['pending', 'active', 'rejected'])
            ->latest()
            ->get();

        // Customer dashboard
        return view('dashboard.customer', compact('rentals'));
    }

    public function adminIndex()
    {
        // Admin dashboard
        return view('dashboard.admin');
    }
}
