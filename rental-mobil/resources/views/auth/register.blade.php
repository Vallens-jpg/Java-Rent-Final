@extends('layouts.auth')

@section('title', 'Sign In / Register')

@section('content')
<div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
    <div class="p-8">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-2">Buat Akun Baru</h2>
        <p class="text-center text-sm text-gray-500 mb-6">Lengkapi data di bawah untuk mendaftar</p>

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

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf
            
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus placeholder="John Doe"
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-gray-50 focus:bg-white outline-none">
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required placeholder="nama@email.com"
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-gray-50 focus:bg-white outline-none">
            </div>

            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">No Telp</label>
                <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" required placeholder="08123456789"
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-gray-50 focus:bg-white outline-none">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" id="password" required placeholder="••••••••"
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-gray-50 focus:bg-white outline-none">
            </div>

            <button type="submit" 
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-xl shadow-md hover:shadow-lg transition-all duration-200 mt-6">
                Sign in
            </button>
        </form>
    </div>
    
    <div class="bg-gray-50 px-8 py-5 border-t border-gray-100 text-center">
        <p class="text-sm text-gray-600">Sudah punya akun? 
            <a href="{{ route('login') }}" class="font-semibold text-blue-600 hover:text-blue-800 hover:underline transition-colors">Masuk di sini</a>
        </p>
    </div>
</div>
@endsection
