<?php
namespace App\Models;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = ['name','email','role','password','registration'];
    protected $hidden = ['password','remember_token'];

    public function presences() { return $this->hasMany(Presence::class); }
    public function justifications() { return $this->hasMany(Justification::class); }
    public function observations() { return $this->hasMany(Observation::class, 'user_id'); }

    public function isCoordinator(): bool { return $this->role === 'coordenador'; }
}
