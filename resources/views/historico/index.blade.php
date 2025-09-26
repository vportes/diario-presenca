@extends('layouts.app')

@section('content')
@php
    use Carbon\Carbon;
    $monthParam = request('month');
    $current = $monthParam ? Carbon::createFromFormat('Y-m', $monthParam)->startOfMonth() : now()->startOfMonth();
    $prev = $current->copy()->subMonth()->format('Y-m');
    $next = $current->copy()->addMonth()->format('Y-m');
    $start = $current->copy();
    $end = $current->copy()->endOfMonth();

    // Indexar presenças por dia (última do dia prevalece)
    $byDay = [];
    foreach ($presences as $p) {
        $dayKey = Carbon::parse($p->occurred_at)->format('Y-m-d');
        $byDay[$dayKey] = $p;
    }

    $defaultFrom = '13:00';
    $defaultTo   = '18:00';
@endphp

<div class="min-h-screen bg-gray-100 dark:bg-gray-900 flex flex-col">
    <div class="flex-1 px-4 pt-4 pb-24 max-w-md mx-auto w-full">
        <!-- Header Mês -->
        <div class="flex items-center justify-between mb-4">
            <a href="{{ route('historico', ['month' => $prev]) }}" class="p-2 rounded-full text-gray-800 dark:text-gray-100 hover:bg-gray-200/60">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor"><path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/></svg>
            </a>
            <div class="flex items-center gap-2 text-gray-800 dark:text-gray-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 -mt-[2px]" viewBox="0 0 24 24" fill="currentColor"><path d="M7 10h5V5H7v5zm6 9h5v-5h-5v5zM7 19h5v-5H7v5zm6-9h5V5h-5v5z"/></svg>
                <span class="text-xl font-medium">
                    {{ ucfirst($current->translatedFormat('F')) }}/{{ $current->format('Y') }}
                </span>
            </div>
            <a href="{{ route('historico', ['month' => $next]) }}" class="p-2 rounded-full text-gray-800 dark:text-gray-100 hover:bg-gray-200/60">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor"><path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6z"/></svg>
            </a>
        </div>

        <!-- Lista de dias -->
        <div class="space-y-2">
            @for ($d = $start->copy(); $d->lte($end); $d->addDay())
                @php
                    $key = $d->format('Y-m-d');
                    /** @var \App\Models\Presence|null $p */
                    $p = $byDay[$key] ?? null;

                    // Status presença
                    $isPresent = $p && in_array($p->status, ['present','presence','ok'], true);

                    // Justificativa (se existir)
                    $just = $p?->justification;
                    $justStatus = $just?->status; // submitted | approved | rejected | needs_more

                    // Horário exibido
                    $from = $p && $p->occurred_at ? Carbon::parse($p->occurred_at)->format('H:i') : $defaultFrom;
                    $to = $defaultTo;
                @endphp

                <div class="bg-gray-200/70 dark:bg-gray-800/60 rounded-xl px-3 py-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gray-800 text-white flex items-center justify-center text-sm font-semibold">
                                {{ $d->format('d') }}
                            </div>
                            <div class="flex flex-col">
                                @if ($isPresent)
                                    <div class="text-gray-900 dark:text-gray-100">{{ $from }} - {{ $to }}</div>
                                @else
                                    <div class="text-gray-900 dark:text-gray-100">Ausente</div>
                                @endif

                                {{-- Badge do status de justificativa --}}
                                @if ($justStatus)
                                    @php
                                        $pillMap = [
                                            'submitted' => ['bg' => 'bg-amber-100 text-amber-800', 'text' => 'Justificativa em análise'],
                                            'approved'  => ['bg' => 'bg-green-100 text-green-800', 'text' => 'Justificativa aprovada'],
                                            'rejected'  => ['bg' => 'bg-red-100 text-red-800', 'text' => 'Justificativa rejeitada'],
                                            'needs_more'=> ['bg' => 'bg-yellow-100 text-yellow-800', 'text' => 'Solicitada complementação'],
                                        ];
                                        $pill = $pillMap[$justStatus] ?? ['bg'=>'bg-gray-100 text-gray-800','text'=>ucfirst($justStatus)];
                                    @endphp
                                    <div class="mt-1 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $pill['bg'] }}">
                                        {{ $pill['text'] }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="shrink-0">
                            @if ($isPresent)
                                <div class="w-6 h-6 rounded-sm bg-green-500 flex items-center justify-center text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17l-3.88-3.88-1.41 1.41L9 19 20.29 7.71 18.88 6.3z"/></svg>
                                </div>
                            @else
                                <div class="w-6 h-6 rounded-sm bg-amber-500 flex items-center justify-center text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Ações --}}
                    @if (!$isPresent)
                        <div class="mt-3 flex items-center gap-2">
                            @if ($p)
                                {{-- Já existe presença do dia: link direto --}}
                                @if (!$justStatus || $justStatus === 'needs_more')
                                    <a href="{{ route('justifications.create', ['presence' => $p->id]) }}"
                                       class="text-xs px-3 py-2 rounded-full text-white"
                                       style="background: linear-gradient(180deg,#F59E0B 0%,#EA580C 100%);">
                                        {{ $justStatus === 'needs_more' ? 'Complementar' : 'Justificar' }}
                                    </a>
                                @endif
                                @if ($justStatus)
                                    <a href="{{ route('justifications.review', $p->justification->id) }}"
                                       class="text-xs px-3 py-2 rounded-full bg-gray-700 text-white">
                                       Ver detalhes
                                    </a>
                                @endif
                            @else
                                {{-- Não existe presença no dia: cria ausência e abre justificativa --}}
                                <form method="POST" action="{{ route('historico.justify') }}">
                                    @csrf
                                    <input type="hidden" name="date" value="{{ $d->format('Y-m-d') }}">
                                    <button type="submit"
                                            class="text-xs px-3 py-2 rounded-full text-white"
                                            style="background: linear-gradient(180deg,#F59E0B 0%,#EA580C 100%);">
                                        Justificar
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endif
                </div>
            @endfor
        </div>
    </div>

    <!-- Bottom Tabs -->
    <nav class="fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-md bg-gray-800 text-gray-200">
        <div class="flex items-center justify-around py-2">
            <a href="{{ route('dashboard') }}" class="flex flex-col items-center px-4 py-1 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-gray-300' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mb-1" viewBox="0 0 24 24" fill="currentColor"><path d="M12 3l9 7h-3v9h-5v-6H11v6H6v-9H3l9-7z"/></svg>
                <span class="text-xs">Início</span>
            </a>
            <a href="{{ route('historico', ['month' => $current->format('Y-m')]) }}" class="flex flex-col items-center px-4 py-1 {{ request()->routeIs('historico') ? 'text-white' : 'text-gray-300' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mb-1" viewBox="0 0 24 24" fill="currentColor"><path d="M19 3H5a2 2 0 00-2 2v14l4-3h12a2 2 0 002-2V5a2 2 0 00-2-2z"/></svg>
                <span class="text-xs">Histórico</span>
            </a>
        </div>
    </nav>
</div>
@endsection
