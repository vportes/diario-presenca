@extends('layouts.app')

@section('content')
    <div class="min-h-[100dvh] bg-gray-100 dark:bg-gray-900 flex flex-col">

        <!-- Área central: ocupa a altura da viewport menos a barra inferior -->
        <main class="flex-1 flex flex-col items-center justify-center px-6"
              style="min-height: calc(100dvh - 64px);">
            <div class="w-full max-w-md flex flex-col items-center">

                <!-- Marca -->
                <div class="text-center mb-6">
                    <div class="text-3xl font-semibold text-orange-500">npi</div>
                    <div class="text-sm text-gray-600 dark:text-gray-300 -mt-1 leading-tight">
                        núcleo de práticas<br>em informática <span class="font-semibold">UniFil</span>
                    </div>
                </div>

                <!-- Relógio + Botão -->
                <div
                    x-data="{
                    now: new Date(),
                    tick() { this.now = new Date(); },
                    pad(n){ return n.toString().padStart(2,'0'); },
                    time(){ return `${this.pad(this.now.getHours())}:${this.pad(this.now.getMinutes())}:${this.pad(this.now.getSeconds())}`; }
                }"
                    x-init="setInterval(()=>tick(),1000)"
                    class="flex flex-col items-center w-full"
                >
                    <!-- Container do relógio -->
                    <div class="relative w-64 h-64 mb-6 flex items-center justify-center">
                        <!-- Anel externo laranja -->
                        <div class="absolute inset-0 rounded-full border-4 border-orange-400 z-10"></div>

                        <!-- Anel branco interno -->
                        <div class="absolute inset-2 rounded-full border-8 border-white/90 dark:border-white/70 z-20"></div>

                        <!-- Fundo leve -->
                        <div class="absolute inset-0 rounded-full bg-white/10 dark:bg-white/5 z-0 pointer-events-none"></div>

                        <!-- Hora -->
                        <div class="relative z-30 text-3xl font-semibold text-gray-800 dark:text-gray-100" x-text="time()"></div>
                    </div>

                    <!-- Botão -->
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

        <!-- Barra inferior fixa -->
        <nav class="fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-md bg-gray-800 text-gray-200">
            <div class="flex items-center justify-around py-2">
                <a href="{{ route('dashboard') }}" class="flex flex-col items-center px-4 py-1 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-gray-300' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mb-1" viewBox="0 0 24 24" fill="currentColor"><path d="M12 3l9 7h-3v9h-5v-6H11v6H6v-9H3l9-7z"/></svg>
                    <span class="text-xs">Início</span>
                </a>
                <a href="{{ route('historico') }}" class="flex flex-col items-center px-4 py-1 {{ request()->routeIs('historico') ? 'text-white' : 'text-gray-300' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mb-1" viewBox="0 0 24 24" fill="currentColor"><path d="M19 3H5a2 2 0 00-2 2v14l4-3h12a2 2 0 002-2V5a2 2 0 00-2-2z"/></svg>
                    <span class="text-xs">Histórico</span>
                </a>
            </div>
        </nav>
    </div>
@endsection
