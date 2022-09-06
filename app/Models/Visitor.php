<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    use HasFactory;

    protected static $visit_table = "visitors";

    protected $fillable = [
        "ip",
        "os",
        "browser",
        "device"
    ];

    public static function makeBackup(){
        // table 
        $table = static::$visit_table;

        // file 
        $file = base_path("DB/visitor_list.sqlite");

        // data 
        $data = Visitor::latest()->first();

        // command 
        $command = "
/* ========================== Backup Visitor List =============================
 * on ".date("Y-m-d H:i:s a")."
 * */
INSERT INTO `{$table}`(`ip`,`os`,`browser`,`device`,`created_at`,
`updated_at`) VALUES(
    '{$data->ip}',
    '{$data->os}',
    '{$data->browser}',
    '{$data->device}',
    '{$data->created_at}',
    '{$data->updated_at}');
";
        // write to file 
        write2text($file,$command);
    }
}
