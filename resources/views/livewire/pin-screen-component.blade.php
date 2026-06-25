<div class="h-screen flex flex-col items-center justify-center p-6 text-white relative overflow-hidden font-sans">
    
    <!-- Ambient Background -->
    <div class="absolute top-1/4 -left-1/4 w-96 h-96 bg-[#cbfc1b]/10 rounded-full blur-[100px] pointer-events-none"></div>
    <div class="absolute -bottom-1/4 -right-1/4 w-96 h-96 bg-emerald-500/10 rounded-full blur-[100px] pointer-events-none"></div>

    <div class="relative z-10 w-full max-w-sm flex flex-col items-center">
        <!-- Lock Icon -->
        <div class="w-20 h-20 bg-white/5 border border-white/10 rounded-full flex items-center justify-center mb-8 shadow-[0_0_40px_rgba(203,252,27,0.1)] {{ $error ? 'animate-bounce text-rose-500 border-rose-500/50' : 'text-[#cbfc1b]' }} transition-colors duration-300">
            @if($error)
                <i data-lucide="shield-alert" class="w-10 h-10"></i>
            @else
                <i data-lucide="lock" class="w-10 h-10"></i>
            @endif
        </div>

        <h1 class="text-2xl font-bold tracking-tight mb-2">Welcome Back</h1>
        <p class="text-sm font-medium text-gray-500 mb-10 text-center">
            @if($error)
                <span class="text-rose-500">Incorrect PIN. Please try again.</span>
            @else
                Enter your 6-digit PIN to access your wealth.
            @endif
        </p>

        <!-- PIN Dots Indicator -->
        <div class="flex gap-4 mb-12">
            @for ($i = 0; $i < 6; $i++)
                <div class="w-4 h-4 rounded-full transition-all duration-300 {{ strlen($pin) > $i ? 'bg-[#cbfc1b] shadow-[0_0_15px_rgba(203,252,27,0.6)] scale-110' : 'bg-white/10 border border-white/20' }}"></div>
            @endfor
        </div>

        <!-- Numpad -->
        <div class="grid grid-cols-3 gap-x-8 gap-y-6 w-full max-w-[280px]">
            @foreach ([1, 2, 3, 4, 5, 6, 7, 8, 9] as $num)
                <button wire:click="appendDigit({{ $num }})" class="w-16 h-16 rounded-full text-2xl font-semibold bg-white/5 hover:bg-white/10 active:bg-white/20 active:scale-95 transition-all flex items-center justify-center mx-auto focus:outline-none">
                    {{ $num }}
                </button>
            @endforeach
            
            <!-- Empty bottom left -->
            <div></div>

            <!-- Zero -->
            <button wire:click="appendDigit(0)" class="w-16 h-16 rounded-full text-2xl font-semibold bg-white/5 hover:bg-white/10 active:bg-white/20 active:scale-95 transition-all flex items-center justify-center mx-auto focus:outline-none">
                0
            </button>

            <!-- Delete/Backspace -->
            <button wire:click="removeDigit" class="w-16 h-16 rounded-full text-lg bg-transparent hover:bg-white/5 active:bg-white/10 active:scale-95 transition-all flex items-center justify-center mx-auto text-gray-400 hover:text-white focus:outline-none">
                <i data-lucide="delete" class="w-7 h-7"></i>
            </button>
        </div>
    </div>
</div>
