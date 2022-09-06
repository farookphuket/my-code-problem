<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Auth;
use Session;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = '';
        if(Auth::check()):
            $user = User::with('role')
                        ->where('id',Auth::user()->id)
                        ->first();
        else:
            $user = null;
        endif;
        return response()->json([
            "user" => $user
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
    public function store(Request $request)
    {
        //
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
        $u = User::with('role')
                ->where('id',$user->id)
                ->first();
        return response()->json([
            "user" => $u
        ]);
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

    public function userConfirm(){
        $user = '';
        $valid = request()->validate([
            "password_confirm" => ["required"]
        ]);

        $msg = "";
        $res = 0;
        if(Hash::check(request()->password_confirm,Auth::user()->password)):
            $msg = "<span class=\"has-text-success\">success</span>";
            $res = 1;
        else:
            $msg = "<span class=\"has-text-danger\">wrong password</span>";
        endif;
        return response()->json([
            "msg" => $msg,
            "res" => $res
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        //
        $valid = request()->validate([
            "email" => ["required","email","unique:users,email,".Auth::user()->id],
            "name" => ["required","min:4","unique:users,name,".Auth::user()->id]
        ]);

        $pass = request()->password;
        if(!empty(request()->password)):
            $valid["password"] = bcrypt($pass);
        else:
            unset($valid["password"]);
        endif;

        $valid["updated_at"] = now();
        $valid["email"] = request()->email;
        // 
        User::where("id",Auth::user()->id)
            ->update($valid);

        $msg = "<span class=\"has-text-success has-text-weight-bold\">
            Success your profile has updated</span>";
        return response()->json([
            "msg" => $msg
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $del = User::find($id);
        $del->delete();
        request()->session()->flush();
        Session::regenerate();
    }
}
