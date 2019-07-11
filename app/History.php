<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    //
    protected $keyType = 'string';
    public $incrementing = false;

    public function getPropertyIdAttribute(){
        $property_id = $this->attributes['property_id'];
        $data = properties::where('id', $property_id)->first();
        return $data;
    }
}
