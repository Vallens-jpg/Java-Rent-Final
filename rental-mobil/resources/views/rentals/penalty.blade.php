@extends('layouts.app')

@section('title', 'Bayar Denda')

@section('content')

<main class="max-w-5xl mx-auto px-8 py-8 w-full">
    
    <div class="bg-white rounded-[2.5rem] shadow-xl shadow-red-900/5 border border-red-100 overflow-hidden relative">
        
        <!-- Decorative Red Glow -->
        <div class="absolute -top-32 -right-32 w-64 h-64 bg-red-50 rounded-full mix-blend-multiply filter blur-3xl opacity-60 pointer-events-none"></div>

        <!-- Header Section Card -->
        <div class="bg-red-50/50 px-10 py-6 border-b border-red-100 flex items-center gap-4 relative z-10">
            <a href="{{ route('dashboard') }}" class="w-10 h-10 rounded-full bg-white border border-red-200 flex items-center justify-center text-red-500 hover:bg-red-100 hover:text-red-700 transition-colors shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h2 class="text-xl font-extrabold text-red-700">Penyelesaian Keterlambatan</h2>
                <p class="text-sm font-medium text-red-400">TRX-9823471 &bull; {{ $rental->car->brand ?? 'Innova Reborn' }}</p>
            </div>
        </div>

        <div class="p-10 lg:p-14 flex flex-col lg:flex-row gap-16 relative z-10">
            
            <!-- Left Side: Delay Info Fields -->
            <div class="flex-1 lg:border-r border-gray-100 lg:pr-12">
                <h3 class="text-lg font-bold text-gray-900 mb-8">Rincian Denda</h3>

                <div class="space-y-8">
                    <!-- Keterlambatan Field -->
                    <div>
                        <label class="block text-xs font-black text-gray-400 mb-2 uppercase tracking-wider">Durasi Keterlambatan</label>
                        <div class="flex items-center gap-4 bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 shadow-inner">
                            <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-red-500 shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <input type="text" readonly value="12:16:00" class="bg-transparent border-none text-2xl font-black text-gray-900 tabular-nums w-full outline-none cursor-not-allowed">
                        </div>
                    </div>

                    <!-- Denda Field -->
                    <div>
                        <label class="block text-xs font-black text-gray-400 mb-2 uppercase tracking-wider">Total Denda Harus Dibayar</label>
                        <div class="flex items-center gap-4 bg-red-50 border border-red-100 rounded-2xl px-5 py-4 shadow-inner">
                            <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-red-600 shadow-sm border border-red-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <input type="text" readonly value="Rp 100.000,00" class="bg-transparent border-none text-3xl font-black text-red-600 w-full outline-none cursor-not-allowed">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Payment Details -->
            <div class="lg:w-[380px] flex-shrink-0">
                <h3 class="text-lg font-bold text-gray-900 mb-8">Cara Pembayaran</h3>

                <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm text-center">
                    
                    <!-- Instruction Image Box / QRIS -->
                    <div class="w-full aspect-square bg-slate-50 rounded-2xl border-2 border-dashed border-gray-200 mb-6 flex flex-col items-center justify-center p-6 text-gray-400 group hover:border-blue-300 transition-colors cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 mb-4 text-slate-300 group-hover:text-blue-400 transition-colors" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M3 3h8v8H3zm2 2v4h4V5zM13 3h8v8h-8zm2 2v4h4V5zM3 13h8v8H3zm2 2v4h4v-4zm13-2h-2v2h2v-2zm-2 2h-2v2h2v-2zm2 2h-2v2h2v-2zm2-2h-2v2h2v-2zm-4 4h-2v2h2v-2zm2 2h-2v2h2v-2zm-6-8h2v2h-2v-2zm0 4h2v2h-2v-2zm0 4h2v2h-2v-2z"/>
                        </svg>
                        <span class="text-xs font-bold uppercase tracking-wider">Scan QRIS</span>
                    </div>

                    <!-- Bank Details -->
                    <div class="bg-slate-50 rounded-2xl p-5 border border-slate-100 relative overflow-hidden">
                        <div class="absolute -top-6 -right-6 w-16 h-16 bg-blue-100 rounded-full opacity-50"></div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-2">Transfer Manual</p>
                        <p class="text-2xl font-black text-gray-900 mb-1 tracking-widest relative z-10">1234 5678 90</p>
                        <p class="text-xs font-bold text-gray-500 relative z-10">a.n. Budiono Siregar</p>
                        <p class="text-blue-600 font-black text-sm mt-1 relative z-10">Bank Jahat</p>
                    </div>

                    <!-- Confirm Button -->
                    <form action="{{ route('rentals.confirm', $rental->id ?? 999) }}" method="POST" class="mt-6">
                        @csrf
                        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-extrabold py-4 rounded-xl shadow-lg shadow-red-600/30 transition-all hover:-translate-y-1">
                            Konfirmasi Pembayaran
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</main>
@endsection
