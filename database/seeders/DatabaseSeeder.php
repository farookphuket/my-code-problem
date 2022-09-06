<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;
use Eloquent;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call([
            UserSeeder::class,
            RoleSeeder::class,
            WhatupSeeder::class,
            ReadSeeder::class,
            PostSeeder::class,
            CommentSeeder::class,
            VisitorSeeder::class,
        ]);

        $this->make_pip_table();
    }

    public function make_pip_table(){

            /* link user to role */
            Eloquent::unguard();
            $role_file = 'DB/user_role_link.sqlite';
            DB::unprepared(file_get_contents($role_file));
            $this->command->info("Role of User has been Created!!");


            /* read whatup list link */
            Eloquent::unguard();
            $role_file = 'DB/read_wp_link.sqlite';
            DB::unprepared(file_get_contents($role_file));
            $this->command->info("Read whatup link has been Created!!");


            /* read post list link */
            Eloquent::unguard();
            $role_file = 'DB/read_post_link.sqlite';
            DB::unprepared(file_get_contents($role_file));
            $this->command->info("Read Post link has been Created!!");


            /* read comment list link */
            Eloquent::unguard();
            $role_file = 'DB/comment_post_link.sqlite';
            DB::unprepared(file_get_contents($role_file));
            $this->command->info("Comment Post link has been Created!!");
 
    }

}
