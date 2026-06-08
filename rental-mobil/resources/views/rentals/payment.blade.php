<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - Rental Mobil</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 font-sans antialiased text-gray-900 min-h-screen">

    <!-- Top Bar -->
    <div class="bg-white shadow-sm border-b border-gray-100 py-4 px-8 flex items-center justify-between sticky top-0 z-50">
        <div class="flex items-center gap-4">
            <h1 class="text-xl font-bold text-gray-800">Pembayaran</h1>
        </div>
        <div>
            <span class="bg-orange-100 text-orange-700 text-xs font-bold px-3 py-1.5 rounded-lg uppercase tracking-wide">Menunggu Pembayaran</span>
        </div>
    </div>

    <main class="max-w-3xl mx-auto px-6 py-12">
        <div class="bg-white rounded-[2rem] p-10 border border-gray-100 shadow-sm flex flex-col items-center text-center">
            
            <h2 class="text-4xl font-extrabold text-gray-900 mb-2">QRIS</h2>
            <p class="text-gray-500 mb-8">Scan barcode di bawah ini menggunakan aplikasi M-Banking atau e-Wallet Anda.</p>

            <!-- QRIS Image Box -->
            <div class="w-64 h-64 bg-slate-50 rounded-3xl border-2 border-dashed border-gray-200 flex items-center justify-center mb-8 relative group overflow-hidden p-4 shadow-inner">
                <!-- Mockup QR Image / SVG -->
                <div class="w-full h-full bg-white rounded-2xl shadow-sm flex items-center justify-center p-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-full h-full text-slate-800" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3 3h8v8H3zm2 2v4h4V5zM13 3h8v8h-8zm2 2v4h4V5zM3 13h8v8H3zm2 2v4h4v-4zm13-2h-2v2h2v-2zm-2 2h-2v2h2v-2zm2 2h-2v2h2v-2zm2-2h-2v2h2v-2zm-4 4h-2v2h2v-2zm2 2h-2v2h2v-2zm-6-8h2v2h-2v-2zm0 4h2v2h-2v-2zm0 4h2v2h-2v-2z"/>
                    </svg>
                </div>
            </div>

            <!-- Total Amount -->
            <div class="mb-8">
                <p class="text-sm text-gray-400 font-bold uppercase tracking-wider mb-1">Total Tagihan</p>
                <p class="text-blue-600 font-extrabold text-4xl">Rp {{ number_format($rental->total_price ?? 500000, 0, ',', '.') }}</p>
            </div>

            <!-- Bank Details -->
            <div class="bg-slate-50 rounded-2xl p-6 w-full max-w-md mb-10 border border-slate-100 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-blue-100 rounded-bl-full opacity-50"></div>
                <p class="text-sm text-gray-500 mb-2 font-medium">Atau transfer manual ke rekening:</p>
                <p class="text-3xl font-black text-gray-900 mb-1 tracking-widest relative z-10">1234 5678 90</p>
                <p class="text-gray-600 font-medium relative z-10">a.n. Budiono Siregar</p>
                <p class="text-blue-600 font-bold text-sm mt-1 relative z-10">Bank Jahat</p>
            </div>

            <!-- Buttons -->
            <div class="w-full max-w-md flex flex-col gap-4">
                <form action="{{ route('rentals.confirm', $rental->id ?? 999) }}" method="POST" class="w-full">
                    @csrf
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-lg py-4 rounded-xl shadow-lg shadow-blue-600/30 transition-all hover:-translate-y-1">
                        Konfirmasi Pembayaran
                    </button>
                </form>

                <a href="https://wa.me/6281234567890" target="_blank" class="w-full bg-white hover:bg-green-50 text-green-600 border-2 border-green-500 font-extrabold py-3.5 rounded-xl transition-all flex justify-center items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
                    </svg>
                    <span>Hubungi Admin Bantuan</span>
                </a>
            </div>

        </div>
    </main>
</body>
</html>
