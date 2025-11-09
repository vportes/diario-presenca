<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Classe Attachment
 *
 * Representa um anexo de arquivo que pode ser associado a diferentes modelos (polimórfico).
 *
 * @package App\Models
 *
 * @property int $id
 * @property string $filename Nome original do arquivo enviado
 * @property string $path Caminho de armazenamento do arquivo
 * @property string $mime Tipo MIME do arquivo
 * @property int $size Tamanho do arquivo em bytes
 * @property string $attachable_type Tipo do modelo pai
 * @property int $attachable_id ID do modelo pai
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $attachable Modelo pai (Justification ou Presence)
 */
class Attachment extends Model
{
    use HasFactory;

    /**
     * Os atributos que podem ser atribuídos em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = ['filename','path','mime','size'];

    /**
     * Obtém o modelo pai anexável (Justification ou Presence).
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }
}
