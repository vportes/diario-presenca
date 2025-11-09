<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Classe StorePresenceRequest
 *
 * Valida os dados para criação de um novo registro de presença.
 *
 * @package App\Http\Requests
 *
 * @property string|null $occurred_at
 * @property string|null $note
 * @property string|null $status
 */
class StorePresenceRequest extends FormRequest
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
            'occurred_at' => 'nullable|date',
            'note' => 'nullable|string|max:1000',
            'status' => 'nullable|in:present,absent'
        ];
    }
}
