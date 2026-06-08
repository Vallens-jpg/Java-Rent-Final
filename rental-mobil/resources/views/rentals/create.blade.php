<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Pemesanan - Rental Mobil</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 font-sans antialiased text-gray-900 min-h-screen">

    <!-- Simple Top Bar -->
    <div class="bg-white shadow-sm border-b border-gray-100 py-4 px-8 flex items-center justify-between sticky top-0 z-50">
        <div class="flex items-center gap-4">
            <a href="{{ url()->previous() }}" class="w-10 h-10 rounded-full bg-slate-50 border border-slate-200 flex items-center justify-center text-slate-500 hover:bg-slate-100 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <h1 class="text-xl font-bold text-gray-800">Formulir Pemesanan</h1>
        </div>
    </div>

    <main class="max-w-7xl mx-auto px-8 py-10">
        <form action="{{ route('rentals.store') }}" method="POST" class="flex flex-col lg:flex-row gap-10">
            @csrf
            <input type="hidden" name="car_id" value="{{ $car->id }}">
            <input type="hidden" name="total_price" id="total_price_hidden" value="{{ $car->price_per_hour * 24 }}">

            <!-- Left: Form Input -->
            <div class="flex-1 bg-white rounded-3xl p-8 border border-gray-100 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900 mb-6 border-b border-slate-100 pb-4">Data Diri & Pengiriman</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap</label>
                        <input type="text" value="{{ auth()->user()->name ?? 'Guest User (Simulasi)' }}" readonly class="w-full px-4 py-3 bg-slate-100 border-transparent rounded-xl text-gray-500 cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nomor Telepon</label>
                        <input type="tel" name="phone" value="{{ auth()->user()->phone ?? '' }}" required placeholder="0812xxxxxx" class="w-full px-4 py-3 bg-white border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nomor KTP</label>
                    <input type="text" name="ktp" required placeholder="16 digit NIK" class="w-full px-4 py-3 bg-white border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Alamat Lengkap</label>
                    <textarea name="address" required rows="3" placeholder="Jalan, RT/RW, Kelurahan, Kecamatan..." class="w-full px-4 py-3 bg-white border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition-all"></textarea>
                </div>

                <h2 class="text-lg font-bold text-gray-900 mb-6 border-b border-slate-100 pb-4 mt-10">Rincian Sewa</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Periode Sewa</label>
                        <select name="duration" id="duration" class="w-full px-4 py-3 bg-white border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition-all cursor-pointer">
                            <option value="1">1 Hari (24 Jam)</option>
                            <option value="2">2 Hari (48 Jam)</option>
                            <option value="3">3 Hari (72 Jam)</option>
                            <option value="7">1 Minggu (Promo)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Harga Otomatis</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 font-bold text-gray-500">Rp</span>
                            <input type="text" id="total_price_display" readonly value="{{ number_format($car->price_per_hour * 24, 0, ',', '.') }}" class="w-full pl-12 pr-4 py-3 bg-blue-50 border-blue-200 border rounded-xl text-blue-700 font-bold text-lg outline-none cursor-not-allowed">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Unit Summary -->
            <div class="lg:w-[450px] flex-shrink-0">
                <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm sticky top-24 flex flex-col items-center">
                    
                    <h3 class="text-lg font-bold text-gray-900 mb-6 w-full text-left">Ringkasan Unit</h3>

                    <!-- Image Box -->
                    <div class="w-full aspect-[4/3] bg-slate-50 rounded-2xl mb-6 p-4 flex items-center justify-center relative overflow-hidden border border-slate-100">
                        @if($car->image)
                            <img src="{{ asset('storage/' . $car->image) }}" alt="{{ $car->brand }}" class="w-full h-full object-contain">
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-48 h-48 text-slate-200" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19 8l-1.63-3.67C17.06 3.51 16.27 3 15.36 3H8.64c-.9 0-1.7.51-2.01 1.33L5 8H3c-1.1 0-2 .9-2 2v7h2v2c0 1.1.9 2 2 2h1c1.1 0 2-.9 2-2v-2h8v2c0 1.1.9 2 2 2h1c1.1 0 2-.9 2-2v-2h2v-7c0-1.1-.9-2-2-2h-2zM6.5 15C5.67 15 5 14.33 5 13.5S5.67 12 6.5 12 8 12.67 8 13.5 7.33 15 6.5 15zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM8.33 8l1.33-3h4.68l1.33 3H8.33z"/>
                            </svg>
                        @endif
                    </div>

                    <!-- Specs -->
                    <h2 class="text-2xl font-extrabold text-gray-900 mb-4">{{ $car->brand ?? 'Innova Reborn' }}</h2>
                    
                    <div class="w-full space-y-3 mb-8">
                        <div class="flex justify-between items-center py-2 border-b border-slate-50">
                            <span class="text-slate-500 font-medium">Plat Nomor</span>
                            <span class="font-bold text-gray-900 bg-slate-100 px-3 py-1 rounded">{{ $car->plate_number ?? 'AD 1945 WW' }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-slate-50">
                            <span class="text-slate-500 font-medium">Transmisi</span>
                            <span class="font-bold text-gray-900">{{ $car->transmission ?? 'Manual' }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-slate-50">
                            <span class="text-slate-500 font-medium">Kapasitas</span>
                            <span class="font-bold text-gray-900">7 Seat ({{ $car->size ?? 'Large' }})</span>
                        </div>
                    </div>

                    <!-- Checkout Button -->
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-lg py-4 rounded-xl shadow-xl shadow-blue-600/30 transition-all hover:-translate-y-1">
                        Bayar Sekarang
                    </button>
                    <p class="text-xs text-center text-gray-400 mt-4 flex items-center justify-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Pembayaran Aman Terenkripsi
                    </p>

                </div>
            </div>
        </form>
    </main>

    <!-- Script for Dynamic Price -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const durationSelect = document.getElementById('duration');
            const priceDisplay = document.getElementById('total_price_display');
            const hiddenPrice = document.getElementById('total_price_hidden');
            const basePrice = {{ $car->price_per_hour * 24 }}; // Harga 24 jam
            
            durationSelect.addEventListener('change', function() {
                let days = parseInt(this.value);
                let total = basePrice * days;
                
                // Promo 1 Minggu
                if (days === 7) {
                    total = total * 0.85; // Diskon 15%
                }

                hiddenPrice.value = total;
                priceDisplay.value = new Intl.NumberFormat('id-ID').format(total);
            });
        });
    </script>
</body>
</html>
