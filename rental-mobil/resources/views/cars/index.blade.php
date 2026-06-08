@extends('layouts.app')

@section('title', 'Katalog Mobil')

@section('content')
<!-- Main Catalog Section -->
<main class="max-w-7xl mx-auto px-8 py-10 flex-1 w-full">
    
    <!-- Filter Bar -->
    <form action="{{ route('cars.index') }}" method="GET" class="flex flex-wrap items-center justify-end gap-3 mb-8">
        @if(request('search'))
            <input type="hidden" name="search" value="{{ request('search') }}">
        @endif
        
        <select name="size" onchange="this.form.submit()" class="bg-white px-4 py-2.5 rounded-xl shadow-sm border border-gray-200 text-gray-700 font-medium text-sm outline-none focus:ring-2 focus:ring-teal-500 cursor-pointer">
            <option value="">Semua Kapasitas</option>
            <option value="4 Seat" {{ request('size') == '4 Seat' ? 'selected' : '' }}>4 Seat</option>
            <option value="5 Seat" {{ request('size') == '5 Seat' ? 'selected' : '' }}>5 Seat</option>
            <option value="6 Seat" {{ request('size') == '6 Seat' ? 'selected' : '' }}>6 Seat</option>
            <option value="7 Seat" {{ request('size') == '7 Seat' ? 'selected' : '' }}>7 Seat</option>
        </select>
        
        <select name="transmission" onchange="this.form.submit()" class="bg-white px-4 py-2.5 rounded-xl shadow-sm border border-gray-200 text-gray-700 font-medium text-sm outline-none focus:ring-2 focus:ring-teal-500 cursor-pointer">
            <option value="">Semua Transmisi</option>
            <option value="Manual" {{ request('transmission') == 'Manual' ? 'selected' : '' }}>Manual</option>
            <option value="Automatic" {{ request('transmission') == 'Automatic' ? 'selected' : '' }}>Automatic</option>
        </select>
        
        @if(request('size') || request('transmission') || request('search'))
            <a href="{{ route('cars.index') }}" class="px-4 py-2.5 text-sm font-bold text-red-500 hover:bg-red-50 rounded-xl transition-colors">Reset Filter</a>
        @endif
    </form>

    <!-- Grid Cards (4 columns x 2 rows = 8 cards max per page typically) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @forelse($cars as $car)
            @if($car->status == 'rented')
            <div class="group bg-slate-50 rounded-2xl shadow-sm overflow-hidden border border-gray-200 flex flex-col opacity-70 cursor-not-allowed relative grayscale-[30%]">
            @else
            <a href="{{ route('cars.show', $car->id) }}" class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 flex flex-col relative cursor-pointer">
            @endif
                <!-- Illustration / Image -->
                <div class="aspect-[4/3] bg-slate-100 p-4 flex items-center justify-center relative overflow-hidden">
                    @if($car->image)
                        <img src="{{ Str::startsWith($car->image, 'http') ? $car->image : asset('storage/' . $car->image) }}" alt="{{ $car->brand }}" class="object-cover w-full h-full group-hover:scale-105 transition-transform duration-500 rounded-xl">
                    @else
                        <!-- Placeholder Car Vector/Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-32 h-32 text-slate-300 group-hover:scale-110 transition-transform duration-500" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19 8l-1.63-3.67C17.06 3.51 16.27 3 15.36 3H8.64c-.9 0-1.7.51-2.01 1.33L5 8H3c-1.1 0-2 .9-2 2v7h2v2c0 1.1.9 2 2 2h1c1.1 0 2-.9 2-2v-2h8v2c0 1.1.9 2 2 2h1c1.1 0 2-.9 2-2v-2h2v-7c0-1.1-.9-2-2-2h-2zM6.5 15C5.67 15 5 14.33 5 13.5S5.67 12 6.5 12 8 12.67 8 13.5 7.33 15 6.5 15zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM8.33 8l1.33-3h4.68l1.33 3H8.33z"/>
                        </svg>
                    @endif
                    <div class="absolute top-3 right-3 bg-white/90 backdrop-blur text-xs font-bold px-2 py-1.5 rounded-lg shadow-sm text-slate-700 uppercase tracking-wide">
                        {{ $car->transmission }}
                    </div>
                    
                    @if($car->status == 'rented')
                    <div class="absolute inset-0 bg-slate-900/10 flex items-center justify-center">
                        <span class="bg-red-500/90 backdrop-blur text-white text-xs font-black px-4 py-2 rounded-xl shadow-lg uppercase tracking-widest border border-white/20">Sedang Disewa</span>
                    </div>
                    @endif
                </div>
                
                <!-- Details -->
                <div class="p-5 flex-1 flex flex-col">
                    <h3 class="font-bold text-lg text-gray-900 truncate group-hover:text-blue-600 transition-colors">{{ $car->brand }}</h3>
                    <p class="text-sm text-gray-500 mb-4 flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        Kapasitas {{ $car->size }}
                    </p>
                    
                    <div class="mt-auto pt-4 border-t border-gray-50 flex items-end justify-between">
                        <div>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Harga Sewa</p>
                            <p class="text-blue-600 font-bold text-xl">Rp {{ number_format($car->price_per_hour, 0, ',', '.') }}<span class="text-xs text-gray-500 font-normal">/jam</span></p>
                        </div>
                    </div>
                </div>
            @if($car->status == 'rented')
            </div>
            @else
            </a>
            @endif
        @empty
            <!-- Fallback Mockup Data (8 Cards) if Database is Empty -->
            @for($i = 1; $i <= 8; $i++)
            <a href="{{ url('/cars/' . $i) }}" class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 flex flex-col cursor-pointer">
                <div class="aspect-[4/3] bg-slate-50 p-4 flex items-center justify-center relative overflow-hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-32 h-32 text-slate-200 group-hover:scale-110 transition-transform duration-500" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 8l-1.63-3.67C17.06 3.51 16.27 3 15.36 3H8.64c-.9 0-1.7.51-2.01 1.33L5 8H3c-1.1 0-2 .9-2 2v7h2v2c0 1.1.9 2 2 2h1c1.1 0 2-.9 2-2v-2h8v2c0 1.1.9 2 2 2h1c1.1 0 2-.9 2-2v-2h2v-7c0-1.1-.9-2-2-2h-2zM6.5 15C5.67 15 5 14.33 5 13.5S5.67 12 6.5 12 8 12.67 8 13.5 7.33 15 6.5 15zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM8.33 8l1.33-3h4.68l1.33 3H8.33z"/>
                    </svg>
                    <div class="absolute top-3 right-3 bg-white border border-gray-100 text-[10px] font-bold px-2 py-1.5 rounded-lg shadow-sm text-slate-400 uppercase tracking-wide">
                        Automatic
                    </div>
                </div>
                <div class="p-5 flex-1 flex flex-col">
                    <div class="h-6 w-3/4 bg-slate-200 rounded animate-pulse mb-2"></div>
                    <div class="h-4 w-1/3 bg-slate-100 rounded animate-pulse mb-6"></div>
                    
                    <div class="mt-auto pt-4 border-t border-gray-50 flex items-end justify-between">
                        <div class="w-full">
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1">Harga Sewa</p>
                            <div class="h-6 w-1/2 bg-blue-100 rounded animate-pulse"></div>
                        </div>
                    </div>
                </div>
            </a>
            @endfor
        @endforelse
    </div>

</main>
@endsection
