@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900 flex flex-col">
        <div class="flex-1 px-4 pt-4 pb-24 max-w-md mx-auto w-full">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-4">
                <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-1">Enviar Justificativa</h1>
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">Anexe documentos e descreva o motivo.</p>

                <form method="POST" action="{{ route('justifications.store') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <input type="hidden" name="presence_id" value="{{ $presence->id ?? '' }}">

                    <div>
                        <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1">Mensagem</label>
                        <textarea name="message" rows="4" class="w-full p-3 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100 border border-transparent focus:ring-2 focus:ring-orange-400 focus:outline-none" placeholder="Descreva o motivo da ausência..."></textarea>
                    </div>

                    <div>
                        <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1">Anexos (PDF, JPG, PNG)</label>
                        <input type="file" name="attachments[]" multiple accept=".pdf,image/*" class="block w-full text-sm text-gray-900 dark:text-gray-100
                    file:mr-4 file:py-2 file:px-4
                    file:rounded-full file:border-0
                    file:text-sm file:font-semibold
                    file:bg-orange-50 file:text-orange-700
                    hover:file:bg-orange-100"/>
                        <p class="text-xs text-gray-500 mt-1">Você pode selecionar múltiplos arquivos.</p>
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                                class="w-full rounded-full py-3 text-white font-medium active:scale-[0.99] transition"
                                style="background: linear-gradient(180deg,#F59E0B 0%,#EA580C 100%); box-shadow: 0 6px 0 rgba(234,88,12,0.35);">
                            Enviar Justificativa
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bottom Tabs -->
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
