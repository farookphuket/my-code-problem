<?php

namespace App\Http\Controllers;

use App\Models\Whatup;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;

use Auth;

class WhatupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $whatup = $this->getWhatup();

        return response()->json([
            "whatup" => $whatup
        ]);
    }

    public function getWhatup(){
        $perpage = request()->perpage;

        $wp = '';
        
        if(Auth::user()):
            $wp = Whatup::where('is_public','!=',0)
            ->orWhere('user_id',Auth::user()->id)
            ->latest()
            ->with('read')
            ->paginate($perpage);
        elseif(Auth::user() && Auth::user()->is_admin == 1):
            // admin 
            $wp = Whatup::latest()
            ->with('read')
            ->paginate($perpage);
        else:
            
            $wp = Whatup::where('is_public','!=',0)
            ->latest()
            ->with("read")
            ->paginate($perpage);
        endif;

        return $wp;

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
            "wp_title" => ["required","min:8"],
            "wp_excerpt" => ["required","min:14"],
            "wp_body" => ["required","min:14"],
        ],[
            "wp_title.required" => "Error! title is required.",
            "wp_title.min" => "Error! title is too short.",
            "wp_excerpt.required" => "Error! the excerpt field required ",
            "wp_excerpt.min" => "Error! the excerpt field required ",
        ]);


        // cover image 
        $cover = $this->makeCover();

        // prepare data to create 
        $valid["user_id"] = Auth::user()->id;
        $valid["wp_title"] = xx_clean(request()->wp_title);
        $valid["wp_excerpt"] = xx_clean(request()->wp_excerpt);
        $valid["wp_body"] = xx_clean(request()->wp_body);
        $valid["is_public"] = request()->is_public?1:0;
        $valid["wp_cover"] = $cover;

        // create whatup
        Whatup::create($valid);

        // get the last row to make backup 
        $wp = Whatup::latest()->first();

        Whatup::makeBackup($wp->id,"insert");


        $msg = "<span class=\"has-text-weight-bold has-text-success\">
Success your post has been save</span>";

        return response()->json([
            "msg" => $msg
        ]);
    }

    public function makeCover($id=false){
        $file_name = "/img/no_image.jpeg";

        // image url 
        $img_url = request()->wp_img_url;
        // upload to folder 
        $upload_to_folder = public_path("USER_UPLOAD/WP");

        $old_cover = "";
        if($id != false):
            // edit case
            $wp = Whatup::find($id);
            $old_cover = $wp->wp_cover;

            // upload file 
            if(request()->hasFile('wp_file_upload')):
                $new_name = Auth::user()->email."_";
                $new_name .=  date("Y-m-d")."_"; 
                $new_name .= request()->file('wp_file_upload')
                                      ->getClientOriginalName();

                // move upload file to upload folder 
                request()->file('wp_file_upload')
                         ->move($upload_to_folder,$new_name);

                // set upload file name 
                $file_name = "/USER_UPLOAD/WP/".$new_name;

                if(file_exists(public_path($old_cover))):
                    // delete the old file 
                    unlink(public_path($old_cover));
                endif;
            endif;

            // url image
            if(filter_var($img_url,FILTER_VALIDATE_URL)):
                $file_name = $img_url;
            endif;

        else:
            // create case 
            // upload file 
            if(request()->hasFile('wp_file_upload')):
                $new_name = Auth::user()->email."_";
                $new_name .=  date("Y-m-d")."_"; 
                $new_name .= request()->file('wp_file_upload')
                                      ->getClientOriginalName();

                // move upload file to upload folder 
                request()->file('wp_file_upload')
                         ->move($upload_to_folder,$new_name);

                // set upload file name 
                $file_name = "/USER_UPLOAD/WP/".$new_name;
            endif;

            // url image
            if(filter_var($img_url,FILTER_VALIDATE_URL)):
                $file_name = $img_url;
            endif;

        endif;

        return $file_name;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Whatup  $whatup
     * @return \Illuminate\Http\Response
     */
    public function show(Whatup $whatup)
    {
        $wp = Whatup::with('read')
            ->find($whatup->id);

        // make read count 
        Whatup::hasRead($whatup->id);

        return response()->json([
            "whatup" => $wp
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Whatup  $whatup
     * @return \Illuminate\Http\Response
     */
    public function edit(Whatup $whatup)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Whatup  $whatup
     * @return \Illuminate\Http\Response
     */
    public function update(Whatup $whatup)
    {

        $valid = request()->validate([
            "wp_title" => ["required","min:8"],
            "wp_excerpt" => ["required","min:14"],
            "wp_body" => ["required","min:14"],
        ],[
            "wp_title.required" => "Error! title is required.",
            "wp_title.min" => "Error! title is too short.",
            "wp_excerpt.required" => "Error! the excerpt field required ",
            "wp_excerpt.min" => "Error! the excerpt field required ",
        ]);


        // update cover image if need
        $cover = $this->makeCover($whatup->id);

        // prepare data to create 
        $valid["wp_title"] = xx_clean(request()->wp_title);
        $valid["wp_excerpt"] = xx_clean(request()->wp_excerpt);
        $valid["wp_body"] = xx_clean(request()->wp_body);
        $valid["is_public"] = request()->is_public?1:0;
        $valid["wp_cover"] = $cover;

        // update whatup
        Whatup::where('id',$whatup->id)
                ->update($valid);

        $msg = "<span class=\"has-text-weight-bold has-text-success\">
Success your post has been updated</span>";

        return response()->json([
            "msg" => $msg
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Whatup  $whatup
     * @return \Illuminate\Http\Response
     */
    public function destroy(Whatup $whatup)
    {
        // fine the delete target
        $del = Whatup::find($whatup->id);

        // unlink the upload pic 
        if(file_exists(public_path($del->wp_cover))):
            // delete the old file 
            unlink(public_path($del->wp_cover));
        endif;

        $del->delete();

        $msg = "Success your post has been deleted";

        return response()->json([
            "msg" => $msg
        ]);
    }
}
