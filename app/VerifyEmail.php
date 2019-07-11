<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class VerifyEmail extends Model
{
    //
    use Notifiable;
    protected $guarded = [];
    protected $table = 'verify_emails';

    protected $fillable = [
      'id', 'user_id', 'email', 'token'
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function user(){
        return $this->belongsTo('App\User', 'user_id');
    }
}
