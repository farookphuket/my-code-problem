<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaticPage extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "title",
        "slug",
        "body",
        "is_public"
    ];

    protected $with = ["user"];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
