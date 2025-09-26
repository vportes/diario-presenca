@extends('layouts.app')
@section('content')
    <h1 class="text-xl font-semibold mb-4">Revisar Justificativa</h1>
    <div class="bg-white p-4 rounded shadow mb-4">
        <p><strong>Aluno:</strong> {{ $just->user->name }}</p>
        <p><strong>Mensagem:</strong> {{ $just->message }}</p>
        @if($just->attachments->count())
            <p class="mt-2"><strong>Anexos:</strong></p>
            <ul class="list-disc ml-5">
                @foreach($just->attachments as $a)
                    <li><a href="{{ Storage::url($a->path) }}" class="text-blue-600">{{ $a->filename }}</a></li>
                @endforeach
            </ul>
        @endif
    </div>

    <form method="POST" action="{{ route('justifications.decide', $just->id) }}">
        @csrf
        <div class="mb-3">
            <label class="block text-sm text-gray-700">Decisão</label>
            <select name="action" class="mt-1 p-2 border rounded">
                <option value="approve">Aprovar</option>
                <option value="reject">Rejeitar</option>
                <option value="needs_more">Solicitar complementação</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="block text-sm text-gray-700">Observação do Coordenador</label>
            <textarea name="review_note" class="mt-1 p-2 border rounded w-full"></textarea>
        </div>
        <button class="bg-green-600 text-white px-4 py-2 rounded">Registrar decisão</button>
    </form>
@endsection
