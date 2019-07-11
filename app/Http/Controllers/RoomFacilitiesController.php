<?php

namespace App\Http\Controllers;

use App\Http\Requests\FacilityRequest;
use App\RoomFacilities;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Ramsey\Uuid\Uuid;

class RoomFacilitiesController extends Controller
{
    //
    public function insert(FacilityRequest $request){
        $publicf = new RoomFacilities();
        $temp_id = Uuid::uuid4();
        $publicf->id = Uuid::uuid4();
        $publicf->name = $request->name;

        $icon = $request->file('icon');
        $extension = $icon->clientExtension();
        $name = $publicf->name .'.'. $extension;
        $path = public_path().'/roomf/';//.'/storage/banner';
        $icon->move($path, $name);
        $publicf->icon = $name;

        $publicf->save();

        //delete dari redis
        $redis = Redis::connection();
        $redis->del('roomf');

        return 'success';
    }


    public function get(Request $request){
        $redis = Redis::connection();
        if(!$redis->get('roomf')){
            $redis->set('roomf', RoomFacilities::all());
        }

        return json_decode($redis->get('roomf'));
//        return RoomFacilities::all();
    }

    public function getPaginate(Request $request){
        $sort = $request->sort;
        $field = $request->field;

//        $room = '';
        if($sort==false){
            return RoomFacilities::orderByDesc($field)->paginate(10);
        }else{
            return RoomFacilities::orderBy($field)->paginate(10);
        }
    }

    public function delete(Request $request){
        $id = $request->id;
        $facility = RoomFacilities::where('id', $id)->get()[0];

        $redis = Redis::connection();
        $redis->del('roomf');

        $facility->delete();
        return "success";
    }

    public function update(Request $request){
        $publicf = RoomFacilities::where('id', $request->id)->get()[0];
//        $temp_id = Uuid::uuid4();
//        $publicf->id = Uuid::uuid4();
        $publicf->name = $request->name;

        $icon = $request->file('icon');
        $extension = $icon->clientExtension();
        $name = $publicf->name .'.'. $extension;
        $path = public_path().'/roomf/';//.'/storage/banner';
        $icon->move($path, $name);
        $publicf->icon = $name;
//        return ['id asli' => $publicf->id, 'name foto' => $name, 'name last' => $publicf->icon];

        $publicf->save();
        $redis = Redis::connection();
        $redis->del('roomf');

        return 'success';
    }
}
