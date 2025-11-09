<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Classe Observation
 *
 * Representa uma observação feita por um coordenador sobre um aluno.
 *
 * @package App\Models
 *
 * @property int $id
 * @property int $user_id ID do usuário sendo observado (o aluno)
 * @property int $created_by ID do usuário que criou a observação (o coordenador)
 * @property string $text Conteúdo de texto da observação
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \App\Models\User $user O aluno sendo observado
 * @property-read \App\Models\User $creator O coordenador que criou a observação
 */
class Observation extends Model
{
    use HasFactory;

    /**
     * Os atributos que podem ser atribuídos em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = ['user_id','created_by','text'];

    /**
     * Obtém o usuário (aluno) que é o assunto desta observação.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class,'user_id');
    }

    /**
     * Obtém o usuário (coordenador) que criou esta observação.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class,'created_by');
    }
}
