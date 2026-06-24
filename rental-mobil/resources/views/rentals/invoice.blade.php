<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - TRX-{{ str_pad($rental->id, 5, '0', STR_PAD_LEFT) }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white;
            }
        }
    </style>
</head>
<body class="bg-gray-100 p-8 flex justify-center min-h-screen">
    <div class="bg-white w-full max-w-3xl rounded-xl shadow-lg p-10 border border-gray-200">
        <!-- Header -->
        <div class="flex justify-between items-start mb-12">
            <div>
                <h1 class="text-4xl font-black text-blue-600 tracking-tighter">DRIVORA</h1>
                <p class="text-sm text-gray-500 font-medium mt-1">Sistem Penyewaan Kendaraan Modern</p>
                <p class="text-xs text-gray-400 mt-2">Jl. Teknologi No. 99, Jakarta<br>Telp: (021) 1234-5678</p>
            </div>
            <div class="text-right">
                <h2 class="text-3xl font-bold text-gray-800 uppercase tracking-widest">INVOICE</h2>
                <p class="text-lg font-mono font-bold text-blue-600 mt-2">TRX-{{ str_pad($rental->id, 5, '0', STR_PAD_LEFT) }}</p>
                <p class="text-sm text-gray-500 mt-1">Tanggal Cetak: {{ now()->format('d M Y, H:i') }}</p>
                <span class="inline-block mt-3 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-green-100 text-green-700 border border-green-200">LUNAS</span>
            </div>
        </div>

        <!-- Info Grid -->
        <div class="grid grid-cols-2 gap-8 mb-12 border-y border-gray-200 py-8">
            <!-- Customer -->
            <div>
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Informasi Penyewa</h3>
                <p class="font-bold text-gray-900 text-lg">{{ $rental->user->name ?? 'Pelanggan Drivora' }}</p>
                <p class="text-sm text-gray-600 mt-1">No. KTP/NIK: {{ $rental->user->ktp ?? $rental->user->nik ?? '-' }}</p>
                <p class="text-sm text-gray-600">No. HP: {{ $rental->user->phone ?? '-' }}</p>
                <p class="text-sm text-gray-600 mt-2">{{ $rental->user->address ?? '-' }}</p>
            </div>
            <!-- Car -->
            <div>
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Informasi Kendaraan</h3>
                <p class="font-bold text-gray-900 text-lg">{{ $rental->car->brand }}</p>
                <p class="text-sm text-gray-600 mt-1">Plat Nomor: <span class="font-mono bg-gray-100 px-2 py-0.5 rounded">{{ $rental->car->plate_number }}</span></p>
                <p class="text-sm text-gray-600">Transmisi: {{ $rental->car->transmission }}</p>
                <p class="text-sm text-gray-600">Kapasitas: {{ $rental->car->size }}</p>
            </div>
        </div>

        <!-- Table -->
        <div class="mb-12">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b-2 border-gray-800 text-gray-800">
                        <th class="py-3 px-2 text-sm font-bold uppercase tracking-widest">Deskripsi Sewa</th>
                        <th class="py-3 px-2 text-sm font-bold uppercase tracking-widest text-right">Durasi</th>
                        <th class="py-3 px-2 text-sm font-bold uppercase tracking-widest text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    <tr class="border-b border-gray-200">
                        <td class="py-4 px-2">
                            <p class="font-bold text-gray-900">Sewa Mobil {{ $rental->car->brand }}</p>
                            <p class="text-xs text-gray-500 mt-1">Waktu Ambil: {{ \Carbon\Carbon::parse($rental->start_time)->format('d M Y, H:i') }}</p>
                            <p class="text-xs text-gray-500">Waktu Kembali: {{ \Carbon\Carbon::parse($rental->end_time)->format('d M Y, H:i') }}</p>
                        </td>
                        <td class="py-4 px-2 text-right">
                            {{ \Carbon\Carbon::parse($rental->start_time)->startOfDay()->diffInDays(\Carbon\Carbon::parse($rental->end_time)->startOfDay()) == 0 ? 1 : \Carbon\Carbon::parse($rental->start_time)->startOfDay()->diffInDays(\Carbon\Carbon::parse($rental->end_time)->startOfDay()) }} Hari
                        </td>
                        <td class="py-4 px-2 text-right font-bold text-gray-900">
                            Rp {{ number_format($rental->total_price, 0, ',', '.') }}
                        </td>
                    </tr>
                    <!-- Jika ada perpanjangan -->
                    @if($rental->extension_status == 'paid')
                    <tr class="border-b border-gray-200">
                        <td class="py-4 px-2">
                            <p class="font-bold text-gray-900">Biaya Perpanjangan</p>
                        </td>
                        <td class="py-4 px-2 text-right">{{ $rental->extension_days }} Hari</td>
                        <td class="py-4 px-2 text-right font-bold text-gray-900">
                            Rp {{ number_format((($rental->car->price_per_hour ?? 50000) * 24) * $rental->extension_days, 0, ',', '.') }}
                        </td>
                    </tr>
                    @endif
                    <!-- Jika ada denda -->
                    @if($rental->penalty_status == 'paid')
                    <tr class="border-b border-gray-200 bg-red-50/50">
                        <td class="py-4 px-2">
                            <p class="font-bold text-red-700">Denda Keterlambatan</p>
                        </td>
                        <td class="py-4 px-2 text-right text-red-600">-</td>
                        <td class="py-4 px-2 text-right font-bold text-red-700">Rp 100.000</td>
                    </tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" class="py-6 px-2 text-right font-bold text-gray-600 uppercase tracking-widest">Total Pembayaran</td>
                        <td class="py-6 px-2 text-right text-2xl font-black text-blue-600">
                            @php
                                $finalTotal = $rental->total_price;
                                if($rental->extension_status == 'paid') {
                                    $finalTotal += ((($rental->car->price_per_hour ?? 50000) * 24) * $rental->extension_days);
                                }
                                if($rental->penalty_status == 'paid') {
                                    $finalTotal += 100000;
                                }
                            @endphp
                            Rp {{ number_format($finalTotal, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Footer -->
        <div class="text-center text-sm text-gray-500 mt-16">
            <p>Terima kasih telah mempercayakan perjalanan Anda bersama Drivora.</p>
            <p>Invoice ini adalah bukti pembayaran yang sah dan diterbitkan oleh sistem secara otomatis.</p>
        </div>

        <!-- Action Buttons (No Print) -->
        <div class="mt-10 flex justify-center gap-4 no-print border-t border-gray-200 pt-8">
            <button onclick="window.history.back()" class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-bold transition-colors">
                Kembali
            </button>
            <button onclick="window.print()" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold transition-colors flex items-center gap-2 shadow-lg shadow-blue-600/30">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Cetak Invoice
            </button>
        </div>
    </div>
</body>
</html>
