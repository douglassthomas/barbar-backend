<?php

namespace App\Http\Controllers;

use App\Http\Requests\FacilityRequest;
use App\PublicFacilities;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Ramsey\Uuid\Uuid;

class PublicFacilitiesController extends Controller
{
    //
    public function insert(FacilityRequest $request){
        $publicf = new PublicFacilities();
        $temp_id = Uuid::uuid4();
        $publicf->id = Uuid::uuid4();
        $publicf->name = $request->name;

        $icon = $request->file('icon');
        $extension = $icon->clientExtension();
        $name = $publicf->name .'.'. $extension;
        $path = public_path().'/publicf/';//.'/storage/banner';
        $icon->move($path, $name);
        $publicf->icon = $name;
//        return ['id asli' => $publicf->id, 'name foto' => $name, 'name last' => $publicf->icon];

        $publicf->save();
        $redis = Redis::connection();
        $redis->del('publicf');

        return 'success';
    }

    public function get(){
        $redis = Redis::connection();
        if(!$redis->get('publicf')){
            $redis->set('publicf', PublicFacilities::all());
        }

        return json_decode($redis->get('publicf'));
    }

    public function getPaginate(Request $request){
        $sort = $request->sort;
        $field = $request->field;

//        $room = '';
        if($sort==false){
            return PublicFacilities::orderByDesc($field)->paginate(10);
        }else{
            return PublicFacilities::orderBy($field)->paginate(10);
        }
    }

    public function delete(Request $request){
        $id = $request->id;
        $facility = PublicFacilities::where('id', $id)->get()[0];

        $redis = Redis::connection();
        $redis->del('publicf');

        $facility->delete();
        return "success";
    }

    public function update(Request $request){
        $publicf = PublicFacilities::where('id', $request->id)->get()[0];
//        $temp_id = Uuid::uuid4();
//        $publicf->id = Uuid::uuid4();
        $publicf->name = $request->name;

        $icon = $request->file('icon');
        $extension = $icon->clientExtension();
        $name = $publicf->name .'.'. $extension;
        $path = public_path().'/publicf/';//.'/storage/banner';
        $icon->move($path, $name);
        $publicf->icon = $name;
//        return ['id asli' => $publicf->id, 'name foto' => $name, 'name last' => $publicf->icon];

        $publicf->save();

        $redis = Redis::connection();
        $redis->del('publicf');

        return 'success';
    }
}
