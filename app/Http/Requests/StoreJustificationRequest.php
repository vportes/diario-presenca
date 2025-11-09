<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Classe StoreJustificationRequest
 *
 * Valida os dados para criação de uma nova justificativa.
 *
 * @package App\Http\Requests
 *
 * @property int|null $presence_id
 * @property string|null $message
 * @property array|null $attachments
 */
class StoreJustificationRequest extends FormRequest
{
    /**
     * Determina se o usuário está autorizado a fazer esta requisição.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Obtém as regras de validação que se aplicam à requisição.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'presence_id' => 'nullable|exists:presences,id',
            'message' => 'nullable|string|max:2000',
            'attachments.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ];
    }
}
