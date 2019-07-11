<?php

namespace App\Http\Controllers;

use App\apartments;
use App\city;
use App\Comment;
use App\follow;
use App\houses;
use App\Http\Requests\ApartementRequest;
use App\Http\Requests\FacilityRequest;
use App\Http\Requests\HouseRequest;
use App\Http\Requests\RegisterAuthRequest;
use App\Http\Requests\ReqGuestRegister;
use App\Http\Requests\ReqOwnerRegister;
use App\Http\Requests\ReviewRequest;
use App\Picture;
use App\Post;
use App\Premium;
use App\priceDetail;
use App\properties;
use App\PublicFacilities;
use App\PublicFacilitiesDetail;
use App\Review;
use App\RoomFacilities;
use App\RoomFacilitiesDetail;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Contracts\Validation\Validator;

class ApiController extends Controller
{
    public $loginAfterSignUp = true;

    public function register(ReqGuestRegister $request)
    {
        $valid = $request->validated();
//        dd($valid);

        $user = new User();
        $user->id = Uuid::uuid4()->getHex();
        $user->username = $request->username;
        $user->name = $valid['name'];
        $user->email = $valid['email'];
        $user->phone = $request->phone;
        $user->password = bcrypt($valid['password']);
        $user->type = $request->type;
        $user->setCreatedAt(now());
        $user->setUpdatedAt(now());
        $user->save();

//        if ($this->loginAfterSignUp) {
//            return $this->login($request);
//        }
        if($user!=null){
            return response()->json([
                'success' => true,
                'data' => $user
            ]);
        }
        else{
            return response()->json([
                'success' => false
            ]);
        }
    }

    public function register_owner(ReqOwnerRegister $request){
        $valid = $request->validated();

        $user = new User();
        $user->id = Uuid::uuid4()->getHex();
        $user->username = $valid['username'];
        $user->name = $valid['name'];
        $user->email = $valid['email'];
        $user->phone = $valid['phone'];
        $user->password = bcrypt($valid['password']);
        $user->type = $valid['type'];
        $user->setCreatedAt(now());
        $user->setUpdatedAt(now());
        $user->save();

        return response()->json([
//            'success' => true,
            'data' => $user
        ], 200);
    }

    public function login(Request $request)
    {
        $input = $request->only('input', 'password');
        $jwt_token = null;
        $u = User::where('email', $input)->first();

        if($u->status == 'banned'){
            return response()->json([
                'success' => false,
                'message' => 'User is being banned',
            ]);
        }

        $req_json = [
            'email' => $request->input,
            'password' => base64_decode($request->password)
        ];
//        {{$u = $u==null ? User::where('username', $request->username)->first():$u;}}
        if(!$u) {
            $u = User::where('phone', $input)->first();
            $req_json = [
                'phone' => $request->input,
                'password' => base64_decode($request->password)
            ];
        }

        JWTAuth::factory()->setTTL(60*24);
        if (!$jwt_token = JWTAuth::attempt($req_json)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Email or Password',
            ]);
        }



        return response()->json([
            'success' => true,
            'token' => $jwt_token,
            'name' => $u->name,
            'id' => $u->id,
            'email' => $u->email,
            'type' => $u->type
        ]);
    }

    public function getUserId(Request $request){
        $input = $request->only('input', 'password');
        $jwt_token = null;
        $u = User::where('email', $input) -> first();

    }

    public function logout(Request $request)
    {
        $this->validate($request, [
            'token' => 'required'
        ]);

        try {
            JWTAuth::invalidate($request->token);

            return response()->json([
                'success' => true,
                'message' => 'User logged out successfully'
            ]);
        } catch (JWTException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, the user cannot be logged out'
            ], 500);
        }
    }

    public function getAuthUser(Request $request)
    {
//        $this->validate($request, [
//            'token' => 'required'
//        ]);

        $user = JWTAuth::authenticate($request->token);
        return response()->json(['user' => $user]);
    }

    public function authUser(Request $request){
        dd($request->only('input'));
    }

    public function editProfile(Request $request){
//        return $request;
        $image = $request->file('input-image');
        $extension = $image->clientExtension();
        $user = User::where('id', $request->id)->first();
//        return $user;

        $name = $request->id.'.'.$extension;
        $path = public_path().'/images/profile_picture/';
        $image->move($path, $name);

        $user->profile_pic = $name;
        $user->save();

        return 'success';
    }

    public function getUserInfo(Request $request){
        $id = $request->id;
        $user = User::where('id', $id)->get();
        return $user[0];
    }

    public function getOwnerData(Request $request){


        $user = User::find($request->id);

        $history = follow::where('guest_id', $request->id)->get();
        return sizeof($history);
    }

    public function getFollowing(Request $request){
//        return $request;

        $user = User::find($request->id);

        $history = follow::where('guest_id', $request->id)->get();
        return $history;
    }

    public function checkFollow(Request $request){
        $history = follow::where('guest_id', $request->guest_id)->where('owner_id', $request->owner_id)->get();
//        return $request;

        if(sizeof($history)==0){
            return "false";
        }

        return "true";
    }

    public function follow(Request $request){
        $follow = new follow();
        $follow->guest_id = $request->guest_id;
        $history = follow::where('guest_id', $request->guest_id)->where('owner_id', $request->owner_id)->get();
        if(sizeof($history)==0){
            $follow->id = Uuid::uuid4();
            $follow->owner_id = $request->owner_id;
            $follow->save();

            return 'success';
        }
        else {
            return 'error history';
        }
    }

    public function unfollow(Request $request){
        $history = follow::where('guest_id', $request->guest_id)->where('owner_id', $request->owner_id)->get()[0];
        $history->delete();
    }

    public function addApartement(ApartementRequest $request){
//        return $request;
        $apartement = new apartments();
        $property = new properties();
        if($request->property_id!='undefined'){
            $property = properties::where('id', $request->property_id)->get()[0];
        }

//        $valid = $request->validated();
        $user = JWTAuth::parseToken()->authenticate();


        //house id
        $apartement->id = Uuid::uuid4()->toString();
        $apartement->save();
        //property id
        $property->id = Uuid::uuid4()->toString();


        //floor
        $apartement->floor = $request->floor;
        //parking

        $apartement->unit_condition = $request->unit_condition;

        //unit type
        $apartement->unit_type = $request->unit_type;

        //name
        $property->name = $request->name;

        //description
        $property->description = $request->description;
        $property->save();

        //room facilities
        for($i = 0; $i<sizeof($request->roomf); $i++){
            $room_facilities_detail = new RoomFacilitiesDetail();
            $room_facilities_detail->property_id = $property->id;
            $room_facilities_detail->roomf_id = $request->roomf[$i];
            $room_facilities_detail->save();
        }

        //area
        $property->area = $request->area;
        //public facilities
//        $property->public_facilities = $request->publicf;
        for($i = 0; $i<sizeof($request->publicf); $i++){
            $room_facilities_detail = new PublicFacilitiesDetail();
            $room_facilities_detail->property_id = $property->id;
            $room_facilities_detail->publicf_id = $request->publicf[$i];
            $room_facilities_detail->save();
        }

        $parking_string = '';
        for($i=0; $i<sizeof($request->parking); $i++){
            $parking_string=$parking_string.$request->parking[$i];
        }
        $apartement->parking = $parking_string;

        //banner
        if($request->file('banner')!=null){
            $image_banner = $request->file('banner');
            $extension = $image_banner->clientExtension();
            $name = $apartement->id .'.'. $extension;
            $banner_path = public_path().'/images/banner/';//.'/storage/banner';
            $image_banner->move($banner_path, $name);
            $property->banner_id = $name;
        }

        //picture360
        if($request->file('picture360')!=null){
            $image_banner = $request->file('picture360');
            $extension = $image_banner->clientExtension();
            $name = $apartement->id .'.'. $extension;
            $banner_path = public_path().'/images/picture360/';//.'/storage/banner';
            $image_banner->move($banner_path, $name);
            $property->banner_id = $name;
        }


        //video
        if($request->file('video')!=null){
            $image_banner = $request->file('video');
            $extension = $image_banner->clientExtension();
            $name = $apartement->id .'.'. $extension;
            $banner_path = public_path().'/images/video/';//.'/storage/banner';
            $image_banner->move($banner_path, $name);
            $property->banner_id = $name;
        }


        //pictures [PENDING]


        //information
        $property->information = $request->information;
        //fee
        $property->fee = $request->fee;
        //price
        $property->price = $request->price;
        $price = new priceDetail();
        $price->property_id = $property->id;
        $price->yearly_price = $request->price_year;
        $price->monthly_price = $request->price_month;
        $price->weekly_price = $request->price_week;
        $price->daily_price = $request->price_day;
        $price->save();

        //city
        $property->city_id = $request->city_id;
        //address
        $property->address = $request->address;
        //latitude
        $property->latitude = $request->latitude;
        //longitude
        $property->longitude = $request->longitude;


        //save
        $apartement->property()->save($property);
        $apartement->save();

        //id nya house ke propertiable_id
        $property->propertiable_id = $apartement->id;
        //owner_id
        $property->owner_id = $request->owner_id;

        $property->save();

        $redis = Redis::connection();

        $names = explode(' ', $property->name);

        $json = [
            'id'=>$property->id,
            'name'=>$property->name,
            'address'=>$property->address
        ];

        $json_en = json_encode($json);
        $redis->sadd($property->id, $json_en);

        foreach ($names as $n){
            $redis->sadd($n, $property->id);
        }

        //return
        return response()->json([
            'success' => true
        ]);
    }

    public function delProperty(Request $request){
        $id = $request->id;

        $property = properties::where('id', $id)->get()[0];
        $property->delete();

        return "property deleted successfully";
    }

    public function addHouse(HouseRequest $request){
//        return $request;
        $house = new houses();
        $property = new properties();
        if($request->property_id!='undefined'){
            $property = properties::where('id', $request->property_id)->get()[0];
        }

//        $valid = $request->validated();
        $user = JWTAuth::parseToken()->authenticate();


        //house id
        $house->id = Uuid::uuid4()->toString();
        $house->save();
        //property id
        $property->id = Uuid::uuid4()->toString();

        //room left
        $house->room_left = $request->room_left;
        //parking
        $parking_string = '';
        for($i=0; $i<sizeof($request->parking); $i++){
            $parking_string=$parking_string.$request->parking[$i];
        }
        $house->parking = $parking_string;

        //gender
        $gender_string = '';
        for($i=0; $i<sizeof($request->gender_type); $i++){
            $gender_string=$gender_string.$request->gender_type[$i];
        }
        $house->gender_type = $gender_string;

        //name
        $property->name = $request->name;
        //description
        $property->description = $request->description;
        $property->save();

        //room facilities
        for($i = 0; $i<sizeof($request->roomf); $i++){
            $room_facilities_detail = new RoomFacilitiesDetail();
            $room_facilities_detail->property_id = $property->id;
            $room_facilities_detail->roomf_id = $request->roomf[$i];
            $room_facilities_detail->save();
        }

        //area
        $property->area = $request->area;
        //public facilities
//        $property->public_facilities = $request->publicf;
        for($i = 0; $i<sizeof($request->publicf); $i++){
            $room_facilities_detail = new PublicFacilitiesDetail();
            $room_facilities_detail->property_id = $property->id;
            $room_facilities_detail->publicf_id = $request->publicf[$i];
            $room_facilities_detail->save();
        }

        //banner
        if($request->file('banner')!=null){
            $image_banner = $request->file('banner');
            $extension = $image_banner->clientExtension();
            $name = $house->id .'.'. $extension;
            $banner_path = public_path().'/images/banner/';//.'/storage/banner';
            $image_banner->move($banner_path, $name);
            $property->banner_id = $name;
        }

        //picture360
        if($request->file('picture360')!=null){
            $image_banner = $request->file('picture360');
            $extension = $image_banner->clientExtension();
            $name = $house->id .'.'. $extension;
            $banner_path = public_path().'/images/picture360/';//.'/storage/banner';
            $image_banner->move($banner_path, $name);
            $property->banner_id = $name;
        }


        //video
        if($request->file('video')!=null){
            $image_banner = $request->file('video');
            $extension = $image_banner->clientExtension();
            $name = $house->id .'.'. $extension;
            $banner_path = public_path().'/images/video/';//.'/storage/banner';
            $image_banner->move($banner_path, $name);
            $property->banner_id = $name;
        }


        //pictures [PENDING]
        $listPicture = $request->file('pictures');
        $count = 0;
        if($listPicture!=null){
            foreach ($listPicture as $p){
                $extension = $p->clientExtension();
                $name = $count++.'.'.$extension;
                $path = public_path().'/images/pictures/'.$house->id.'/';
                $p->move($path, $name);
            }
        }


        //information
        $property->information = $request->information;
        //fee
        $property->fee = $request->fee;
        //price
        $property->price = $request->price;
        $price = new priceDetail();
        $price->property_id = $property->id;
        $price->yearly_price = (int)$request->price_year;
        $price->monthly_price = (int)$request->price_month;
        $price->weekly_price = (int)$request->price_week;
        $price->daily_price = (int)$request->price_day;
        $price->save();

        //city
        $property->city_id = $request->city_id;
        //address
        $property->address = $request->address;
        //latitude
        $property->latitude = $request->latitude;
        //longitude
        $property->longitude = $request->longitude;


        //save
        $house->property()->save($property);
        $house->save();

        //id nya house ke propertiable_id
        $property->propertiable_id = $house->id;
        //owner_id
        $property->owner_id = $request->owner_id;

        $property->save();


        $redis = Redis::connection();

        $names = explode(' ', $property->name);

        $json = [
            'id'=>$property->id,
            'name'=>$property->name,
            'address'=>$property->address
        ];

        $json_en = json_encode($json);
        $redis->sadd($property->id, $json_en);

        foreach ($names as $n){
            $redis->sadd($n, $property->id);
        }

        //return
        return response()->json([
            'success' => true
        ]);
    }

    public function updateHouse(HouseRequest $request){
//        return $request;
        $property = properties::where('id', $request->property_id)->get()[0];
        $house = houses::where('id', $property->propertiable_id)->get()[0];

//        $valid = $request->validated();
        $user = JWTAuth::parseToken()->authenticate();



        //room left
        $house->room_left = $request->room_left;
        //parking
        $parking_string = '';
        for($i=0; $i<sizeof($request->parking); $i++){
            $parking_string=$parking_string.$request->parking[$i];
        }
        $house->parking = $parking_string;

        //gender
        $gender_string = '';
        for($i=0; $i<sizeof($request->gender_type); $i++){
            $gender_string=$gender_string.$request->gender_type[$i];
        }
        $house->gender_type = $gender_string;

        //name
        $property->name = $request->name;
        //description
        $property->description = $request->description;
        $property->save();

        //room facilities
        $roomf = RoomFacilitiesDetail::where('property_id', $property->id)->get();
        for($i=0; $i<sizeof($roomf); $i++){
            return $roomf[$i];
        }
        return 'a';
        for($i = 0; $i<sizeof($request->roomf); $i++){
            $room_facilities_detail = new RoomFacilitiesDetail();
            $room_facilities_detail->property_id = $property->id;
            $room_facilities_detail->roomf_id = $request->roomf[$i];
            $room_facilities_detail->save();
        }

        //area
        $property->area = $request->area;
        //public facilities
//        $property->public_facilities = $request->publicf;
        $publicf = PublicFacilitiesDetail::where('property_id', $property->id)->get();
        for($i=0; $i<sizeof($publicf); $i++){
            $publicf[$i]->delete();
        }
        for($i = 0; $i<sizeof($request->publicf); $i++){
            $room_facilities_detail = new PublicFacilitiesDetail();
            $room_facilities_detail->property_id = $property->id;
            $room_facilities_detail->publicf_id = $request->publicf[$i];
            $room_facilities_detail->save();
        }

        //banner
        if($request->file('banner')!=null){
            $image_banner = $request->file('banner');
            $extension = $image_banner->clientExtension();
            $name = $house->id .'.'. $extension;
            $banner_path = public_path().'/images/banner/';//.'/storage/banner';
            $image_banner->move($banner_path, $name);
            $property->banner_id = $name;
        }

        //picture360
        if($request->file('picture360')!=null){
            $image_banner = $request->file('picture360');
            $extension = $image_banner->clientExtension();
            $name = $house->id .'.'. $extension;
            $banner_path = public_path().'/images/picture360/';//.'/storage/banner';
            $image_banner->move($banner_path, $name);
            $property->banner_id = $name;
        }


        //video
        if($request->file('video')!=null){
            $image_banner = $request->file('video');
            $extension = $image_banner->clientExtension();
            $name = $house->id .'.'. $extension;
            $banner_path = public_path().'/images/video/';//.'/storage/banner';
            $image_banner->move($banner_path, $name);
            $property->banner_id = $name;
        }


        //pictures [PENDING]
        $listPicture = $request->file('pictures');
        $count = 0;
        if($listPicture!=null){
            foreach ($listPicture as $p){
                $extension = $p->clientExtension();
                $name = $count++.'.'.$extension;
                $path = public_path().'/images/pictures/'.$house->id.'/';
                $p->move($path, $name);
            }
        }

        //information
        $property->information = $request->information;
        //fee
        $property->fee = $request->fee;
        //price
        $property->price = $request->price;
        $price = priceDetail::where('property_id', $property->id)->get()[0];
//        return $price;
//        $price->property_id = $property->id;
        $price->yearly_price = $request->price_year;
        $price->monthly_price = $request->price_month;
        $price->weekly_price = $request->price_week;
        $price->daily_price = $request->price_day;
        $price->save();

        //city
        $property->city_id = $request->city;
        //address
        $property->address = $request->address;
        //latitude
        $property->latitude = $request->latitude;
        //longitude
        $property->longitude = $request->longitude;


        //save
//        $house->property()->save($property);
        $house->save();

        //id nya house ke propertiable_id
        $property->propertiable_id = $house->id;
        //owner_id
        $property->owner_id = $request->owner_id;

        $property->save();

        //return
        return response()->json([
            'success' => true
        ]);
    }

    public function getAll(Request $request){
//        return $request;
        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $type = $request->type;
        $perPage = $request->perPage;

        if($type==="houses") $type = "App\houses";
        else if($type==="apartements") $type="App\apartments";

//        return $type;



        $property = properties::with('propertiable')->where('propertiable_type', $type)->select(DB::raw('*, ( 6367 * acos( cos( radians('.$latitude.') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('.$longitude.') ) + sin( radians('.$latitude.') ) * sin( radians( latitude ) ) ) ) AS distance'))->having('distance', '<', 25)
            ->orderBy('distance')->simplePaginate($perPage?$perPage:10);

        return $property;
    }



    public function getKostByOwnerId(Request $request){
//        return $request;
        $owner_id = $request->owner_id;

        $property = properties::with('propertiable')->where('owner_id', $owner_id)
            ->where('propertiable_type', 'App\houses')->orderByDesc('created_at')
            ->paginate(10);

        return $property;
    }

    public function getApartementByOwnerId(Request $request){
//        return $request;
        $owner_id = $request->owner_id;

        $property = properties::where('owner_id', $owner_id)
            ->where('propertiable_type', 'App\apartments')->
            paginate(10);

        return $property;
    }

    public function getPropertyById(Request $request){
        $id = $request->id;

        return properties::with('propertiable')->where('id', $id)->get()[0];
    }

    public function testRedis(Request $request){
        $to_id = $request->to_id;
        $from_id = $request->from_id;
        $contents = $request->contents;

        $redis = Redis::connection();
        $chat_room_id = $redis->get($to_id.$from_id)?$redis->get($to_id.$from_id):$redis->get($from_id.$to_id);
        if($chat_room_id == null){
            $chat_room_id = (int)$redis->get('chat_room_id')+1;
            $redis->set('chat_room_id', $chat_room_id);
            $redis->set($to_id.$from_id, $chat_room_id);
        }
        //masukin ke list $to_id
        $redis->lrem('chat.with:'.$to_id, 0, $from_id);
        $redis->lpush('chat.with:'.$to_id, $from_id);

        //masukin ke list $from_id
        $redis->lrem('chat.with:'.$from_id, 0, $to_id);
        $redis->lpush('chat.with:'.$from_id, $to_id);


        $newId = sizeof($redis->zrangebyscore('chat.room:'.$chat_room_id, '-inf','+inf'))+1;
        $redis->hset('chat.'.$chat_room_id.':'.$newId, 'chat_id', $newId, 'to_id', $to_id, 'from_id', $from_id, 'contents', $contents);
        $redis->zadd('chat.room:'.$chat_room_id, $newId, $newId);
        $arr = $redis->zrangebyscore('chat.room:'.$chat_room_id, 0, $newId);

        $arrItem = [];
        for($i=0; $i<sizeof($arr); $i++){
            if($redis->hgetall('chat.'.$chat_room_id.':'.$arr[$i])!=null){
                array_push($arrItem, $redis->hgetall('chat.'.$chat_room_id.':'.$arr[$i]));
            }
        }
        return response()->json([
            'history_'.$to_id => $redis->lrange('chat.with:'.$to_id, 0, -1),
            'history_'.$from_id => $redis->lrange('chat.with:'.$from_id, 0, -1),
            'chats' => $arrItem
        ]);

        $s = json_encode($redis->lrange('test', 0, -1));
//        return $redis->lrange('test', 0, -1);
        return json_decode($s, true);
    }

    public function sendChat(Request $request){
        $to_id = $request->to_id;
        $from_id = $request->from_id;
        $contents = $request->contents;

        $redis = Redis::connection();
        $chat_room_id = $redis->get($to_id.$from_id)?$redis->get($to_id.$from_id):$redis->get($from_id.$to_id);
        if($chat_room_id == null){
            $chat_room_id = (int)$redis->get('chat_room_id')+1;
            $redis->set('chat_room_id', $chat_room_id);
            $redis->set($to_id.$from_id, $chat_room_id);
        }
        //masukin ke list $to_id
        $redis->lrem('chat.with:'.$to_id, 0, $from_id);
        $redis->lpush('chat.with:'.$to_id, $from_id);

        //masukin ke list $from_id
        $redis->lrem('chat.with:'.$from_id, 0, $to_id);
        $redis->lpush('chat.with:'.$from_id, $to_id);

        $newId = sizeof($redis->zrangebyscore('chat.room:'.$chat_room_id, '-inf','+inf'))+1;
        $redis->hset('chat.'.$chat_room_id.':'.$newId, 'chat_id', $newId, 'to_id', $to_id, 'from_id', $from_id, 'contents', $contents);
        $redis->zadd('chat.room:'.$chat_room_id, $newId, $newId);

        return "success";
    }

    public function getAllChat(Request $request){
        $to_id = $request->to_id;
        $from_id = $request->from_id;

        $redis = Redis::connection();
        $chat_room_id = $redis->get($to_id.$from_id)?$redis->get($to_id.$from_id):$redis->get($from_id.$to_id);
        if($chat_room_id == null){
            $chat_room_id = (int)$redis->get('chat_room_id')+1;
            $redis->set('chat_room_id', $chat_room_id);
            $redis->set($to_id.$from_id, $chat_room_id);
        }

        $newId = sizeof($redis->zrangebyscore('chat.room:'.$chat_room_id, '-inf','+inf'));
        $arr = $redis->zrangebyscore('chat.room:'.$chat_room_id, 0, $newId);

        //kasih tau lawan, gua udh read sampe mana
        $redis->set($to_id.':'.$chat_room_id, $newId);
        //liat lawan udah read sampe mana
        $read = $redis->get($from_id.':'.$chat_room_id)?
            $redis->get($from_id.':'.$chat_room_id):0;

        $arrItem = [];
        for($i=0; $i<sizeof($arr); $i++){
            if($redis->hgetall('chat.'.$chat_room_id.':'.$arr[$i])!=null){
                array_push($arrItem, $redis->hgetall('chat.'.$chat_room_id.':'.$arr[$i]));
            }
        }

        return response()->json([
            'chat'=>$arrItem,
            'read'=>$read
        ]);
    }

    public function getRead(Request $request){
        $to_id = $request->to_id;
        $from_id = $request->from_id;

        $redis = Redis::connection();
        $chat_room_id = $redis->get($to_id.$from_id)?$redis->get($to_id.$from_id):$redis->get($from_id.$to_id);
        if($chat_room_id == null){
            $chat_room_id = (int)$redis->get('chat_room_id')+1;
            $redis->set('chat_room_id', $chat_room_id);
            $redis->set($to_id.$from_id, $chat_room_id);
        }

        $read = $redis->get($from_id.':'.$chat_room_id)?
            $redis->get($from_id.':'.$chat_room_id):0;

        return $read;
    }

    public function read(Request $request){
        $to_id = $request->to_id;
        $from_id = $request->from_id;

        $redis = Redis::connection();
        $chat_room_id = $redis->get($to_id.$from_id)?$redis->get($to_id.$from_id):$redis->get($from_id.$to_id);
        if($chat_room_id == null){
            $chat_room_id = (int)$redis->get('chat_room_id')+1;
            $redis->set('chat_room_id', $chat_room_id);
            $redis->set($to_id.$from_id, $chat_room_id);
        }

        $newId = sizeof($redis->zrangebyscore('chat.room:'.$chat_room_id, '-inf','+inf'));
        $redis->set($to_id.':'.$chat_room_id, $newId);

        return "success";
    }

    public function getChatList(Request $request){
//        return $request;
        $id = $request->id;
        $redis = Redis::connection();
        $arr = $redis->lrange('chat.with:'.$id, 0, -1);
        $arrJson = [];
        for($i=0; $i<sizeof($arr); $i++){
            $chat_room_id = $redis->get($id.$arr[$i])?$redis->get($id.$arr[$i]):$redis->get($arr[$i].$id);
            $newId = sizeof($redis->zrangebyscore('chat.room:'.$chat_room_id, '-inf','+inf'));
            $last_id = $redis->zrangebyscore('chat.room:'.$chat_room_id, $newId, $newId)[0];
            $message = $redis->hgetall('chat.'.$chat_room_id.':'.$last_id);
            $new = [
                'id' => $arr[$i],
                'last_chat' => $message,
                'user' => User::where('id', $arr[$i])->get()[0]
            ];
            array_push($arrJson, $new);
        }

        return $arrJson;
    }

    public function getCity(){
        return response(city::all()->sortBy('id')->values());
    }

    public function incrementView(Request $request){
//        return $request->property_id;
        $property_id = $request->property_id;
        $data = properties::where('id', $property_id)->get()[0];
        if($data != null){
            $views = $data->total_views?$data->total_views:0;
            $data->total_views = $views+1;
            $data->save();

            return 'success';
        }

        return 'error';
    }

    public function insertRating(ReviewRequest $request){
//        return $request;
        $property_id = $request->property_id;
        $parent_id = $request->parent_id;
        $contents = $request->contents;
        $cleanliness = $request->cleanliness;
        $roomf = $request->roomf;
        $publicf = $request->publicf;
        $security = $request->security;

        $review = new Review();

        $review->id = Uuid::uuid4()->toString();
        $review->property_id = $property_id;
        $review->contents = $contents;
        $review->user_id = $request->user_id;
        $review->cleanliness = $cleanliness;
        $review->roomf = $roomf;
        $review->publicf = $publicf;
        $review->security = $security;
        $review->save();

        return "success";
    }

    public function insertReview(Request $request){
//        return $request;
        $property_id = $request->property_id;
        $parent_id = $request->parent_id;
        $contents = $request->contents;

        $review = new Comment();

        $review->id = Uuid::uuid4();
        $review->property_id = $property_id;
        $review->parent_id = $parent_id;
        $review->contents = $contents;
        $review->user_id = $request->user_id;
        $review->save();

        return "success";
    }

    public function getReviewByPropertyId(Request $request){
        $property_id = $request->property_id;
        $paginate = $request->paginate;


        $data = Review::where('property_id', $property_id)->paginate($paginate!=null?$paginate:10);
        return $data;

    }

    public function getRatingAverage(Request $request){
//        return $request;
        $property_id = $request->property_id;
        $data = Review::where('property_id', $property_id)->get();

        $avg = 0;
        foreach ($data as $d){
            $sum = ($d->cleanliness+$d->roomf+$d->publicf+$d->security)/4;
            $avg += $sum;
        }
        $avg/=sizeof($data);

        return round($avg, 1);
    }

    public function getReviewByPropertyIdnopage(Request $request){
        $property_id = $request->property_id;
        $data = Review::where('property_id', $property_id)->where('parent_id', null)->get();
        return $data;

    }

    public function goChangePassword(Request $request){
//        return $request;
        $id = $request->id;
        $curr_password = $request->curr_password;
        $new_password = $request->new_password;

        $req_json = [
            'id' => $id,
            'password' => $curr_password
        ];

        if (!$jwt_token = JWTAuth::attempt($req_json)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Email or Password',
            ]);
        }

        $user = User::where('id', $id)->first();
        $user->password = bcrypt($new_password);
        $user->save();
        return "password changed successfuly";
    }

    public function goEditProfileInfo(Request $request){
//        return $request;
        $full_name = $request->full_name;
        $email = $request->email;
        $phone = $request->phone;
        $id = $request->id;

//        $req_json = [
//            'id' => $id
//        ];
//        if (!$jwt_token = JWTAuth::attempt($req_json)) {
//            return response()->json([
//                'success' => false,
//                'message' => 'Your session has expired',
//            ]);
//        }

        $user = User::where('id', $id)->first();
        if($full_name!=null){
            $user->name = $full_name;
        }
        if($email!=null) {
            $user->email = $email;
        }
        if($phone!=null) {
            $user->phone = $phone;
        }
        $user->save();

        return "profile updated successfuly";
    }

    public function getAdminDashboardInfo(){
        $now_month = '';
        $now_month .= (string)Carbon::now()->year;
        $now_month .= '-';
        if((int)Carbon::now()->month<10){
            $now_month.='0';
            $now_month.=Carbon::now()->month;
        }
        else{
            $now_month.=Carbon::now()->month;
        }
//        return $now_month;

        $guest_count = sizeof(User::where('type', '1')->get());
        $owner_count = sizeof(User::where('type', '2')->get());
        $apartements_count = sizeof(properties::where('propertiable_type', 'App\apartments')->get());
        $houses_count = sizeof(properties::where('propertiable_type', 'App\houses')->get());
        $facility_count = sizeof(RoomFacilities::all())+sizeof(PublicFacilities::all());
        $premium_count = sizeof(Premium::all());
        $post_count = sizeof(Post::all());

        $guest_this_month = sizeof(User::where('type', '1')->where( DB::raw('MONTH(created_at)'), '=', date('n') )->get());
        $owner_this_month = sizeof(User::where('type', '2')->where( DB::raw('MONTH(created_at)'), '=', date('n') )->get());
        $apartements_this_month = sizeof(properties::where('propertiable_type', 'App\apartments')->where( DB::raw('MONTH(created_at)'), '=', date('n') )->get());
        $houses_this_month = sizeof(properties::where('propertiable_type', 'App\houses')->where( DB::raw('MONTH(created_at)'), '=', date('n') )->get());
        $facility_this_month = sizeof(RoomFacilities::where( DB::raw('MONTH(created_at)'), '=', date('n') )->get())+sizeof(PublicFacilities::where( DB::raw('MONTH(created_at)'), '=', date('n') )->get());
        $premium_this_month = sizeof(Premium::where( DB::raw('MONTH(created_at)'), '=', date('n') )->get());
        $post_this_month = sizeof(Post::where( DB::raw('MONTH(created_at)'), '=', date('n') )->get());


//        return Carbon::now()->month;
        return response()->json([
            ['name' => 'GUEST',
                'contents' => $guest_count,
                'this_month' => $guest_this_month],
            ['name' => 'OWNER',
                'contents' => $owner_count,
                'this_month' => $owner_this_month],
            ['name' => 'APARTEMENTS',
                'contents' => $apartements_count,
                'this_month' => $apartements_this_month],
            ['name' => 'KOST',
                'contents' => $houses_count,
                'this_month' => $houses_this_month],
            ['name' => 'FACILITIES',
                'contents' => $facility_count,
                'this_month' => $facility_this_month],
            ['name' => 'PREMIUM',
                'contents' => $premium_count,
                'this_month' => $premium_this_month],
            ['name' => 'POST',
                'contents' => $post_count,
                'this_month' => $post_this_month]
        ]);
    }

    public function cobaReview(Request $request){
        $redis = Redis::connection();


        $names = explode(' ', $request->name);
        $json = [
            'id'=>$request->id,
            'name'=>$request->name,
            'address'=>$request->address
        ];

        $json_en = json_encode($json);
//        return $json_en;
        foreach ($names as $n){
            $redis->rpush($n, $json_en);
        }

        $a = $redis->lrange('apar', 0, -1);
        $arr = [];
        foreach ($a as $aa){
            array_push($arr, json_decode($aa));
        }
        return $arr;

    }

    public function search(Request $request){
//        return $request;
        $key = $request->keyword;
        $redis = Redis::connection();

        $keys = explode(' ', $key);
        $range = $redis->sunion(keys);
//        return $range;
//        if($range!=null){
            $arr = [];
            foreach ($range as $r){
                array_push($arr, json_decode($redis->sinter($r)[0]));
            }

            return response()->json([
                'data'=>$arr
            ]);
//        }

//        $properties = properties::where('name', 'like', '%'.$key.'%')
//            ->orderByDesc('total_views')
//            ->paginate(5);

//        return $properties;


//        return $properties;
    }



    public function testt(Request $request){
        $keyword = $request->keyword;

        $redis = Redis::connection();
        return $redis->get($keyword);


    }

}
