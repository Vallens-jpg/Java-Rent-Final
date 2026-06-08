@extends('layouts.app')

@section('title', 'Perpanjang Sewa')

@section('content')

<main class="max-w-4xl mx-auto px-8 py-8 w-full">
    
    <div class="bg-white rounded-[2.5rem] shadow-xl shadow-blue-900/5 border border-gray-100 overflow-hidden">
        
        <!-- Header Section Card -->
        <div class="bg-slate-50 px-10 py-6 border-b border-gray-100 flex items-center gap-4">
            <a href="{{ route('dashboard') }}" class="w-10 h-10 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-blue-50 hover:text-blue-600 transition-colors shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h2 class="text-xl font-extrabold text-gray-900">Perpanjangan Waktu Sewa</h2>
                <p class="text-sm font-medium text-gray-500">TRX-9823471 &bull; {{ $rental->car->brand ?? 'Innova Reborn' }}</p>
            </div>
        </div>

        <div class="p-10 lg:p-14 bg-white relative overflow-hidden">
            <!-- Decorative Background blob -->
            <div class="absolute -top-32 -right-32 w-64 h-64 bg-blue-50 rounded-full mix-blend-multiply filter blur-3xl opacity-60"></div>
            
            <form action="{{ route('rentals.submit_extend', $rental->id) }}" method="POST" class="w-full max-w-sm mx-auto flex flex-col gap-8 relative z-10">
                @csrf
                
                <!-- Input: Tambah Waktu -->
                <div>
                    <label class="block text-sm font-extrabold text-gray-700 mb-3 uppercase tracking-wider">Tambah Waktu Sewa</label>
                    <div class="relative group">
                        <select id="extend_days" name="extend_days" class="w-full pl-5 pr-12 py-5 bg-slate-50 border-2 border-slate-200 rounded-2xl text-xl font-bold text-gray-900 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all appearance-none cursor-pointer">
                            <option value="1">1 Hari</option>
                            <option value="2" selected>2 Hari</option>
                            <option value="3">3 Hari</option>
                            <option value="4">4 Hari</option>
                            <option value="7">1 Minggu</option>
                        </select>
                        <div class="absolute inset-y-0 right-5 flex items-center pointer-events-none group-hover:text-blue-500 transition-colors">
                            <svg class="w-6 h-6 text-gray-400 group-hover:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                    
                    <!-- Durasi Text (48:00:00) -->
                    <div class="mt-4 flex items-center gap-3 bg-blue-50 text-blue-700 px-5 py-3 rounded-xl border border-blue-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-sm font-bold uppercase tracking-wider">Tambahan Durasi</span>
                        <span id="extend_duration" class="text-xl font-black tabular-nums tracking-tight ml-auto">48:00:00</span>
                    </div>
                </div>

                <!-- Divider -->
                <div class="border-t-2 border-dashed border-gray-100 my-2"></div>

                <!-- Input: Biaya -->
                <div>
                    <label class="block text-sm font-extrabold text-gray-700 mb-3 uppercase tracking-wider text-center">Estimasi Biaya Tambahan</label>
                    
                    <!-- Nominal Output -->
                    <div class="bg-gradient-to-br from-slate-50 to-slate-100 border-2 border-slate-200 px-6 py-8 rounded-[2rem] text-center shadow-inner">
                        <p id="extend_cost" class="text-4xl font-black text-gray-900 tracking-tight">Rp.100.000,00</p>
                    </div>
                </div>

                <!-- Order Button -->
                <button type="submit" class="w-full mt-4 bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xl py-5 rounded-2xl shadow-xl shadow-blue-600/30 transition-all duration-300 hover:-translate-y-1 flex justify-center items-center gap-3 group">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 group-hover:rotate-12 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span>Order Perpanjangan</span>
                </button>
                
            </form>
        </div>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectDays = document.getElementById('extend_days');
        const textDuration = document.getElementById('extend_duration');
        const textCost = document.getElementById('extend_cost');
        
        // Harga dari database
        const pricePerDay = {{ ($rental->car->price_per_hour ?? 50000) * 24 }};

        function updateForm() {
            const days = parseInt(selectDays.value);
            
            // Format jam (misal 2 hari = 48:00:00)
            const hours = days * 24;
            textDuration.innerText = hours + ":00:00";
            
            // Format biaya
            const cost = days * pricePerDay;
            textCost.innerText = "Rp." + new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2 }).format(cost);
        }

        selectDays.addEventListener('change', updateForm);
        
        // Panggil saat pertama dimuat
        updateForm();
    });
</script>
@endsection
