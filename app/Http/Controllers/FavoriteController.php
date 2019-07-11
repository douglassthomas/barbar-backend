<?php

namespace App\Http\Controllers;

use App\Favorite;
use App\properties;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class FavoriteController extends Controller
{
    //

    public function addFavorite(Request $request){
//        return $request;
        $user_id = $request->user_id;
        $property_id = $request->property_id;

        $favorite = new Favorite();
        $favorite->id = Uuid::uuid4();
        $favorite->user_id = $user_id;
        $favorite->property_id = $property_id;

        $favorite->save();

        return 'success';
    }

    public function getFavoriteByUserId(Request $request){
//        return $request;
        $user_id = $request->user_id;

        return $favorite = Favorite::where('user_id', $user_id)->paginate(10);
//        $properties = [];
//        foreach ($favorite as $f){
//            $property = properties::where('id', $f->property_id)->get()[0];
//            array_push($properties, $property);
//        }

//        return $properties;
    }

    public function deleteFavorite(Request $request){
        $user_id = $request->user_id;
        $property_id = $request->property_id;

        $favorite = Favorite::where('user_id', $user_id)->where('property_id', $property_id)->get()[0];
        $favorite->delete();

        return 'success';
    }

    public function checkFavorite(Request $request){
//        return $request;
        $user_id = $request->user_id;
        $property_id = $request->property_id;

        $favorite = Favorite::where('user_id', $user_id)->where('property_id', $property_id)->get();
//        return count($favorite);
        if(sizeof($favorite)>0) return 'true';
        else return 'false';
    }
}
