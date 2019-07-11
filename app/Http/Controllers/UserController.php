<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    //
    public function getAllUser(Request $request){
//        return $request;
        $email = $request->email;
        $phone = $request->phone;


        $type = $request->type;
        $user = User::where('type', $type);

        if($email!='all'){
            if($email=='verified'){
                $user->whereNotNull('email_verified_at');
            }else{
                $user->whereNull('email_verified_at');
            }
        }
        if($phone!='all'){
            if($phone=='verified'){
                $user->whereNotNull('phone_verified_at');
            }else{
                $user->whereNull('phone_verified_at');
            }
        }

        return $user->paginate(10);


    }

    public function banUser(Request $request){
        $id = $request->id;

        $user = User::where('id', $id)->first();
        if($user->status=="banned"){
            $user->status = "ok";
        }else{
            $user->status = "banned";
        }

        $user->save();

        return "success";
    }

    public function deleteUser(Request $request){
        $id = $request->id;

        $user = User::where('id', $id)->first();
        $user->delete();

        return "user deleted successfully";
    }

    public function resetPasswordUser(Request $request){
        $id = $request->id;

        $user = User::where('id', $id)->first();

        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';


        $newpass = explode(' ', $user->name)[0].substr(str_shuffle($permitted_chars), 0, 5);
        $user->password = bcrypt($newpass);
        $user->save();

        return "new password: ".$newpass;
    }
}
