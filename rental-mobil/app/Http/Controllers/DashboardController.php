<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil pesanan yang sedang aktif atau pending milik user yang login
        $rental = \App\Models\Rental::with('car')
            ->where('user_id', auth()->id())
            ->latest()
            ->first();
            
        if ($rental && $rental->status == 'completed') {
            $rental = null;
        }

        // Customer dashboard
        return view('dashboard.customer', compact('rental'));
    }

    public function adminIndex()
    {
        // Admin dashboard
        return view('dashboard.admin');
    }
}
