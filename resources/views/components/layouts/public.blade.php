<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Pusat Bantuan' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800">
    <div class="container mx-auto px-4 py-8">
        <header class="mb-8">
            <a href="{{ route('kb.index') }}" class="text-3xl font-bold text-emerald-600">Pusat Bantuan</a>
            <p class="text-gray-600">Temukan jawaban atas pertanyaan Anda di sini.</p>
        </header>
        <main>
            {{ $slot }}
        </main>
        <footer class="mt-12 text-center text-sm text-gray-500">
            <p>&copy; {{ date('Y') }} Layanan Publik. Semua Hak Cipta Dilindungi.</p>
        </footer>
    </div>
</body>
</html>