<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Read;
use DB;

class Whatup extends Model
{
    use HasFactory;

    // whatup table 
    protected static $wp_table = "whatups";

    // read table 
    protected static $read_table = "reads";

    // read pip table 
    protected static $read_pip_table = "read_whatup";

    protected $fillable = [
        "wp_title",
        "wp_excerpt",
        "wp_body",
        "user_id",
        "is_public",
        "wp_cover",
    ];

    protected $with = ["user"];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function read(){
        return $this->belongsToMany(Read::class);
    }

    public static function makeBackup($id=false,$cmd=false){
        // table
        $table = static::$wp_table;

        // file 
        $file = base_path("DB/whatup_list.sqlite");

        // data 
        $wp = Whatup::find($id);

        // command 
        $command = "";

        // switch case 
        switch($cmd):
        case"insert":
            $command = "
/* ============================ INSERT ===================================
 * on ".date("Y-m-d H:i:s")."
 * */
INSERT INTO `{$table}`(`user_id`,`is_public`,`wp_title`,`wp_cover`,
`wp_excerpt`,`wp_body`,`created_at`,`updated_at`) VALUES(
    '{$wp->user_id}',
    '{$wp->is_public}',
    '{$wp->wp_title}',
    '{$wp->wp_cover}',
    '{$wp->wp_excerpt}',
    '{$wp->wp_body}',
    '{$wp->created_at}',
    '{$wp->updated_at}');
";
            break;
case"update":
    $command = "
/* ============================ Update ===================================
 * on ".date("Y-m-d H:i:s")."
 * */
UPDATE `{$table}` SET wp_title='{$wp->wp_title}',
wp_cover='{$wp->wp_cover}',
is_public='{$wp->is_public}',
wp_excerpt='{$wp->wp_excerpt}',
wp_body='{$wp->wp_body}',
updated_at='{$wp->updated_at}'
WHERE id='{$id}'; 
";
    break;
default:
    $command = "
/* ============================ Default Delete  ===============================
 * on ".date("Y-m-d H:i:s")."
 * */
/* DELETE FROM `{$table}` WHERE id='{$id}'; */
/* The delete command will not execute as just to prevent the script from Error */
";
    break;
            endswitch;

            // write to file 
            write2text($file,$command);
    }

    public static function hasRead($wp_id){

        // read table 
        $r_table = static::$read_table;

        // pip table 
        $pip_table = static::$read_pip_table;

        // today 
        $today = date("Y-m-d",time());

        $check = DB::table($pip_table)
                    ->where("whatup_id",$wp_id)
                    ->where("ip",getUserIp())
                    ->whereDate("created_at","=",$today)
                    ->get()
                    ->count();
        if($check == 0):
            $read_data = [
                "ip" => getUserIp(),
                "os" => getUserOs(),
                "browser" => getUserBrowser(),
                "device" => getUserDevice(),
                "created_at" => now(),
                "updated_at" => now()
            ];
            // create new read detail 
            Read::create($read_data);

            // get the last row
            $re = Read::latest()->first();

            // backup read of reads table
            Read::makeBackup($re->id);

            // create read pip table 
            $pip_data = [
                "read_id" => $re->id,
                "whatup_id" => $wp_id,
                "ip" => getUserIp(),
                "created_at" => now(),
                "updated_at" => now()
            ];
            // attatch function will not insert ip created_at field so use 
            // Db instead
            DB::table($pip_table)->insert($pip_data);

            // then backup the read count link
            static::backupReadLink($wp_id);
        endif;
    }

    public static function backupReadLink($wp_id){
        // table 
        $table = static::$read_pip_table;

        // file 
        $file = base_path("DB/read_wp_link.sqlite");

        // data 
        $r_data = DB::table($table)->where("whatup_id",$wp_id)
                        ->whereDate("created_at","=",date("Y-m-d"))
                        ->where("ip",getUserIp())
                        ->first();
        // command 
        $command = "
/* ====================== pip table for read count whatup ==================== 
 * on ".date("Y-m-d H:i:s")."
 * */
INSERT INTO `{$table}`(`read_id`,`whatup_id`,`ip`,`created_at`,`updated_at`) 
VALUES(
'{$r_data->read_id}',
'{$r_data->whatup_id}',
'{$r_data->ip}',
'{$r_data->created_at}',
'{$r_data->updated_at}');
";
       write2text($file,$command); 
    }

}
