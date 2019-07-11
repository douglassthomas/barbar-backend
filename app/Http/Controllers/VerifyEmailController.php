<?php

namespace App\Http\Controllers;

use App\Mail\emailTemplate;
use App\User;
use App\VerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Ramsey\Uuid\Uuid;

class VerifyEmailController extends Controller
{
    //
    public function verifyUser(Request $request){

        $user = User::where('email', $request->email)->get()->first();
        if($user->email_verified_at!=null){
            echo("verified");
            return;
        }


        $verified = VerifyEmail::create([
           'id'=>Uuid::uuid4()->getHex(),
            'user_id'=>$user->id,
            'email'=>$user->email,
            'token'=>Uuid::uuid4()->getHex()
        ]);

        Mail::to($user->email)->send(new emailTemplate($user));
    }



}
