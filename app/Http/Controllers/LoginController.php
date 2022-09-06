<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;


use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Auth;


use Session;
use DB;

class LoginController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function checkUserPassport(){
        $login = false;
        $user = "";
        $token = "";

        $login_id = request()->login_id;
        $login_token = request()->login_token;

        if($login_token != null && $login_id != null):
            $ck = DB::table("personal_access_tokens")
                    ->where("tokenable_id",$login_id)
                    ->where("token",$login_token)
                    ->first();
            $token = $ck->token;
            $user = User::find($login_id);
        endif;
        return response()->json([
            "id" => $login_id,
            "token" => $login_token,
            "check_token" => $token,
            "check_user" => $user
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LoginRequest $request)
    {
        $valid = request()->validate([
            "email" => ["required","email"],
            "password" => ["required"]
        ]);

        // return back data
        $name = '';
        $url = '';
        $token = '';
        $user = '';
        $get_tk = '';
        $u_id = 0;
        if(!Auth::attempt($valid)):
            // login fail

            $msg = "<span style=\"font-weight:bold;color:red; 
            \">Sorry,login fail!</span>";
            $url = '/login';
        else:
            $user = Auth::user();
            if(!$this->isUserHasActivate($user)):
                $url = '/activation-errors';
                $msg = "<span style=\"font-weight:bold;color:red;\"> 
Error! cannot log you in activation code error</span>";
                
            else:

                // login true then set the session 
                $name = Auth::user()->name;
                $url = $this->userSetUrl();

                // create token
                Auth::user()->createToken('auth_token')->plainTextToken;


                $request->authenticate();

                //$request->session()->regenerate();

                // get the last key then sent key to user 
                $get_tk = DB::table("personal_access_tokens")
                                ->latest()
                                ->first();
                $token = $get_tk->token;
               // foreach($get_tk as $t):
               //     $token = $t->token;
               // endforeach;
                $u_id = Auth::user()->id;

                $msg = "<span style=\"font-weight:bold;color:green;\">Welcome {$name}</span>";
            endif;

        endif;



        return response()->json([
            "msg" => $msg,
            "url" => $url,
            "login_token" => $token,
            "login_user_id" => $u_id,
            "user" => $user
        ]);
    }

    public function userSetUrl(){
        $u = User::find(Auth::user()->id);

        $root = false;
        $member = false;

        $url = "/member";

        if($u->is_admin != 0):
            $url = "/admin";
            $root = true;
            Session::put("USER_IS_ADMIN",$root);
        else:
            $member = true;
            Session::put("USER_IS_MEMBER",$member);
        endif;

        return $url;

    }

    public function isUserHasActivate(User $user){

        $u = User::where('id',$user->id)
                    ->where("email_verified_at","!=",null)
                    ->first();
        if($u):
            return true;
        else:
            return false;
        endif;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        request()->session()->flush();
        Session::regenerate();
    }
}
