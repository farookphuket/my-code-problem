<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Post;
use DB;

class Comment extends Model
{
    use HasFactory;

    protected static $post_link_table="comment_post";
    protected static $comment_table="comments";

    protected $fillable = [
        "title","body","is_approved","user_id"
    ];

    protected $with = ["user"];

    public function post(){
        return $this->belongsToMany(Post::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function reply(){
        return $this->belongsToMany(Reply::class)->orderBy("created_at","DESC");
    }

    public static function postCommentWithReply($post_id){
        $po = Post::find($post_id);
        $comm = Comment::with(["post" => fn($q) => $q->where('id',$po->id)])
                        ->with("reply")
                        ->latest();
        return $po;
    }

    public static function backupComment($id,$cmd=false){
        // comment table
        $table = static::$comment_table;

        // file 
        $file = base_path("DB/comment_list.sqlite");

        // data 
        $co = Comment::find($id);

        $command = "";

        switch($cmd):
        case"insert":
            $command = "
/* ========================== insert comment id '{$id}' =======================
 * on ".date("Y-m-d H:i:s a")."
 * */
INSERT INTO `{$table}`(`user_id`,`title`,`body`,`is_approved`,`created_at`,
`updated_at`) VALUES(
    '{$co->user_id}',
    '{$co->title}',
    '{$co->body}',
    '{$co->is_approved}',
    '{$co->created_at}',
    '{$co->updated_at}');";
            static::backupCommentLink($id);
            break;
        case"update":
            $command = "
/* ============================ update comment for {$id} ======================
 * on ".date("Y-m-d H:i:s a")."
 * */
UPDATE `{$table}` SET title='{$co->title}',
body='{$co->body}',
is_approved='{$co->is_approved}',
created_at='{$co->updated_at}' WHERE id='{$id}';";
        break;
        default:
        $command = "
/* ============================ update comment for {$id} ======================
 * on ".date("Y-m-d H:i:s a")."
 * */
/* DELETE FROM `{$table}` WHERE id='{$id}';*/
";

        break;
        endswitch;

        // write 
        write2text($file,$command);
    }

    public static function backupCommentLink($comment_id){
        // table 
        $table = static::$post_link_table;

        // file 
        $file = base_path("DB/comment_post_link.sqlite");

        // data 
        $po = DB::table($table)
                    ->where('comment_id',$comment_id)
                    ->get();
        $command = "";

        if(count($po) > 1):
            $command = "
/* ===================== '{$comment_id}' delete old item ==========================
 * on ".date("Y-m-d H:i:s a")."
 * */
DELETE FROM `{$table}` WHERE comment_id='{$comment_id}';
";
        endif;
        foreach($po as $item):
            $command = "
/* ================== '{$comment_id}' insert item =============================== 
 * on ".date("Y-m-d H:i:s a")."
 * */
INSERT INTO `{$table}` (`post_id`,`comment_id`) 
VALUES(
    '{$item->post_id}',
    '{$item->comment_id}');";
        endforeach;

        write2text($file,$command);
    }

}
