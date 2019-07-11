<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class priceDetail extends Model
{
    //
    public function priceDetail(){
        return $this->hasOne(properties::class, 'id', 'property_id');
    }
}
