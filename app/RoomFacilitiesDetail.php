<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoomFacilitiesDetail extends Model
{
    //
    protected $primaryKey = ['property_id', 'roomf_id'];
    public $incrementing=false;

    public function getRoomfIdAttribute(){
        $id = $this->attributes['roomf_id'];
//        return $id;
        $data = RoomFacilities::where('id', $id)->get();
        return $data;
    }


    public function RoomFacilitiesDetail(){
        return $this->hasOne(properties::class, 'id', 'property_id');
    }
}
