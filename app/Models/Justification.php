<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Justification extends Model
{
    use HasFactory;
    protected $fillable = ['presence_id','user_id','message','status','reviewed_by','review_note'];

    public function presence() { return $this->belongsTo(Presence::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function attachments() { return $this->morphMany(Attachment::class,'attachable'); }
}
