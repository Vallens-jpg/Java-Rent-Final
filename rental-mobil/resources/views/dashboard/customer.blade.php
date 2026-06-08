@extends('layouts.app')

@section('title', 'Informasi Sewa')

@section('content')

<main class="max-w-5xl mx-auto px-8 py-8 w-full">
    
    @if($rental)
    <div class="bg-white rounded-[2.5rem] shadow-xl shadow-blue-900/5 border border-gray-100 overflow-hidden">
        
        <!-- Top Status Bar -->
        <div class="px-8 py-4 flex items-center justify-between text-white {{ $rental->status == 'pending' ? 'bg-orange-500' : ($rental->status == 'rejected' ? 'bg-red-500' : 'bg-blue-600') }}" id="statusBar">
            <div class="flex items-center gap-3">
                @if($rental->status == 'pending' || $rental->status == 'active')
                <div class="w-2 h-2 bg-white rounded-full animate-pulse shadow-[0_0_8px_rgba(255,255,255,0.8)]"></div>
                @endif
                <span class="font-bold text-sm tracking-widest uppercase">
                    Status: {{ $rental->status == 'pending' ? 'Menunggu Konfirmasi' : ($rental->status == 'rejected' ? 'Pesanan Ditolak' : 'Sedang Disewa') }}
                </span>
            </div>
            <span class="font-bold text-xs bg-white/20 px-3 py-1.5 rounded-lg backdrop-blur-sm tracking-wider">ID: TRX-{{ str_pad($rental->id, 5, '0', STR_PAD_LEFT) }}</span>
        </div>

        <div class="p-8 lg:p-12 flex flex-col lg:flex-row gap-12">
            
            <!-- Left Side: Rental Info & Countdown -->
            <div class="flex-1 lg:border-r border-gray-100 lg:pr-12">
                <div class="flex items-center gap-6 mb-10">
                    <div class="w-24 h-24 bg-slate-50 rounded-2xl flex items-center justify-center border border-slate-100 p-2 shadow-sm overflow-hidden">
                        @if($rental->car->image)
                            <img src="{{ Str::startsWith($rental->car->image, 'http') ? $rental->car->image : asset('storage/' . $rental->car->image) }}" class="w-full h-full object-cover rounded-xl" alt="{{ $rental->car->brand }}">
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-full h-full text-blue-300" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19 8l-1.63-3.67C17.06 3.51 16.27 3 15.36 3H8.64c-.9 0-1.7.51-2.01 1.33L5 8H3c-1.1 0-2 .9-2 2v7h2v2c0 1.1.9 2 2 2h1c1.1 0 2-.9 2-2v-2h8v2c0 1.1.9 2 2 2h1c1.1 0 2-.9 2-2v-2h2v-7c0-1.1-.9-2-2-2h-2z"/>
                            </svg>
                        @endif
                    </div>
                    <div>
                        <h2 class="text-3xl font-extrabold text-gray-900 mb-2 tracking-tight">{{ $rental->car->brand }}</h2>
                        <div class="flex items-center gap-3 text-xs font-bold text-gray-500 uppercase tracking-wider">
                            <span class="bg-blue-50 text-blue-600 px-2.5 py-1 rounded-md">{{ $rental->car->size }}</span>
                            <span>&bull;</span>
                            <span>{{ $rental->car->transmission }}</span>
                            <span>&bull;</span>
                            <span class="bg-slate-100 px-2.5 py-1 rounded-md text-slate-700">{{ $rental->car->plate_number }}</span>
                        </div>
                    </div>
                </div>

                @if($rental->status == 'active')
                <div>
                    <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mb-3 flex items-center gap-2" id="timerTitle">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Sisa Waktu Sewa
                    </p>
                    <div class="flex items-baseline gap-2">
                        <span class="text-5xl font-black text-gray-900 tabular-nums tracking-tight" id="countdownTimer">Menghitung...</span>
                    </div>
                    
                    <div class="flex justify-between items-center mt-6 text-xs font-bold text-gray-400">
                        <span>Mulai: {{ \Carbon\Carbon::parse($rental->start_time)->format('d M Y, H:i') }}</span>
                        <span class="text-blue-600">Batas: {{ \Carbon\Carbon::parse($rental->end_time)->format('d M Y, H:i') }}</span>
                    </div>
                </div>
                @elseif($rental->status == 'rejected')
                <div class="bg-red-50 p-6 rounded-2xl border border-red-100">
                    <p class="text-red-700 font-bold mb-2 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Pesanan Ditolak Admin
                    </p>
                    <p class="text-red-600 text-sm">Pesanan Anda tidak dapat diproses saat ini.</p>
                    @if($rental->rejection_reason)
                    <div class="mt-4 p-4 bg-white/60 rounded-xl border border-red-100">
                        <p class="text-xs text-red-500 font-bold uppercase tracking-wider mb-1">Alasan Penolakan:</p>
                        <p class="text-sm text-red-800 italic">"{{ $rental->rejection_reason }}"</p>
                    </div>
                    @endif
                </div>
                @else
                <div class="bg-orange-50 p-6 rounded-2xl border border-orange-100">
                    <p class="text-orange-700 font-bold mb-2">Menunggu Persetujuan Admin</p>
                    <p class="text-orange-600 text-sm">Pesanan Anda telah diterima dan sedang menunggu konfirmasi dari pihak Drivora. Silakan tunggu atau hubungi admin.</p>
                </div>
                @endif
            </div>

            <!-- Right Side: Penalty & Late Info (Only show if active and overdue) -->
            @if($rental->status == 'active')
            <div class="lg:w-[320px] flex-shrink-0 flex flex-col justify-center hidden" id="penaltySection">
                <div class="bg-red-50 rounded-[2rem] p-8 border border-red-100 text-center relative overflow-hidden shadow-inner shadow-red-900/5">
                    <div class="relative z-10">
                        <div class="w-12 h-12 bg-red-100 rounded-2xl flex items-center justify-center text-red-600 mx-auto mb-5 shadow-sm border border-red-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        
                        <p class="text-xs text-red-500 font-extrabold uppercase tracking-widest mb-2">Waktu Keterlambatan</p>
                        <p class="text-3xl font-black text-red-600 mb-6 tabular-nums tracking-tight" id="overdueTimer">00:00:00</p>
                        
                        <div class="border-t border-red-200/60 pt-6">
                            <p class="text-[10px] text-red-400 font-black uppercase tracking-widest mb-1">Denda Keterlambatan (Flat)</p>
                            <p class="text-3xl font-black text-red-700 tracking-tight">Rp 100.000</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        @if($rental->status == 'active')
        <!-- Bottom Action Buttons -->
        <div class="p-8 lg:px-12 lg:py-8 bg-slate-50 border-t border-gray-100 flex flex-col sm:flex-row gap-4">
            <button id="extendOrPenaltyBtn" onclick="window.location.href = '/rentals/{{ $rental->id }}/extend'" class="flex-1 bg-white hover:bg-slate-100 text-gray-700 font-bold py-4 px-6 rounded-2xl border border-gray-200 shadow-sm transition-all duration-300 text-base flex items-center justify-center gap-2 group">
                Ajukan Perpanjangan
            </button>
            <button onclick="alert('Silakan kembalikan mobil ke garasi Drivora dan konfirmasi kepada Admin yang bertugas.')" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-extrabold py-4 px-6 rounded-2xl shadow-xl shadow-blue-600/20 transition-all duration-300 hover:-translate-y-1 text-base flex items-center justify-center gap-2 group">
                Instruksi Pengembalian
            </button>
        </div>
        @endif
    </div>

    @if($rental->status == 'active')
    <script>
        // Tanggal batas waktu dari server
        const endTimeStr = "{{ $rental->end_time }}";
        // Convert ke standard ISO format (mengganti spasi dengan T jika diperlukan)
        const endTime = new Date(endTimeStr.replace(' ', 'T')).getTime();
        const penaltyStatus = "{{ $rental->penalty_status }}";
        const extensionStatus = "{{ $rental->extension_status }}";
        const rentalId = {{ $rental->id }};

        const timerEl = document.getElementById('countdownTimer');
        const overdueTimerEl = document.getElementById('overdueTimer');
        const penaltySection = document.getElementById('penaltySection');
        const timerTitle = document.getElementById('timerTitle');
        const statusBar = document.getElementById('statusBar');
        const extendOrPenaltyBtn = document.getElementById('extendOrPenaltyBtn');

        function formatTime(ms) {
            let totalSeconds = Math.floor(ms / 1000);
            let hours = Math.floor(totalSeconds / 3600);
            let minutes = Math.floor((totalSeconds % 3600) / 60);
            let seconds = Math.floor(totalSeconds % 60);
            return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        }

        function updateTimer() {
            const now = new Date().getTime();
            const difference = endTime - now;

            if (difference > 0) {
                // Masih ada waktu sewa
                timerEl.innerText = formatTime(difference);
                if (extendOrPenaltyBtn) {
                    if (extensionStatus === 'pending_verification') {
                        if (extendOrPenaltyBtn.innerText !== "Verifikasi Perpanjangan Diproses") {
                            extendOrPenaltyBtn.innerHTML = "Verifikasi Perpanjangan Diproses";
                            extendOrPenaltyBtn.onclick = null;
                            extendOrPenaltyBtn.className = "flex-1 bg-yellow-500 text-white font-extrabold py-4 px-6 rounded-2xl shadow-xl shadow-yellow-500/20 text-base flex items-center justify-center gap-2 cursor-not-allowed opacity-80";
                        }
                    } else if (extensionStatus === 'unpaid') {
                        if (extendOrPenaltyBtn.innerText !== "Lanjutkan Pembayaran Perpanjangan") {
                            extendOrPenaltyBtn.innerHTML = "Lanjutkan Pembayaran Perpanjangan";
                            extendOrPenaltyBtn.onclick = () => window.location.href = `/rentals/${rentalId}/extend`;
                            extendOrPenaltyBtn.className = "flex-1 bg-orange-500 text-white font-extrabold py-4 px-6 rounded-2xl shadow-xl shadow-orange-500/20 text-base flex items-center justify-center gap-2";
                        }
                    } else {
                        if (extendOrPenaltyBtn.innerText !== "Ajukan Perpanjangan") {
                            extendOrPenaltyBtn.innerHTML = "Ajukan Perpanjangan";
                            extendOrPenaltyBtn.onclick = () => window.location.href = `/rentals/${rentalId}/extend`;
                            extendOrPenaltyBtn.className = "flex-1 bg-white hover:bg-slate-100 text-gray-700 font-bold py-4 px-6 rounded-2xl border border-gray-200 shadow-sm transition-all duration-300 text-base flex items-center justify-center gap-2 group";
                        }
                    }
                }
            } else {
                // Telat / Overdue
                let overdueMs = Math.abs(difference);
                
                // Ubah tampilan menjadi peringatan merah
                timerEl.innerText = "WAKTU HABIS";
                timerEl.classList.remove('text-gray-900');
                timerEl.classList.add('text-red-600');
                
                timerTitle.innerHTML = "STATUS KETERLAMBATAN";
                timerTitle.classList.remove('text-gray-400');
                timerTitle.classList.add('text-red-500');

                statusBar.classList.remove('bg-blue-600');
                statusBar.classList.add('bg-red-600');
                statusBar.querySelector('span').innerText = 'STATUS: TERLAMBAT PENGEMBALIAN';

                // Tampilkan box denda
                penaltySection.classList.remove('hidden');
                overdueTimerEl.innerText = formatTime(overdueMs);

                // Ubah tombol Ajukan Perpanjangan menjadi Bayar Denda atau Status Denda
                if (extendOrPenaltyBtn) {
                    if (penaltyStatus === 'pending_verification') {
                        if (extendOrPenaltyBtn.innerText !== "Verifikasi Denda Diproses") {
                            extendOrPenaltyBtn.innerHTML = "Verifikasi Denda Diproses";
                            extendOrPenaltyBtn.onclick = null;
                            extendOrPenaltyBtn.className = "flex-1 bg-yellow-500 text-white font-extrabold py-4 px-6 rounded-2xl shadow-xl shadow-yellow-500/20 text-base flex items-center justify-center gap-2 cursor-not-allowed opacity-80";
                        }
                    } else if (penaltyStatus === 'paid') {
                        if (extendOrPenaltyBtn.innerText !== "Denda Lunas") {
                            extendOrPenaltyBtn.innerHTML = "Denda Lunas";
                            extendOrPenaltyBtn.onclick = null;
                            extendOrPenaltyBtn.className = "flex-1 bg-green-500 text-white font-extrabold py-4 px-6 rounded-2xl shadow-xl shadow-green-500/20 text-base flex items-center justify-center gap-2 cursor-not-allowed";
                        }
                    } else {
                        if (extendOrPenaltyBtn.innerText !== "Bayar Denda (Rp 100.000)") {
                            extendOrPenaltyBtn.innerHTML = "Bayar Denda (Rp 100.000)";
                            extendOrPenaltyBtn.onclick = () => document.getElementById('penaltyModal').classList.remove('hidden');
                            extendOrPenaltyBtn.className = "flex-1 bg-red-600 hover:bg-red-700 text-white font-extrabold py-4 px-6 rounded-2xl shadow-xl shadow-red-600/20 transition-all duration-300 hover:-translate-y-1 text-base flex items-center justify-center gap-2 group";
                        }
                    }
                }
            }
        }

        // Update setiap 1 detik
        setInterval(updateTimer, 1000);
        updateTimer(); // panggil sekali saat load
        
        // Pengecekan otomatis ke backend jika status berubah (terutama jika admin sudah mengonfirmasi pengembalian)
        function checkStatus() {
            fetch(`/rentals/${rentalId}/api-status`)
                .then(res => res.json())
                .then(data => {
                    // Jika rental menjadi completed, atau ditolak, atau status denda/perpanjangan berubah dari backend
                    if (data.status === 'completed' || data.status === 'not_found' || data.penalty_status !== penaltyStatus || data.extension_status !== extensionStatus) {
                        window.location.reload();
                    }
                })
                .catch(err => console.error("Error checking status:", err));
        }
        // Cek status setiap 3 detik
        setInterval(checkStatus, 3000);
        
        function submitPenalty() {
            fetch(`/rentals/${rentalId}/pay-penalty`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(res => res.json()).then(data => {
                if(data.success) {
                    window.location.reload();
                }
            }).catch(err => alert("Gagal mengonfirmasi pembayaran."));
        }
    </script>

    <!-- Penalty Payment Modal -->
    <div id="penaltyModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-[2rem] w-full max-w-md p-8 shadow-2xl relative">
            <button onclick="document.getElementById('penaltyModal').classList.add('hidden')" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
            <h2 class="text-2xl font-black text-gray-900 mb-4 text-center">Pembayaran Denda</h2>
            <p class="text-sm text-gray-500 text-center mb-6">Silakan scan QRIS di bawah ini atau transfer ke nomor rekening untuk melunasi denda keterlambatan Anda (Flat Rp 100.000).</p>
            
            <div class="bg-slate-50 p-6 rounded-2xl flex justify-center mb-6 border border-gray-100">
                <!-- Dummy QRIS Placeholder -->
                <div class="w-48 h-48 bg-white border-2 border-dashed border-gray-300 rounded-xl flex items-center justify-center">
                    <span class="text-gray-400 font-bold">QRIS Image</span>
                </div>
            </div>
            
            <div class="text-center mb-8">
                <p class="text-sm font-bold text-gray-700">Bank BCA: 1234567890</p>
                <p class="text-xs text-gray-500">a.n. Drivora Car Rental</p>
            </div>
            
            <div class="flex flex-col gap-3">
                <button onclick="submitPenalty()" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-xl transition-colors">
                    Saya Sudah Transfer
                </button>
                <a href="https://wa.me/6281234567890" target="_blank" class="w-full bg-slate-100 hover:bg-slate-200 text-gray-700 font-bold py-3 px-6 rounded-xl transition-colors text-center flex items-center justify-center gap-2">
                    Hubungi Admin
                </a>
            </div>
        </div>
    </div>
    @endif

    @else
    <!-- EMPTY STATE -->
    <div class="bg-white rounded-[2.5rem] shadow-xl shadow-blue-900/5 border border-gray-100 p-12 text-center flex flex-col items-center justify-center min-h-[50vh]">
        <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <h2 class="text-2xl font-black text-gray-900 mb-2">Belum Ada Pesanan Aktif</h2>
        <p class="text-gray-500 mb-8 max-w-md">Anda saat ini tidak memiliki mobil yang sedang disewa atau dalam proses konfirmasi.</p>
        <a href="{{ route('cars.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-xl transition-colors shadow-lg shadow-blue-600/30">
            Sewa Mobil Sekarang
        </a>
    </div>
    @endif

</main>
@endsection
