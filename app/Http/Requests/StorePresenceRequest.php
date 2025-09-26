<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class StorePresenceRequest extends FormRequest
{
    public function authorize(): bool { return auth()->check(); }
    public function rules(): array {
        return [
            'occurred_at' => 'nullable|date',
            'note' => 'nullable|string|max:1000',
            'status' => 'nullable|in:present,absent'
        ];
    }
}
