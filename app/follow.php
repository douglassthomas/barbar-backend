<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class follow extends Model
{
    //
    protected $keyType = 'string';
    public $incrementing = false;

    public function getOwnerIdAttribute(){
        $data = User::where('id', $this->attributes['owner_id'])->get()[0];
        return $data;
    }
}
