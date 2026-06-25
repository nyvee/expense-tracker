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

        <!-- Floating Bottom Navigation Bar -->
        <div class="fixed bottom-4 left-4 right-4 max-w-[calc(100%-2rem)] sm:max-w-[400px] mx-auto z-40">
            <div class="bg-[#111111]/95 backdrop-blur-xl rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.8)] border border-white/5 px-2 py-2 flex justify-between items-center relative">
                
                <!-- Dashboard -->
                <a href="/" class="flex flex-col items-center justify-center w-[20%] py-1 rounded-2xl hover:bg-white/5 transition-colors group {{ request()->routeIs('dashboard') ? 'text-[#cbfc1b]' : 'text-gray-500' }}">
                    <i data-lucide="home" class="w-6 h-6 mb-1"></i>
                    <span class="text-[10px] font-bold">Home</span>
                </a>
                
                <a href="/history" class="flex flex-col items-center justify-center w-[20%] py-1 rounded-2xl hover:bg-white/5 transition-colors group {{ request()->routeIs('history') ? 'text-[#cbfc1b]' : 'text-gray-500' }}">
                    <i data-lucide="history" class="w-6 h-6 mb-1"></i>
                    <span class="text-[10px] font-bold">History</span>
                </a>
                
                <!-- Center Add Button (Floating) -->
                <div class="w-[20%] flex justify-center">
                    <button x-data @click="$dispatch('open-tx-modal')" class="absolute -top-6 flex items-center justify-center w-14 h-14 bg-[#cbfc1b] rounded-full shadow-[0_8px_20px_rgb(203,252,27,0.3)] text-black hover:bg-[#b5e016] hover:scale-105 active:scale-95 transition-all border-4 border-[#0a140b] focus:outline-none">
                        <i data-lucide="plus" class="w-6 h-6"></i>
                    </button>
                </div>

                <a href="/reconciliation" class="flex flex-col items-center justify-center w-[20%] py-1 rounded-2xl hover:bg-white/5 transition-colors group relative {{ request()->routeIs('reconciliation') ? 'text-[#cbfc1b]' : 'text-gray-500' }}">
                    <div class="relative">
                        <i data-lucide="inbox" class="w-6 h-6 mb-1"></i>
                        @php
                            $unreviewedCount = \App\Models\Transaction::where('status', 'Needs Review')->count();
                        @endphp
                        @if($unreviewedCount > 0)
                            <div class="absolute inline-flex items-center justify-center w-3 h-3 text-[9px] font-bold text-black bg-red-600 border-2 border-[#111111] rounded-full -top-0.5 -right-1"></div>
                        @endif
                    </div>
                    <span class="text-[10px] font-bold">Review</span>
                </a>

                <a href="/profile" class="flex flex-col items-center justify-center w-[20%] py-1 rounded-2xl hover:bg-white/5 transition-colors group {{ request()->routeIs('profile') ? 'text-[#cbfc1b]' : 'text-gray-500' }}">
                    <i data-lucide="user" class="w-6 h-6 mb-1"></i>
                    <span class="text-[10px] font-bold">Profile</span>
                </a>
            </div>
        </div>

        <!-- Global Transaction Form Component -->
        <livewire:transaction-form />

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
