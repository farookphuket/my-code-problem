<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

use App\Models\Role;
use DB;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'avatar',
        'is_admin',
        'email',
        'password',
        'email_verified_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
    // backup user table 
    protected static $user_table = 'users';
    protected static $role_link_table = "role_user";

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function role(){
        return $this->belongsToMany(Role::class);
    }

    public function whatup(){
        return $this->hasMany(Whatup::class);
    }

    public function post(){
        return $this->hasMany(Post::class);
    }

    public function comment(){
        return $this->hasMany(Comment::class);
    }

    public function static_page(){
        return $this->hasMany(StaticPage::class);
    }

    /*
     *backupUser START
     * */
    public static function backupUser($id,$cmd=false){
        // table 
        $table = static::$user_table;

        // file 
        $file = base_path("DB/user_list.sqlite");

        // data 
        $u = User::find($id);

        // command 
        $command = "";

        switch($cmd):
        case"insert":
            $command = "
/* ==================== insert command for {$id} =============================
 * on ".date("Y-m-d H:i:s a")."
 * */
INSERT INTO `{$table}`(`avatar`,`name`,`email`,`password`,`email_verified_at`
,`is_admin`,`created_at`,`updated_at`) VALUES(
'{$u->avatar}',
'{$u->name}',
'{$u->email}',
'{$u->password}',
'{$u->is_admin}',
'{$u->created_at}',
'{$u->updated_at}'); ";

        // backup role link
        static::backupUserRoleLink($u->id);
    break;
        case"update":
            $command = "
/* ==================== update command for {$id} =============================
 * on ".date("Y-m-d H:i:s a")."
 * */
UPDATE `{$table}` SET avatar='{$u->avatar}',
name='{$u->name}',
email='{$u->email}',
password='{$u->password}',
is_admin='{$u->is_admin}',
updated_at='{$u->updated_at}' WHERE id='{$id}';
";
            break;
        default:
            $command = "
/* ==================== DELETE  command for {$id} =============================
 * on ".date("Y-m-d H:i:s a")." 
 * DELETE command will not be execute just to avoid from Error on seeding
 * */
/* DELETE FROM `{$table}` WHERE id='{$id}'; */
";
            break;
            endswitch;

            // write to file 
            write2text($file,$command);
    }

    public static function backupUserRoleLink($id){
        // table 
        $table = static::$role_link_table;

        // file 
        $file = base_path("DB/user_role_link.sqlite");

        // data 
        $ro = DB::table($table)
                ->where("user_id",$id)
                ->get();
        if(count($ro) > 1):
            $command = "
DELETE FROM `{$table}` WHERE user_id='{$id}';
";

        endif;

            foreach($ro as $role):
                $command = "
    INSERT INTO `{$table}` (`user_id`,`role_id`) VALUES(
        '{$role->user_id}',
        '{$role->role_id}');
    ";
            endforeach;
        write2text($file,$command);
    }
    /*
     *backupUser END
     * */


}
