<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Read;
use DB;

class Post extends Model
{
    use HasFactory;

    protected static $post_table = "posts";
    protected static $read_table = "reads";
    protected static $read_pip_table = "post_read";

    protected $fillable = [
        "user_id",
        "title",
        "slug",
        "cover",
        "excerpt",
        "body",
        "is_public"
    ];

    protected $with = ["user"];


    public function user(){
        return $this->belongsTo(User::class);
    }

    public function comment(){
        return $this->belongsToMany(Comment::class)->orderBy("created_at","DESC");
    }

    public function read(){
        return $this->belongsToMany(Read::class);
    }


    /* ============= backup Post START */
    public static function makeBackup($id,$cmd=false){
        // table 
        $table = static::$post_table;

        // file 
        $file = base_path("DB/post_list.sqlite");

        // data 
        $data = Post::find($id);

        // command 
        $command = '';

        // switch command 
        switch($cmd):
        case"insert":
            $command = "
/* ========================= backup INSERT POST {$id} ========================
 * on ".date("Y-m-d H:i:s")."
 * */
INSERT INTO `{$table}`(`user_id`,`title`,`cover`,`slug`,`excerpt`,`body`,
`is_public`,`created_at`,`updated_at`) VALUES(
    '{$data->user_id}',
    '{$data->title}',
    '{$data->cover}',
    '{$data->slug}',
    '{$data->excerpt}',
    '{$data->body}',
    '{$data->is_public}',
    '{$data->created_at}',
    '{$data->updated_at}');
";
        break;
case"update":
    $command = "
/* ========================= backup UPDATE POST {$id} =========================
 * on ".date("Y-m-d H:i:s")."
 * */
UPDATE `{$table}` SET title='{$data->title}',
cover='{$data->cover}',
cover='{$data->slug}',
cover='{$data->excerpt}',
cover='{$data->body}',
cover='{$data->is_public}',
cover='{$data->updated_at}' WHERE id='{$id}';
";
        break;
default:
    $command = "
/* ========================= DEFAULT DELETE Command for POST {$id} =============
 * on ".date("Y-m-d H:i:s")."
 * command will not be execute to avoid ERROR on restore data
 * */
/* DELETE FROM `{$table}` WHERE id='{$id}'; */
";
    break;
    endswitch;
    write2text($file,$command);
    }
    /* ============= backup Post END */

    public static function hasRead($id){
        // read table 
        $r_table = static::$read_table;

        // pip table 
        $pip_table = static::$read_pip_table;

        // today 
        $today = date("Y-m-d");

        // get read count from pip table 
        $check = DB::table($pip_table)->where("ip",getUserIp())
                                ->whereDate("created_at","=",$today)
                                ->where("post_id",$id)
                                ->get()
                                ->count();
        if($check == 0):
            // new read data
            $r_data = [
                "ip" => getUserIp(),
                "os" => getUserOs(),
                "browser" => getUserBrowser(),
                "device" => getUserDevice()
            ];
            Read::create($r_data);

            // get the last read data to make pip data 
            $re = Read::latest()->first();

            // backup read data 
            Read::makeBackup($re->id);

            // pip data 
            $pip_data = [
                "post_id" => $id,
                "read_id" => $re->id,
                "ip" => getUserIp(),
                "created_at" => now(),
                "updated_at" => now()
            ];
            DB::table($pip_table)->insert($pip_data);

            static::backupReadLink($id);
        endif;
    }

    public static function backupReadLink($post_id){
        // table 
        $table = static::$read_pip_table;

        // file 
        $file = base_path("DB/read_post_link.sqlite");

        // data 
        $data = DB::table($table)->where("post_id",$post_id)
                        ->whereDate("created_at",date("Y-m-d"))
                        ->where("ip",getUserIp())
                        ->first();
        $command = "
/* ==================== backup read link for POST {$post_id} ==================
 * on ".date("Y-m-d H:i:s")."
 * */
INSERT INTO `{$table}`(`read_id`,`post_id`,`ip`,`created_at`,`updated_at`) 
VALUES(
'{$data->read_id}',
'{$data->post_id}',
'{$data->ip}',
'{$data->created_at}',
'{$data->updated_at}');
";
        write2text($file,$command);
    }

}
