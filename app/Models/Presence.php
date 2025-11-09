<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Classe Presence
 *
 * Representa um registro de presença/falta de um usuário em uma data específica.
 *
 * @package App\Models
 *
 * @property int $id
 * @property int $user_id ID do usuário
 * @property \Illuminate\Support\Carbon $occurred_at Data e hora do registro de presença
 * @property string $status Status da presença (present, absent)
 * @property string|null $note Nota opcional sobre a presença
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \App\Models\User $user
 * @property-read \App\Models\Justification|null $justification
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attachment> $attachments
 * @property-read int|null $attachments_count
 */
class Presence extends Model
{
    use HasFactory;

    /**
     * Os atributos que podem ser atribuídos em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = ['user_id','occurred_at','status','note'];

    /**
     * Os atributos que devem ser convertidos.
     *
     * @var array<string, string>
     */
    protected $casts = ['occurred_at' => 'datetime'];

    /**
     * Obtém o usuário associado a este registro de presença.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtém a justificativa para este registro de presença (se houver).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function justification(): HasOne
    {
        return $this->hasOne(Justification::class);
    }

    /**
     * Obtém todos os anexos para este registro de presença.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class,'attachable');
    }
}
