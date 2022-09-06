<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Comment;

use DB;

class Reply extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id","comment_id",
        "title","body","is_approved"
    ];

    protected $with = ["user"];

    protected static $reply_table = "replies";
    protected static $reply_link_table = "comment_reply";

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function comment(){
        return $this->belongsToMany(Comment::class);
    }


    public static function backupReply($id,$cmd=false){
        //table 
        $table  = static::$reply_table;

        // file 
        $file = base_path("DB/reply_list.sqlite");

        // data 
        $re = Reply::find($id);

        // command 
        $command = "";


    }

    public static function backupReplyLink($reply_id){
        //
    }


}
