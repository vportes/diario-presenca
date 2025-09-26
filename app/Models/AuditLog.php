<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;
    protected $fillable = ['action','user_id','meta'];
    protected $casts = ['meta' => 'array'];
}
