<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use Auth;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $perpage = request()->perpage;
        $post = $this->getAs($perpage);
        return response()->json([
            "post" => $post
        ]);
    }

    public function getAs($perpage = 4){
        $post = '';

        if(Auth::user() && Auth::user()->is_admin):
            // root will get all post 
            $post = Post::latest()
                    ->with("read")
                    ->with('comment')
                    ->paginate($perpage); 

        elseif(Auth::user()):
            $post = Post::where('is_public','!=',0)
                        ->orWhere('user_id',Auth::user()->id)
                        ->latest()
                        ->with("read")
                        ->with("comment")
                        ->paginate($perpage);
        else:
            $post = Post::where('is_public','!=',0)
                    ->with('comment')
                    ->latest()
                    ->with("read")
                    ->paginate($perpage);
        endif;
        return $post;
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
        // validate data
        $valid = request()->validate([
            "title" => ["required","min:8","max:80"],
            "excerpt" => ["required","min:8","max:200"],
        ]);

        // make cover if file upload
        $cover = $this->makeCover();

        // make slug
        $slug = rtrim(request()->slug,"-");

        // 
        $is_public = !request()->is_public?0:1;

        // prepare data
        $valid["user_id"] = Auth::user()->id;
        $valid["is_public"] = $is_public;
        $valid["slug"] = $slug;
        $valid["cover"] = $cover;
        $valid["title"] = xx_clean(request()->title);
        $valid["excerpt"] = xx_clean(request()->excerpt);
        $valid["body"] = xx_clean(request()->body);

        // create post 
        Post::create($valid);

        // get the last row 
        $po = Post::latest()->first();

        // make backup 
        Post::makeBackup($po->id,"insert");

        // return result
        $msg = "<span class=\"has-text-success has-text-weight-bold\">
Success! your post has been created.
            </span>";
        return response()->json([
            "msg" => $msg
        ]);
    }


    public function makeCover($id=false){
        $file_name = "/img/no_image.jpeg";

        // image url 
        $img_url = request()->cover_url;
        // upload to folder 
        $upload_to_folder = public_path("USER_UPLOAD/POST");

        $old_cover = "";
        if($id != false):
            // edit case
            $wp = Post::find($id);
            $old_cover = $wp->cover;

            // upload file 
            if(request()->hasFile('cover_upload')):
                $new_name = Auth::user()->email."_";
                $new_name .=  date("Y-m-d")."_"; 
                $new_name .= request()->file('cover_upload')
                                      ->getClientOriginalName();

                // move upload file to upload folder 
                request()->file('cover_upload')
                         ->move($upload_to_folder,$new_name);

                // set upload file name 
                $file_name = "/USER_UPLOAD/POST/".$new_name;

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
            if(request()->hasFile('cover_upload')):
                $new_name = Auth::user()->email."_";
                $new_name .=  date("Y-m-d")."_"; 
                $new_name .= request()->file('cover_upload')
                                      ->getClientOriginalName();

                // move upload file to upload folder 
                request()->file('cover_upload')
                         ->move($upload_to_folder,$new_name);

                // set upload file name 
                $file_name = "/USER_UPLOAD/POST/".$new_name;
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
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $p = Post::where('slug',$slug)
                    ->with("read")
                    ->with("comment")
                    ->first();

        // read count
        Post::hasRead($p->id);


        $comm = $this->getCommentPost($p->id);
        $re = Comment::postCommentWithReply($p->id);

        return response()->json([
            "post" => $p,
            "comment" => $comm,
            "reply" => $re
        ]);
    }

    public function getCommentPost($id){
        $perpage = request()->perpage;

        $po = Post::find($id)
                ->comment()
                ->paginate($perpage);
        return $po;
                
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        $post = Post::find($post->id);
        return response()->json([
            "post" => $post
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Post $post)
    {
        // validate data
        $valid = request()->validate([
            "title" => ["required","min:8","max:80"],
            "excerpt" => ["required","min:8"],
        ]);

        // make cover if file upload
        $cover = $this->makeCover($post->id);

        // make slug
        $slug = rtrim(request()->slug,"-");

        // 
        $is_public = !request()->is_public?0:1;

        // prepare data
        $valid["is_public"] = $is_public;
        $valid["slug"] = $slug;
        $valid["cover"] = $cover;
        $valid["title"] = xx_clean(request()->title);
        $valid["excerpt"] = xx_clean(request()->excerpt);
        $valid["body"] = xx_clean(request()->body);
        $valid["updated_at"] = now();
        // create post 
        Post::where("id",$post->id)
            ->update($valid);

        // return result
        $msg = "<span class=\"has-text-success has-text-weight-bold\">
Success! your post {$post->id} has been updated.
            </span>";
        return response()->json([
            "msg" => $msg
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        $del = Post::find($post->id);
        $del->delete();

        // return result
        $msg = "<span class=\"has-text-success has-text-weight-bold\">
Success! your post {$post->id} has been deleted.
            </span>";
        return response()->json([
            "msg" => $msg
        ]);
    }
}
