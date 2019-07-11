<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    //
    public function send(Request $request){
        $data = array('name'=>"Brisia Jodie");

        Mail::send(['text'=>'mail'], $data, function ($message){
           $message->to('dougitu@gmail.com', 'No Reply Barbarkos')->subject
           ('Email Verification - Barbarkos');
           $message->from('barbarkos.noreply@gmail.com', 'noreply team');
        });
        echo "Basic Email sent. Check your box";
    }

}
