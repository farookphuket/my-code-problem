<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;


use Mail;
use App\Mail\RegisterMail;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;

use DB;
use Session;

class RegisterController extends Controller
{
    protected $user_token_table = "personal_access_tokens";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function getYourFriends(){
        $users = User::where("is_admin","!=",1)
                    ->where("email_verified_at","!=",null)
                    ->latest()
                        ->paginate(4);

        return response()->json([
            "users" => $users
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
    public function store()
    {
        $valid = request()->validate([
            "name" => ["required","unique:users,name"],
            "email" => ["required","email","unique:users,email"],
            "password" => ["required","min:4"]
        ]);

        // prepare data
        $valid["password"] = bcrypt(request()->password);
        $valid["avatar"] = "img/default.jpeg";

        // create new user 
        User::create($valid);

        // get the last create user
        $user = User::latest()->first();

        // get the role and set new user to member role
        $role = Role::where("role_name","member")->first();
        $user->role()->attach($role);

        // create token for activation link
        $user->createToken('auth_token')->plainTextToken;

        $tk = DB::table($this->user_token_table)
                        ->where("tokenable_id",$user->id)
                        ->first();

        Session::put("USER_HAS_ACTIVATION_TOKEN",$tk->token);
         
        if(Session::has("USER_HAS_ACTIVATION_TOKEN")):
            // sent email to new user         
            $this->sentActivationLinkEmail();
        endif;

        $msg = "<span class=\"has-text-success has-text-weight-bold\">
            Success ,you've registered!</span>";
        return response()->json([
            "msg" => $msg,
            "token" => $tk->token
        ]);
    }

    public function sentActivationLinkEmail(){
        // get the last create user
        $user = User::latest()->first();

        $email = $user->email;

        // website 
        $website =  request()->getHttpHost();

        if(Session::has("USER_HAS_ACTIVATION_TOKEN")):

        $get_token = Session::get("USER_HAS_ACTIVATION_TOKEN");


        $url = URL::to('/activated/'.$get_token);


        // use password resets table to compare the token key
        DB::table('password_resets')
            ->insert([
                "email" => $email,
                "token" => $get_token,
                "created_at" => now()
            ]);

        /*
         * this code is not working
        $email_data = [
            "title" => 'please re-confirm your request on register at '.$website,
            "website" => $website,
            "code" => $get_token,
            "url" => $url,
            "name" => $user->name,
            "registered_at" => date("d-m-Y H:i:s",time())
        ];
         */
        /*
        Mail::to($user->email)
                ->send(new RegisterMail($email_data));
         */
        $user["website"] = $website;

        $data = array(
            "name" => $user['name'],
            "title" => 'Please confirm is this you?',
            "link" => $url,
            "website" => $website,
            "code" => $get_token,
            "registered_at" => date("d-m-Y H:i:s",time())
        );
        Mail::send('mail.user-activation-email',$data,function($msg) use ($user){
            $msg->from('no-reply@'.request()->getHttpHost());
            $msg->to($user['email'],'no-reply-back')->subject("Dear 
            {$user['name']} please confirm your account at {$user['website']}!
            ");
        });

        endif;

    }

    public function activated($token){
        return $token;
    }

    public function activateUser($token){

        $u = DB::table("password_resets")
                    ->where("token",$token)
                    ->whereDate("created_at","=",date("Y-m-d"))
                    ->first();
        $msg = "";
        $status_code = 0;
        if($u):
            User::where("email",$u->email)
                ->update([
                    "email_verified_at" => now(),
                    "updated_at" => now()
                ]);

            // backup user
            $u = User::latest()->first();
            User::backupUser($u->id,"insert");

            $msg = "<span class=\"has-text-success has-text-weight-bold\">
            Success your account has been activated</span>";
            $status_code = 1;
        else:
            $msg = "<span class=\"has-text-danger has-text-weight-bold\">
            Error link has expired!</span>";
        endif;

        return response()->json([
            "msg" => $msg,
            "status_code" => $status_code
        ]);
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
        //
    }
}
