@extends('layouts.app')

@section('content')
    <!--
      We use calc(100dvh - 65px) because the navigation bar (top bar) is usually h-16 (64px) + 1px border.
      This ensures the total height fits exactly on the screen without scrolling.
    -->
    <div class="h-[calc(100dvh-65px)] w-full overflow-hidden bg-gray-100 dark:bg-gray-900 flex flex-col relative"
         x-data="{
            showPopup: {{ session('popup_message') ? 'true' : 'false' }},
            init() {
                if(this.showPopup) {
                    setTimeout(() => this.showPopup = false, 2000);
                }
            }
         }">

        <!-- Popup Notification -->
        <div x-show="showPopup"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-90"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-90"
             class="fixed top-6 left-1/2 -translate-x-1/2 z-50 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-lg rounded-full px-6 py-3 flex items-center gap-3">
            <div class="text-green-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <span class="text-sm font-medium text-gray-800 dark:text-gray-200">
                {{ session('popup_message') }}
            </span>
        </div>

        <!-- Main Content Area -->
        <!-- pb-20 to clear the bottom navbar -->
        <main class="flex-1 flex flex-col items-center w-full h-full pb-20">
            <!-- Use justify-evenly so elements are spaced out and never overlap or force scrolling -->
            <div class="w-full max-w-md flex flex-col items-center justify-evenly h-full px-6">

                <!-- Branding/Logo -->
                <div class="text-center shrink-0">
                    <div class="text-3xl font-semibold text-orange-500">npi</div>
                    <div class="text-sm text-gray-600 dark:text-gray-300 -mt-1 leading-tight">
                        núcleo de práticas<br>em informática <span class="font-semibold">UniFil</span>
                    </div>
                </div>

                <!-- Clock Section -->
                <div
                    x-data="{
                        now: new Date(),
                        tick() { this.now = new Date(); },
                        pad(n){ return n.toString().padStart(2,'0'); },
                        time(){ return `${this.pad(this.now.getHours())}:${this.pad(this.now.getMinutes())}:${this.pad(this.now.getSeconds())}`; }
                    }"
                    x-init="setInterval(()=>tick(),1000)"
                    class="flex flex-col items-center w-full shrink-0"
                >
                    <!-- Clock Ring -->
                    <div class="relative w-64 h-64 flex items-center justify-center shrink-0">
                        <div class="absolute inset-0 rounded-full border-4 border-orange-400 z-10"></div>
                        <div class="absolute inset-2 rounded-full border-8 border-white/90 dark:border-white/70 z-20"></div>
                        <div class="absolute inset-0 rounded-full bg-white/10 dark:bg-white/5 z-0 pointer-events-none"></div>
                        <div class="relative z-30 text-3xl font-semibold text-gray-800 dark:text-gray-100" x-text="time()"></div>
                    </div>
                </div>

                <!-- Info & Button Section -->
                <div class="flex flex-col items-center w-full shrink-0 gap-4">
                    <!-- Display Today's Sessions -->
                    @php
                        $todayPresences = auth()->user()->presences()
                            ->whereDate('occurred_at', now()->toDateString())
                            ->orderBy('occurred_at', 'asc')
                            ->take(2)
                            ->get();

                        $startTime = $todayPresences->first()?->occurred_at->format('H:i') ?? '--:--';
                        $endTime = $todayPresences->count() > 1 ? $todayPresences->last()->occurred_at->format('H:i') : '--:--';
                        $displayText = "$startTime - $endTime";
                    @endphp

                    <div class="text-center">
                        <span class="text-2xl font-bold text-gray-700 dark:text-gray-200 tracking-wide">
                           {{ $displayText }}
                        </span>
                        <div class="text-xs text-gray-500 uppercase tracking-wider mt-1">Hoje</div>
                    </div>

                    <!-- Button -->
                    <form method="POST" action="{{ route('presence.store') }}" class="w-full max-w-sm">
                        @csrf
                        <input type="hidden" name="status" value="present">
                        <button
                            type="submit"
                            class="w-full py-3 rounded-full text-white font-medium shadow-md shadow-orange-500/20 active:scale-[0.99] transition"
                            style="background: linear-gradient(180deg, #F59E0B 0%, #EA580C 100%);"
                        >
                            Registrar Presença
                        </button>
                    </form>
                </div>

            </div>
        </main>

        <!-- Fixed Bottom Nav -->
        <nav class="fixed bottom-0 left-0 w-full bg-gray-800 text-gray-200 z-40 h-16">
            <div class="flex items-center justify-around h-full max-w-md mx-auto">
                <a href="{{ route('dashboard') }}" class="flex flex-col items-center px-4 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-gray-300' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mb-1" viewBox="0 0 24 24" fill="currentColor"><path d="M12 3l9 7h-3v9h-5v-6H11v6H6v-9H3l9-7z"/></svg>
                    <span class="text-xs">Início</span>
                </a>
                <a href="{{ route('historico') }}" class="flex flex-col items-center px-4 {{ request()->routeIs('historico') ? 'text-white' : 'text-gray-300' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mb-1" viewBox="0 0 24 24" fill="currentColor"><path d="M19 3H5a2 2 0 00-2 2v14l4-3h12a2 2 0 002-2V5a2 2 0 00-2-2z"/></svg>
                    <span class="text-xs">Histórico</span>
                </a>
            </div>
        </nav>
    </div>
@endsection
