@extends('layouts.app')

@section('title', $car->brand ?? 'Detail Mobil')

@section('content')
<!-- Main Detail Section -->
<main class="max-w-7xl mx-auto px-8 py-10 flex-1 w-full">
    
    <!-- Back Button -->
    <a href="{{ route('cars.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-blue-600 transition-colors mb-8 group">
        <div class="w-8 h-8 rounded-full bg-white border border-gray-200 flex items-center justify-center group-hover:border-blue-600 transition-colors shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </div>
        Kembali ke Katalog
    </a>

    <div class="flex flex-col lg:flex-row gap-10 items-start">
        
        <!-- Left Side (Photo & Description) -->
        <div class="flex-1 w-full">
            <!-- Photo Box -->
            <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm mb-10 flex items-center justify-center min-h-[450px] relative overflow-hidden group">
                <!-- Background decorative blob -->
                <div class="absolute -top-24 -right-24 w-64 h-64 bg-slate-50 rounded-full mix-blend-multiply filter blur-3xl opacity-50"></div>
                <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-blue-50 rounded-full mix-blend-multiply filter blur-3xl opacity-50"></div>
                
                @if(isset($car) && $car->image)
                    <img src="{{ Str::startsWith($car->image, 'http') ? $car->image : asset('storage/' . $car->image) }}" alt="{{ $car->brand }}" class="w-full max-w-xl object-contain relative z-10 group-hover:scale-105 transition-transform duration-700 rounded-xl shadow-lg">
                @else
                    <!-- Placeholder Car Vector -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-72 h-72 text-slate-200 relative z-10 group-hover:scale-105 transition-transform duration-700" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 8l-1.63-3.67C17.06 3.51 16.27 3 15.36 3H8.64c-.9 0-1.7.51-2.01 1.33L5 8H3c-1.1 0-2 .9-2 2v7h2v2c0 1.1.9 2 2 2h1c1.1 0 2-.9 2-2v-2h8v2c0 1.1.9 2 2 2h1c1.1 0 2-.9 2-2v-2h2v-7c0-1.1-.9-2-2-2h-2zM6.5 15C5.67 15 5 14.33 5 13.5S5.67 12 6.5 12 8 12.67 8 13.5 7.33 15 6.5 15zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM8.33 8l1.33-3h4.68l1.33 3H8.33z"/>
                    </svg>
                @endif
            </div>

            <!-- Description -->
            <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm">
                <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Deskripsi Kendaraan
                </h2>
                <div class="prose prose-blue max-w-none text-gray-600 leading-relaxed">
                    <p>
                        Mobil <strong class="text-gray-900">{{ $car->brand ?? 'Sesuai Pilihan Anda' }}</strong> merupakan opsi ideal untuk mobilitas tinggi, baik untuk perjalanan bisnis, liburan keluarga, maupun berkeliling kota. Memiliki kapasitas <strong class="text-gray-900">{{ $car->size ?? '5 Seat' }}</strong>, mobil ini menawarkan perpaduan sempurna antara kenyamanan kabin dan efisiensi bahan bakar.
                    </p>
                    <p class="mt-4">
                        Kami menjamin kebersihan unit secara menyeluruh sebelum serah terima. AC dingin, mesin terawat secara berkala, dan dilengkapi dengan surat-surat kendaraan yang lengkap. Demi keamanan Anda, kendaraan ini rutin melewati pengecekan rem, ban, dan kelistrikan sebelum disewakan.
                    </p>
                </div>
            </div>
        </div>

        <!-- Right Side (Specs & Actions) -->
        <div class="lg:w-[420px] flex-shrink-0 w-full">
            <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm sticky top-8">
                
                <div class="mb-8">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="bg-blue-100 text-blue-700 text-xs font-bold px-2.5 py-1 rounded-md uppercase tracking-wide">
                            {{ $car->status ?? 'Tersedia' }}
                        </span>
                        <span class="bg-slate-100 text-slate-600 text-xs font-bold px-2.5 py-1 rounded-md uppercase tracking-wide">
                            Plat: {{ $car->plate_number ?? 'B 1234 XYZ' }}
                        </span>
                    </div>
                    <h1 class="text-3xl font-extrabold text-gray-900 mb-2">{{ $car->brand ?? 'Toyota Avanza Terbaru' }}</h1>
                </div>
                
                <div class="mb-8 p-6 bg-slate-50 rounded-2xl border border-slate-100">
                    <p class="text-[11px] text-gray-400 font-bold uppercase tracking-widest mb-1">Harga Sewa</p>
                    <div class="flex items-baseline gap-1">
                        <p class="text-blue-600 font-extrabold text-4xl">Rp {{ number_format($car->price_per_hour ?? 50000, 0, ',', '.') }}</p>
                        <p class="text-base text-gray-500 font-medium">/ jam</p>
                    </div>
                </div>

                <!-- Specs Grid -->
                <div class="grid grid-cols-2 gap-4 mb-8">
                    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex flex-col gap-3">
                        <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Transmisi</p>
                            <p class="font-bold text-gray-900">{{ $car->transmission ?? 'Automatic' }}</p>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex flex-col gap-3">
                        <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Kapasitas</p>
                            <p class="font-bold text-gray-900">{{ $car->size ?? '5 Seat' }}</p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-4">
                    <a href="{{ auth()->check() ? route('rentals.create', $car->id ?? 1) : route('login') }}" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-xl shadow-lg shadow-blue-600/20 transition-all duration-300 flex justify-center items-center gap-2 group hover:-translate-y-0.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        <span>Pesan Unit Sekarang</span>
                    </a>

                    <a href="https://wa.me/6281234567890" target="_blank" class="w-full bg-white hover:bg-green-50 text-green-600 border-2 border-green-500 font-bold py-3.5 rounded-xl transition-all duration-300 flex justify-center items-center gap-2 group">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="group-hover:scale-110 transition-transform">
                            <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
                        </svg>
                        <span>Hubungi Admin</span>
                    </a>
                </div>

            </div>
        </div>
        
    </div>

</main>
@endsection
