<?php

namespace App\Http\Controllers;

use App\PremiumTransaction;
use App\User;
use Illuminate\Http\Request;

class PremiumTransactionController extends Controller
{
    //
    public function getPremiumTransaction(Request $request){
//        return $request;
        $name = $request->name;
        $date = $request->date;
        $duration = $request->duration;
        $status = $request->status;

        $premium = PremiumTransaction::join('users', 'user_id', '=', 'users.id')
            ->where('name', 'like', '%'.$name.'%')
            ->where('premium_transactions.created_at', 'like', $date.'%');

        if($duration!=null){
            $premium
                ->where('day', $duration)
                ->select('premium_transactions.*')
                ->paginate(10);
        }

        if($status=='Active'){
            $premium->whereRaw('end_date > ?', now());
        }
        else if($status=='Inactive'){
            $premium->whereRaw('end_date < ?', now());
        }


        return $premium
            ->select('premium_transactions.*')
            ->paginate(10);
    }
}
