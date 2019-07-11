<?php

namespace App\Http\Controllers;

use App\History;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class HistoryController extends Controller
{
    //
    public function addHistory(Request $request){
//        return $request;
        $user_id = $request->user_id;
        $property_id = $request->property_id;

        $check = History::where('user_id', $user_id)->where('property_id', $property_id)->get();
        if(sizeof($check)>0) $check[0]->delete();

        $history = new History();
        $history->id = Uuid::uuid4();
        $history->user_id = $user_id;
        $history->property_id = $property_id;

        $history->save();

        return 'success';
    }

    public function getHistoryByUserId(Request $request){
        $user_id = $request->user_id;

        return History::where('user_id', $user_id)->paginate(10);
    }


}
