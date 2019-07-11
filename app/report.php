<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class report extends Model
{
    //
    public function getPropertyIdAttribute(){
        $data = properties::where('id', $this->attributes['property_id'])->get()[0];
        return $data;
    }
}
