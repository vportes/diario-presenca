@extends('layouts.app')
@section('content')
    <h1 class="text-xl font-semibold mb-4">Registrar Presença</h1>
    <form method="POST" action="{{ route('presence.store') }}">
        @csrf
        <div class="mb-3">
            <label class="block text-sm text-gray-700">Data e hora (opcional)</label>
            <input type="datetime-local" name="occurred_at" class="mt-1 p-2 border rounded w-full">
        </div>
        <div class="mb-3">
            <label class="block text-sm text-gray-700">Observação (opcional)</label>
            <textarea name="note" class="mt-1 p-2 border rounded w-full"></textarea>
        </div>
        <div class="mb-3">
            <label class="block text-sm text-gray-700">Status</label>
            <select name="status" class="mt-1 p-2 border rounded">
                <option value="present">Presente</option>
                <option value="absent">Ausente (criar como ausência)</option>
            </select>
        </div>
        <button class="bg-green-600 text-white px-4 py-2 rounded">Salvar</button>
    </form>
@endsection
