<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //Asetetaan tietokantaan admin-käyttäjä
        DB::insert('insert into users (username,usertype,password,created_at,updated_at) values (?,?,?,?,?)', ['admin','admin',Hash::make('codetag12345678'),\Carbon\Carbon::now(),\Carbon\Carbon::now()]);
    }
}
