<?php

namespace App\Http\Controllers;

use App\Http\Requests\PremiumRequest;
use App\Mail\checkout;
use App\Mail\emailTemplate;
use App\Premium;
use App\PremiumTransaction;
use App\User;
use App\VerifyEmail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Ramsey\Uuid\Uuid;

class PremiumController extends Controller
{
    //
    public function insertPremium(PremiumRequest $request){
        $id = $request->id;
        $day = $request->day;
        $price = $request->price;


        $premium = new Premium();
        $premium->id = Uuid::uuid4();
        if($id!=null){
            $premium = Premium::where('id', $id)->get()[0];
        }
        $premium->day = $day;
        $premium->price = $price;
        $premium->save();

        return 'success';
    }

    public function getPremium(Request $request){
        if($request->no_paginate!=null){

            return Premium::orderByDesc('day')->get();
        }
        return Premium::orderByDesc('day')->paginate(10);
    }

    public function deletePremium(Request $request){
        $id = $request->id;
        $premium = Premium::where('id', $id)->get()[0];
        $premium->delete();

        return "success";
    }

    public function addPromo(Request $request){
        $id = $request->id;
        $promo = $request->promo;

        $premium = Premium::where('id', $id)->get()[0];
        $premium->promo = $promo;
        $premium->save();

        if($promo==null) return "success delete promo";
        return "success add promo";
    }

    public function deletePromo(Request $request){
        $id = $request->id;

        $premium = Premium::where('id', $id)->get()[0];
        $premium->promo = null;
        $premium->save();

        return "success delete pormo";
    }

    public function getUserPremiumDate(Request $request){
        $user_id = $request->user_id;
        $date_now = date("Y-m-d");

        $user = User::where('id', $user_id)->get()[0];
        if($user->start_premium == null){
            return response()->json([
                'start'=>'-',
                'end'=>'-'
            ]);
        }
        else if($user->end_premium < $date_now){
            return response()->json([
                'start'=>'-',
                'end'=>'-'
            ]);
        }

        $start_date = date("F jS, Y", strtotime($user->start_premium));
        $end_date = date("F jS, Y", strtotime($user->end_premium));

        return response()->json([
           'start'=>$start_date,
           'end'=>$end_date
        ]);
    }

    public function checkout(Request $request){
//        return $request;
        $user_id = $request->user_id;
        $day = $request->day;
        if($day==null) return 'error duration';

        $transaction = new PremiumTransaction();
        $transaction->id = Uuid::uuid4();
        $transaction->user_id = $user_id;
        $transaction->day = $day;
        $transaction->price = $request->price;
        $transaction->end_date = Carbon::parse(now())->addDays($day);
        $transaction->save();

        $user = User::where('id', $user_id)->get()->first();
        $user->start_premium = now();
        if($user->end_premium == null){
            $user->end_premium = now()->addDays($day);
        }else{
            $end = Carbon::parse($user->end_premium);
            $end->addDays($day);
            $user->end_premium = $end;
        }
        $user->save();

        $full_invoice_path = '';
        $file_name = '';

        if($request->invoice!=null){
            $invoice = base64_decode($request->invoice);
            $name = $user_id.now().'.'.'pdf';
            $invoice_path = public_path().'/invoices/';//.'/storage/banner';
//            $invoice->move($invoice_path, $name);
            file_put_contents( $invoice_path.$name, $invoice);
            $full_invoice_path = $invoice_path.$name;
            $file_name = 'http://localhost:8000/invoices/'.$name;
        }

        Mail::to($user->email)->send(new checkout($user, $full_invoice_path, $request->day, 'Rp '.$request->price), $user);

        return 'success';
    }

    public function getMyPremium(Request $request){
        $id = $request->user_id;

        return PremiumTransaction::where('user_id', $id)->paginate(10);
    }
}
