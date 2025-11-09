<?php
namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Classe User
 *
 * Representa um usuário no sistema (aluno ou coordenador).
 *
 * @package App\Models
 *
 * @property int $id
 * @property string $name Nome completo do usuário
 * @property string $email Endereço de e-mail
 * @property string $role Função do usuário (aluno, coordenador)
 * @property string $password Senha criptografada
 * @property string|null $registration Número de matrícula do aluno
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Presence> $presences
 * @property-read int|null $presences_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Justification> $justifications
 * @property-read int|null $justifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Observation> $observations
 * @property-read int|null $observations_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * Os atributos que podem ser atribuídos em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name','email','role','password','registration'];

    /**
     * Os atributos que devem ser ocultados na serialização.
     *
     * @var array<int, string>
     */
    protected $hidden = ['password','remember_token'];

    /**
     * Obtém todos os registros de presença para este usuário.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function presences(): HasMany
    {
        return $this->hasMany(Presence::class);
    }

    /**
     * Obtém todas as justificativas enviadas por este usuário.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function justifications(): HasMany
    {
        return $this->hasMany(Justification::class);
    }

    /**
     * Obtém todas as observações sobre este usuário.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function observations(): HasMany
    {
        return $this->hasMany(Observation::class, 'user_id');
    }

    /**
     * Verifica se o usuário é um coordenador.
     *
     * @return bool
     */
    public function isCoordinator(): bool
    {
        return $this->role === 'coordenador';
    }
}
