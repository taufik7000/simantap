<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'SIMANTAP - Layanan Administrasi Digital Terpadu Simalungun' }}</title>

    @vite('resources/css/app.css')

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    {{-- CSS dari file home.blade.php Anda dipindahkan ke sini --}}
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        .glassmorphism { background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.2); }
        .gradient-text { background: linear-gradient(135deg, #34d399, #059669, #047857); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .floating-card { animation: float 6s ease-in-out infinite; }
        @keyframes float { 0%, 100% { transform: translateY(0px) rotate(0deg); } 33% { transform: translateY(-10px) rotate(1deg); } 66% { transform: translateY(5px) rotate(-1deg); } }
        .grid-pattern { background-image: linear-gradient(rgba(16, 185, 129, 0.1) 1px, transparent 1px), linear-gradient(90deg, rgba(16, 185, 129, 0.1) 1px, transparent 1px); background-size: 60px 60px; }
        .scroll-reveal { opacity: 0; transform: translateY(30px); transition: all 0.8s ease-out; }
        .scroll-reveal.revealed { opacity: 1; transform: translateY(0); }
    </style>
</head>
<body class="antialiased">

    {{-- Memanggil Komponen Header --}}
    <x-partials.header />


    <main>
        {{-- Di sinilah konten halaman (seperti home) akan ditempatkan --}}
        {{ $slot }}
    </main>

    {{-- Memanggil Komponen Footer --}}
    <x-partials.footer />

    {{-- JavaScript dari file home.blade.php Anda dipindahkan ke sini --}}
    <script>
        function revealOnScroll() {
            const reveals = document.querySelectorAll('.scroll-reveal');
            for (let i = 0; i < reveals.length; i++) {
                const windowHeight = window.innerHeight;
                const elementTop = reveals[i].getBoundingClientRect().top;
                const elementVisible = 120;
                if (elementTop < windowHeight - elementVisible) {
                    reveals[i].classList.add('revealed');
                }
            }
        }
        window.addEventListener('scroll', revealOnScroll);
        window.addEventListener('load', revealOnScroll);
    </script>
</body>
</html>