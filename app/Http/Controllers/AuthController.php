<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Illuminate\Support\Facades\Cookie;
class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }
    //
    public function login(Request $request){
        

        $token = Auth::attempt($request->only("email","password"));

        if(!$token):
            return response()->json([
"message" => "Loin XXX FAIL"
],401);
        endif;

        $cookie = cookie("jwt",$token,60*24);
        return response()->json([
"user" => Auth::user(),
"token" => $token,
"access_token" => $token
])->withCookie($cookie);
    }

    public function user(){
return Auth::user();
}

    public function logout(){
        $cookie = Cookies::forget("jwt");
    
        return response()->json([
"message" => "logout !!"

])->withCookie($cookie);
    }
}
