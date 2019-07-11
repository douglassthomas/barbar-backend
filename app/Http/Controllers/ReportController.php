<?php

namespace App\Http\Controllers;

use App\properties;
use App\report;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class ReportController extends Controller
{
    //
    public function getAllReport(Request $request){
//        return $request;
        $type = $request->type?$request->type:'';
        $date = $request->date?$request->date:'';

        $report = report::where('created_at', 'like', '%');

        if($type!=null){
            $report->where('type', 'like', '%'.$type);
        }
        if($date!=null){
            $report->where('created_at', 'like', $date.'%');
        }

        return $report->orderByDesc('created_at')->paginate(10);
    }

    public function goReport(Request $request){
//        return $request;
        $property_id = $request->property_id;
        $user_id = $request->user_id;
        $contents = $request->contents;

        $property = properties::where('id', $property_id)->first();
//        return $property->propertiable_type;

        $report = new report();
        $report->id = Uuid::uuid4();
        $report->property_id = $property_id;
        $report->user_id = $user_id;
        $report->type = explode('\\', $property->propertiable_type)[1];
        $report->contents = $contents;

        $report->save();

        return "success report a property";
    }
}
