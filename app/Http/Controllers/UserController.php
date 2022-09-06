<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

use Auth;
use DB;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $perpage = request()->perpage;
        $users = User::with('role')
                    ->latest()
                    ->paginate($perpage);
        $role = Role::all();

        return response()->json([
            "users" => $users,
            "roles" => $role
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
        //
        $valid = request()->validate([
            "name" => ["required"],
            "email" => ["required","email","unique:users,email"]
        ]);

        $roles = request()->roles;
        $verify = !request()->verified? null : date("Y-m-d H:i:s");
        $pass = request()->password;
        if(!$pass):
            $pass = "1234";
        endif;

        $valid["password"] = bcrypt($pass);
        $valid["email_verified_at"] = $verify;
        $valid["avatar"] = "img/default.jpeg";

        // create user 
        User::create($valid);

        // get the last row 
        $u = User::latest()->first();
        $u->role()->attach($roles);

        $msg = "<span class=\"has-text-weight-bold has-text-success\">
Success user has been created</span>";

        return response()->json([
            "msg" => $msg,
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
        $user = User::with('role')
                    ->where("id",$user->id)
                    ->first();
        return response()->json([
            "user" => $user
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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(User $user)
    {
        //
        $valid = request()->validate([
            "name" => ["required","unique:users,name,".$user->id],
            "email" => ["required","email","unique:users,email,".$user->id]
        ]);

        $roles = request()->roles;

        $verify = null;
        // no verify set will be null
        if(request()->verified == true && request()->verified != 0):
            $verify = date("Y-m-d");
        else:
            User::where("id",$user->id)
                ->update([
                    "email_verified_at" => null
                ]);
        endif;
        $valid["email_verified_at"] = $verify;
        $pass = request()->password;
        if(!$pass):
            unset($valid["password"]);
        else:
            $valid["password"] = bcrypt($pass);
        endif;

        $valid["updated_at"] = now();

        // update user 
        User::where("id",$user->id)
                ->update($valid);

        // get the last row 
        $u = User::find($user->id);
        $u->role()->sync($roles);

        $msg = "<span class=\"has-text-weight-bold has-text-success\">
Success the user id {$user->id} has been created</span>";

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
    public function destroy(User $user)
    {
        //
    }
}
