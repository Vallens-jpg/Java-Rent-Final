@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
    <div class="p-8">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-2">Selamat Datang Kembali</h2>
        <p class="text-center text-sm text-gray-500 mb-6">Silakan masuk ke akun Anda</p>

        {{-- Flash Message: Sukses (Misal setelah register) --}}
        @if(session('success'))
            <div class="mb-5 p-4 text-sm text-green-700 bg-green-100/80 rounded-lg border border-green-200">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        {{-- Error Validation --}}
        @if($errors->any())
            <div class="mb-5 p-4 text-sm text-red-700 bg-red-100/80 rounded-lg border border-red-200">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf
            
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus placeholder="nama@email.com"
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-gray-50 focus:bg-white outline-none">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" id="password" required placeholder="••••••••"
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-gray-50 focus:bg-white outline-none">
            </div>

            <button type="submit" 
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-xl shadow-md hover:shadow-lg transition-all duration-200 mt-2">
                LOGIN
            </button>
        </form>
    </div>
    
    <div class="bg-gray-50 px-8 py-5 border-t border-gray-100 text-center">
        <p class="text-sm text-gray-600">Belum punya akun? 
            <a href="{{ route('register') }}" class="font-semibold text-blue-600 hover:text-blue-800 hover:underline transition-colors">Daftar sekarang</a>
        </p>
    </div>
</div>
@endsection
