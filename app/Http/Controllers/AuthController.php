<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use JWTAuth;
use JWTAuthExceptio;

class AuthController extends Controller
{
    //
    public function store(Request $request){

        $this->validate($request, [
            'name'=>'required',
            'email'=> 'required|email',
            'password'=>'required|min:5'
        ]);

        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');

        $credentials = [
            'email' => $email,
            'password' => $password
        ];

    }




}
