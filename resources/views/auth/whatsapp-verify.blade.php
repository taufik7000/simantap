<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi WhatsApp - SIMANTAP</title>
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen bg-gradient-to-br from-primary-50 to-primary-100 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                    <x-heroicon-o-chat-bubble-left-right class="w-8 h-8 text-green-600" />
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Periksa WhatsApp Anda</h2>
                <p class="text-gray-600">Kami telah mengirimkan 6 digit kode verifikasi ke nomor Anda.</p>
            </div>

            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                    <p>{{ $errors->first() }}</p>
                </div>
            @endif

            <form method="POST" action="{{ route('whatsapp.verification.verify') }}" class="space-y-6">
                @csrf
                <div>
                    <label for="code" class="block text-sm font-semibold text-gray-700 mb-2">
                        Kode OTP
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
                </div>
                
                <button type="submit" class="w-full bg-gradient-to-r from-primary-600 to-primary-700 text-white font-semibold py-3 px-4 rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                    Verifikasi Akun
                </button>
            </form>
        </div>
    </div>
</body>
</html>