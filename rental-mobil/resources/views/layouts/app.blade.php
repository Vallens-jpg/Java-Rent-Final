<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Rental Mobil</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 font-sans antialiased text-gray-900 min-h-screen flex flex-col">
    
    <!-- Header Section (Global) -->
    <header class="bg-white shadow-sm border-b border-gray-100 py-6 px-8 relative z-10">
        <div class="max-w-7xl mx-auto flex items-start justify-between">
            
            <!-- Left: LOGO -->
            <a href="{{ route('cars.index') }}" class="flex-shrink-0 flex items-center gap-3 cursor-pointer hover:opacity-80 transition group">
                <div class="w-20 h-20 relative flex items-center justify-center">
                    <svg viewBox="0 0 512 512" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                        <rect width="512" height="512" rx="120" fill="#F8F9FB" class="group-hover:fill-slate-100 transition-colors"/>
                        <path d="M170 140H285C355 140 398 183 398 253C398 323 355 366 285 366H170V312H275C314 312 338 289 338 253C338 217 314 194 275 194H170V140Z" fill="#0F172A" />
                        <path d="M170 312C190 250 238 215 312 215H350C297 225 259 254 232 312H170Z" fill="#14B8A6" />
                        <!-- Brand Name -->
                        <text x="256" y="445" text-anchor="middle" font-family="Poppins, Arial, sans-serif" font-size="42" letter-spacing="10" fill="#0F172A">DRIVORA</text>
                    </svg>
                </div>
            </a>

            <!-- Center: Greeting & Search -->
            <div class="flex-1 px-12 flex flex-col items-center">
                <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-gray-900 mb-6 uppercase">
                    HELLO, {{ strtoupper(auth()->user()->name ?? 'GUEST') }}
                </h1>
                
                <form action="{{ route('cars.index') }}" method="GET" class="w-full max-w-2xl relative group mb-6">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Masukkan Merk Mobil..." 
                        class="w-full pl-12 pr-4 py-4 bg-gray-100 border border-transparent rounded-2xl text-gray-900 placeholder-gray-500 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 transition-all shadow-inner outline-none text-sm font-medium">
                </form>

                @auth
                <!-- Global Navigation Bar (Only for Logged In Users) -->
                <nav class="flex items-center gap-2 bg-slate-100 p-1.5 rounded-2xl border border-slate-200 w-full max-w-2xl shadow-inner">
                    <a href="{{ route('cars.index') }}" class="{{ request()->routeIs('cars.*') ? 'bg-white shadow-sm text-blue-600' : 'text-gray-500 hover:text-gray-700 hover:bg-white/50' }} flex-1 text-center py-2.5 rounded-xl font-bold text-sm transition-all flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        Katalog
                    </a>
                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') || request()->routeIs('rentals.*') ? 'bg-white shadow-sm text-blue-600' : 'text-gray-500 hover:text-gray-700 hover:bg-white/50' }} flex-1 text-center py-2.5 rounded-xl font-bold text-sm transition-all flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                        Informasi Pesanan
                    </a>
                    
                    @php
                        $notifications = [];
                        $notifCount = 0;
                        if (auth()->check()) {
                            $notifications = \App\Models\Rental::with('car')->where('user_id', auth()->id())
                                ->where('notification_dismissed', false)
                                ->whereIn('status', ['rejected', 'active', 'pending'])
                                ->orderBy('updated_at', 'desc')
                                ->get();
                            $notifCount = $notifications->count();
                        }
                    @endphp
                    <div class="relative group cursor-pointer flex-1 flex">
                        <div class="w-full flex items-center justify-center gap-2 py-2.5 rounded-xl font-bold text-sm transition-all {{ $notifCount > 0 ? 'text-orange-500' : 'text-gray-500 hover:text-gray-700 hover:bg-white/50' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            Notifikasi
                            @if($notifCount > 0)
                            <span class="bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded-full">{{ $notifCount }}</span>
                            @endif
                        </div>

                        <!-- Dropdown Notifikasi -->
                        <div class="absolute left-1/2 -translate-x-1/2 top-full pt-2 w-80 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50">
                            <div class="bg-white border border-gray-100 rounded-2xl shadow-xl overflow-hidden transform origin-top scale-95 group-hover:scale-100">
                                <div class="bg-slate-50 px-4 py-3 border-b border-gray-100 text-left">
                                <h3 class="font-bold text-gray-800 text-sm">Notifikasi Anda</h3>
                            </div>
                            <div class="max-h-64 overflow-y-auto text-left">
                                @forelse($notifications as $notif)
                                <div class="p-4 border-b border-gray-50 hover:bg-slate-50 transition-colors relative" id="notif-{{ $notif->id }}">
                                    <!-- Dismiss Button -->
                                    <button onclick="dismissNotification({{ $notif->id }})" class="absolute top-4 right-4 text-gray-300 hover:text-red-500 transition-colors" title="Hapus Notifikasi">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                    <div class="flex items-start gap-3 pr-6">
                                        <div class="p-2 {{ ($notif->extension_status == 'rejected' || $notif->status == 'rejected') ? 'bg-red-100 text-red-600' : (($notif->extension_status == 'pending_verification' || $notif->status == 'pending') ? 'bg-orange-100 text-orange-600' : 'bg-green-100 text-green-600') }} rounded-full flex-shrink-0">
                                            @if($notif->extension_status == 'rejected' || $notif->status == 'rejected')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            @elseif($notif->extension_status == 'pending_verification' || $notif->status == 'pending')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-800">
                                                @if($notif->extension_status == 'rejected')
                                                    Perpanjangan Ditolak
                                                @elseif($notif->extension_status == 'approved')
                                                    Perpanjangan Disetujui
                                                @elseif($notif->extension_status == 'pending_verification')
                                                    Menunggu Verifikasi Perpanjangan
                                                @else
                                                    {{ $notif->status == 'rejected' ? 'Pesanan Ditolak' : ($notif->status == 'pending' ? 'Menunggu Konfirmasi' : 'Pesanan Dikonfirmasi') }}
                                                @endif
                                            </p>
                                            <p class="text-xs text-gray-500 mt-1">Mobil: {{ $notif->car->brand ?? 'Mobil' }}</p>
                                            @if($notif->status == 'rejected' && $notif->rejection_reason && empty($notif->extension_status))
                                            <p class="text-xs text-red-500 mt-1 italic">"{{ $notif->rejection_reason }}"</p>
                                            @endif
                                            <p class="text-[10px] text-gray-400 mt-2">{{ $notif->updated_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="p-6 text-center text-gray-400 text-sm">
                                    Belum ada notifikasi
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </nav>
                @endauth
            </div>

            <!-- Right: Profile Icon -->
            <div class="flex-shrink-0 pt-2">
                @auth
                <div class="relative group cursor-pointer">
                    <!-- Profile Icon -->
                    <div class="w-12 h-12 rounded-full bg-slate-100 border-2 border-white shadow flex items-center justify-center group-hover:bg-slate-200 group-hover:ring-2 group-hover:ring-blue-500 transition-all overflow-hidden">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-slate-600 group-hover:text-blue-600 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>

                    <!-- Popup Logout (Hover Menu) -->
                    <div class="absolute right-0 top-full pt-2 w-48 z-50 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform translate-y-2 group-hover:translate-y-0">
                        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                            <form method="POST" action="{{ route('logout') }}" class="w-full m-0">
                                @csrf
                                <button type="submit" class="w-full px-5 py-4 text-left text-sm font-bold text-red-600 hover:bg-red-50 transition-colors flex items-center gap-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Keluar (Logout)
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @else
                <a href="{{ route('login') }}" class="w-12 h-12 rounded-full bg-slate-100 border-2 border-white shadow flex items-center justify-center hover:bg-slate-200 hover:ring-2 hover:ring-blue-500 transition-all group" title="Login">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-slate-600 group-hover:text-blue-600 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                </a>
                @endauth
            </div>

        </div>
    </header>

    @yield('content')

    <!-- Footer Section -->
    <footer class="bg-white border-t border-gray-100 mt-auto pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
                <!-- Brand Column -->
                <div class="md:col-span-1">
                    <a href="{{ route('cars.index') }}" class="flex items-center gap-2 mb-6 cursor-pointer hover:opacity-80 transition">
                        <div class="w-12 h-12 relative flex items-center justify-center">
                            <svg viewBox="0 0 512 512" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                                <rect width="512" height="512" rx="120" fill="#F8F9FB"/>
                                <path d="M170 140H285C355 140 398 183 398 253C398 323 355 366 285 366H170V312H275C314 312 338 289 338 253C338 217 314 194 275 194H170V140Z" fill="#0F172A" />
                                <path d="M170 312C190 250 238 215 312 215H350C297 225 259 254 232 312H170Z" fill="#14B8A6" />
                            </svg>
                        </div>
                        <span class="text-xl font-black text-slate-900 tracking-widest">DRIVORA</span>
                    </a>
                    <p class="text-sm text-gray-500 leading-relaxed">
                        Platform rental mobil terpercaya untuk perjalanan bisnis dan liburan Anda. Kami menyediakan unit terbaik dengan pelayanan 24/7 di seluruh Indonesia.
                    </p>
                </div>

                <!-- Links Column 1 -->
                <div>
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4">Layanan Kami</h3>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-sm text-gray-500 hover:text-teal-600 transition-colors">Sewa Mobil Harian</a></li>
                        <li><a href="#" class="text-sm text-gray-500 hover:text-teal-600 transition-colors">Sewa Mobil Bulanan</a></li>
                        <li><a href="#" class="text-sm text-gray-500 hover:text-teal-600 transition-colors">Layanan Lepas Kunci</a></li>
                        <li><a href="#" class="text-sm text-gray-500 hover:text-teal-600 transition-colors">Antar Jemput Bandara</a></li>
                    </ul>
                </div>

                <!-- Links Column 2 -->
                <div>
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4">Bantuan</h3>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-sm text-gray-500 hover:text-teal-600 transition-colors">Syarat & Ketentuan</a></li>
                        <li><a href="#" class="text-sm text-gray-500 hover:text-teal-600 transition-colors">Kebijakan Privasi</a></li>
                        <li><a href="#" class="text-sm text-gray-500 hover:text-teal-600 transition-colors">FAQ (Tanya Jawab)</a></li>
                        <li><a href="#" class="text-sm text-gray-500 hover:text-teal-600 transition-colors">Cara Pembayaran</a></li>
                    </ul>
                </div>

                <!-- Contact Column -->
                <div>
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4">Hubungi Kami</h3>
                    <ul class="space-y-3">
                        <li class="flex items-start gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-teal-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="text-sm text-gray-500 leading-relaxed">Jl. Jend. Sudirman No.Kav 21, Jakarta Selatan, 12920</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <span class="text-sm text-gray-500">+62 812-3456-7890</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span class="text-sm text-gray-500">cs@drivora.com</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Copyright -->
            <div class="border-t border-gray-100 pt-8 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-xs text-gray-400 font-medium">
                    &copy; {{ date('Y') }} Drivora Car Rental. Hak Cipta Dilindungi.
                </p>
                <div class="flex gap-4">
                    <a href="#" class="w-8 h-8 bg-slate-50 text-slate-400 hover:text-teal-600 hover:bg-teal-50 rounded-full flex items-center justify-center transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd"></path></svg>
                    </a>
                    <a href="#" class="w-8 h-8 bg-slate-50 text-slate-400 hover:text-teal-600 hover:bg-teal-50 rounded-full flex items-center justify-center transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd"></path></svg>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        function dismissNotification(id) {
            fetch(`/rentals/${id}/dismiss-notification`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(res => res.json()).then(data => {
                if (data.success) {
                    const notifEl = document.getElementById(`notif-${id}`);
                    if (notifEl) {
                        notifEl.remove();
                        // reload to update count
                        window.location.reload();
                    }
                }
            }).catch(err => console.error(err));
        }
    </script>
</body>
</html>
