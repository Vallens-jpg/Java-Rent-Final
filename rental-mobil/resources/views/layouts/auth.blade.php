<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Rental Mobil</title>
    <!-- Memuat file CSS & JS menggunakan Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen font-sans antialiased text-gray-900">
    
    <div class="w-full max-w-md p-6">
        
        <!-- Logo Header -->
        <div class="flex justify-center mb-8">
            <div class="flex flex-col items-center gap-3">
                <div class="w-32 h-32 relative flex items-center justify-center">
                    <svg viewBox="0 0 512 512" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                        <rect width="512" height="512" rx="120" fill="#F8F9FB"/>
                        <path d="M170 140H285C355 140 398 183 398 253C398 323 355 366 285 366H170V312H275C314 312 338 289 338 253C338 217 314 194 275 194H170V140Z" fill="#0F172A" />
                        <path d="M170 312C190 250 238 215 312 215H350C297 225 259 254 232 312H170Z" fill="#14B8A6" />
                        <!-- Brand Name -->
                        <text x="256" y="445" text-anchor="middle" font-family="Poppins, Arial, sans-serif" font-size="42" letter-spacing="10" fill="#0F172A">DRIVORA</text>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Konten Form -->
        @yield('content')
        
    </div>

</body>
</html>
