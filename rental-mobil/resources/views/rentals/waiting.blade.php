@extends('layouts.app')

@section('title', 'Menunggu Validasi')

@section('content')
<main class="flex-1 flex items-center justify-center py-16 px-8">
    <div class="bg-white max-w-lg w-full rounded-[2.5rem] shadow-xl shadow-teal-900/5 border border-gray-100 p-12 text-center relative overflow-hidden">
        
        <!-- Decorative Background -->
        <div class="absolute -top-32 -right-32 w-64 h-64 bg-teal-50 rounded-full mix-blend-multiply filter blur-3xl opacity-60"></div>
        <div class="absolute -bottom-32 -left-32 w-64 h-64 bg-blue-50 rounded-full mix-blend-multiply filter blur-3xl opacity-60"></div>

        <div class="relative z-10">
            <!-- Animation / Icon Box -->
            <div class="w-28 h-28 mx-auto bg-teal-50 rounded-full flex items-center justify-center mb-8 relative">
                <!-- Outer Pulse Rings -->
                <div class="absolute inset-0 bg-teal-100 rounded-full animate-ping opacity-20"></div>
                <div class="absolute inset-2 bg-teal-200 rounded-full animate-pulse opacity-40"></div>
                
                <!-- Inner Icon -->
                <div class="w-20 h-20 bg-white rounded-full shadow-md flex items-center justify-center text-teal-500 z-10 relative">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            <!-- Texts -->
            <h2 class="text-3xl font-black text-gray-900 mb-4 tracking-tight">Pembayaran Diterima!</h2>
            <p class="text-gray-500 mb-8 font-medium leading-relaxed">
                Terima kasih! Bukti konfirmasi Anda telah kami terima. Saat ini, transaksi sedang <span class="text-teal-600 font-bold">divalidasi oleh admin</span> kami.
            </p>

            <!-- Status Card -->
            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-6 mb-10 text-left">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-black text-gray-400 uppercase tracking-widest">Status Validasi</span>
                    <span class="bg-amber-100 text-amber-700 text-xs font-bold px-3 py-1 rounded-full animate-pulse">Menunggu Verifikasi</span>
                </div>
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center text-amber-500 flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-900">Pengecekan Mutasi Bank</p>
                        <p class="text-xs text-gray-500 mt-1 font-medium">Estimasi waktu: 1 - 5 menit. Silakan pantau halaman <a href="{{ route('dashboard') }}" class="text-teal-600 hover:underline">Informasi Pesanan</a> Anda.</p>
                    </div>
                </div>
            </div>

            <!-- Back to Home Button -->
            <a href="{{ route('cars.index') }}" class="block w-full bg-slate-900 hover:bg-slate-800 text-white font-extrabold py-4 px-6 rounded-2xl shadow-lg shadow-slate-900/20 transition-all duration-300 hover:-translate-y-1 text-center">
                Kembali ke Katalog
            </a>
        </div>
    </div>
</main>
@endsection
