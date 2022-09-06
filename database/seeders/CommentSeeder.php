<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Eloquent;
use DB;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // make post table
        Eloquent::unguard();
        $path = 'DB/comment_list.sqlite';
        DB::unprepared(file_get_contents($path));
        $this->command->info("Comment list has been Created!!");
    }
}
