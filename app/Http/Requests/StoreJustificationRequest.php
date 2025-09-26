<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class StoreJustificationRequest extends FormRequest
{
    public function authorize(): bool { return auth()->check(); }
    public function rules(): array {
        return [
            'presence_id' => 'nullable|exists:presences,id',
            'message' => 'nullable|string|max:2000',
            'attachments.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ];
    }
}
