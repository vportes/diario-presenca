<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presence extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','occurred_at','status','note'];
    protected $casts = ['occurred_at' => 'datetime'];

    public function user() { return $this->belongsTo(User::class); }
    public function justification() { return $this->hasOne(Justification::class); }
    public function attachments() { return $this->morphMany(Attachment::class,'attachable'); }
}
