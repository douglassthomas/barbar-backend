<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PublicFacilitiesDetail extends Model
{
    //
    protected $primaryKey = ['property_id', 'publicf_id'];
    public $incrementing=false;

    public function getPublicfIdAttribute(){
        $id = $this->attributes['publicf_id'];
//        return $id;
        $data = PublicFacilities::where('id', $id)->get();
        return $data;
    }

    public function PublicFacilitiesDetail(){
        return $this->hasOne(properties::class, 'id', 'property_id');
    }
}
