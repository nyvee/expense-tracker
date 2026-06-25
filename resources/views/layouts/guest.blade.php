<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $title ?? 'Expense Tracker PWA' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="theme-color" content="#f9fafb">
    <link rel="icon" type="image/png" href="/favicon-64x64.png">
    <link rel="apple-touch-icon" href="/favicon-64x64.png">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fontsource/geist-sans@5.0.1/index.css" rel="stylesheet">

    <style>
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>

<body class="bg-[#050505] text-white">
    <!-- Mobile Container Wrapper -->
    <div class="max-w-md mx-auto min-h-screen bg-gradient-to-b from-[#112a14] via-[#0a140b] to-[#050505] relative shadow-2xl overflow-hidden pb-24 text-white">
        
        <!-- Main Content Area -->
        <main class="min-h-screen overflow-y-auto">
            {{ $slot }}
        </main>

    </div>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });
        document.addEventListener('livewire:initialized', () => {
            Livewire.hook('morph.updated', ({ el, component }) => {
                lucide.createIcons();
            });
        });
    </script>
</body>
</html>
