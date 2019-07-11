<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PremiumTransaction extends Model
{
    //
    protected $keyType = 'string';
    public $incrementing = false;

    public function getUserIdAttribute(){
        $data = User::where('id', $this->attributes['user_id'])->get()[0];
        return $data;
    }

    public function getCreatedAtAttribute(){
        return date("F jS, Y", strtotime($this->attributes['created_at']));
    }

    public function getEndDateAttribute(){
        return date("F jS, Y", strtotime($this->attributes['end_date']));

    }
}
