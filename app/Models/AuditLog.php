<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Classe AuditLog
 *
 * Representa um registro de auditoria para rastreamento de ações dos usuários no sistema.
 *
 * @package App\Models
 *
 * @property int $id
 * @property string $action Identificador da ação (ex: 'presence.create', 'justification.submit')
 * @property int|null $user_id ID do usuário que executou a ação
 * @property array|null $meta Metadados adicionais sobre a ação (armazenado como JSON)
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class AuditLog extends Model
{
    use HasFactory;

    /**
     * Os atributos que podem ser atribuídos em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = ['action','user_id','meta'];

    /**
     * Os atributos que devem ser convertidos.
     *
     * @var array<string, string>
     */
    protected $casts = ['meta' => 'array'];
}
