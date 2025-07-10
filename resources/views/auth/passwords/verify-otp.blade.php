<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Kode - SIMANTAP</title>
    @vite('resources/css/app.css')
    {{-- Import Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-primary-50 to-primary-100">
    <div class="min-h-screen flex">
        <!-- Left Side - Branding -->
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-primary-600 via-primary-700 to-primary-800 relative overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-br from-white/20 to-transparent"></div>
                <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-white/10 rounded-full blur-3xl"></div>
                <div class="absolute bottom-1/4 right-1/4 w-80 h-80 bg-white/10 rounded-full blur-3xl"></div>
            </div>
            
            <!-- Content -->
            <div class="relative z-10 flex-1 flex flex-col justify-center items-center p-8">
                <div class="max-w-md w-full text-center text-white">
                    <!-- Logo/Icon -->
                    <div class="mb-8 w-24 h-24 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm mx-auto">
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                    </div>
                    
                    <!-- Title -->
                    <h1 class="text-4xl font-bold mb-4">SIMANTAP</h1>
                    <p class="text-xl mb-8 text-primary-100">Simalungun Administrasi Terpadu</p>
                    
                    <!-- Description -->
                    <div class="space-y-4">
                        <h2 class="text-2xl font-semibold text-white">Verifikasi Keamanan</h2>
                        <p class="text-lg text-primary-100">
                            Kami telah mengirimkan kode verifikasi melalui WhatsApp untuk memastikan keamanan akun Anda
                        </p>
                        
                        <div class="space-y-3 text-primary-200 mt-6">
                            <div class="flex items-center justify-center">
                                <svg class="w-5 h-5 mr-3 text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="text-sm">Kode berlaku 10 menit</span>
                            </div>
                            <div class="flex items-center justify-center">
                                <svg class="w-5 h-5 mr-3 text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                <span class="text-sm">Aman dan terenkripsi</span>
                            </div>
                            <div class="flex items-center justify-center">
                                <svg class="w-5 h-5 mr-3 text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                <span class="text-sm">Proses cepat dan mudah</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - OTP Verification Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8">
            <div class="w-full max-w-md">
                <!-- Mobile Logo -->
                <div class="lg:hidden text-center mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-primary-600 rounded-full mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900">SIMANTAP</h1>
                    <p class="text-sm text-gray-600">Simalungun Administrasi Terpadu</p>
                </div>

                <!-- OTP Verification Card -->
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
                    <!-- Header -->
                    <div class="text-center mb-8">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">Verifikasi Kode</h2>
                        <p class="text-gray-600">Masukkan 6 digit kode yang kami kirimkan ke nomor WhatsApp Anda.</p>
                    </div>

                    @if(session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p>{{ session('success') }}</p>
                            </div>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p>{{ $errors->first() }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Form -->
                    <form method="POST" action="{{ route('password.verify_otp') }}" class="space-y-6">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
                        
                        <!-- OTP Code Field -->
                        <div>
                            <label for="code" class="block text-sm font-semibold text-gray-700 mb-2">
                                Kode Verifikasi
                            </label>
                            <input 
                                id="code" 
                                type="text" 
                                name="code" 
                                required 
                                autofocus
                                class="w-full text-center tracking-[1em] font-mono text-2xl py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200"
                                placeholder="------"
                                maxlength="6"
                            >
                            @error('code')
                            <p class="text-red-500 text-sm mt-2 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <button 
                            type="submit" 
                            class="w-full bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 text-white font-semibold py-3 px-4 rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                        >
                            <span class="flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Verifikasi
                            </span>
                        </button>
                    </form>

                    <!-- Resend Code Section -->
                    <div 
                        x-data="{ timer: 60, canResend: false, startTimer() { this.canResend = false; let interval = setInterval(() => { this.timer--; if (this.timer === 0) { clearInterval(interval); this.canResend = true; this.timer = 60; }}, 1000); } }" 
                        x-init="startTimer()" 
                        class="text-center mt-6"
                    >
                        <form x-show="canResend" action="{{ route('password.nik') }}" method="POST">
                            @csrf
                            <button type="submit" @click="startTimer()" class="font-semibold text-primary-600 hover:text-primary-500 text-sm transition-colors">
                                Kirim Ulang Kode Verifikasi
                            </button>
                        </form>

                        <p x-show="!canResend" class="text-sm text-gray-500">
                            Tidak menerima kode? Kirim ulang dalam <span x-text="timer" class="font-bold"></span> detik.
                        </p>
                    </div>

                    <!-- Back Link -->
                    <div class="mt-8 text-center">
                        <p class="text-gray-600">
                            Salah nomor?
                            <a href="{{ route('password.request') }}" class="font-semibold text-primary-600 hover:text-primary-500 transition-colors duration-200">
                                Ubah NIK
                            </a>
                        </p>
                    </div>
                </div>

                <!-- Footer -->
                <div class="text-center mt-8 text-sm text-gray-500">
                    <p>&copy; 2024 SIMANTAP. Seluruh hak cipta dilindungi.</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>