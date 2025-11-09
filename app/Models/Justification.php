<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Classe Justification
 *
 * Representa uma justificativa de falta, enviada por um aluno e avaliada por um coordenador.
 *
 * @package App\Models
 *
 * @property int $id
 * @property int $presence_id ID do registro de presença relacionado
 * @property int $user_id ID do usuário que enviou a justificativa
 * @property string|null $message Mensagem de justificativa do aluno
 * @property string $status Status da justificativa (submitted, approved, rejected, needs_more)
 * @property int|null $reviewed_by ID do coordenador que avaliou a justificativa
 * @property string|null $review_note Notas de avaliação do coordenador
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \App\Models\Presence $presence
 * @property-read \App\Models\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attachment> $attachments
 * @property-read int|null $attachments_count
 */
class Justification extends Model
{
    use HasFactory;

    /**
     * Os atributos que podem ser atribuídos em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = ['presence_id','user_id','message','status','reviewed_by','review_note'];

    /**
     * Obtém o registro de presença associado a esta justificativa.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function presence(): BelongsTo
    {
        return $this->belongsTo(Presence::class);
    }

    /**
     * Obtém o usuário que enviou esta justificativa.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtém todos os anexos para esta justificativa.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class,'attachable');
    }
}
