<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //$professions = DB::select('SELECT id FROM professions WHERE title = ? LIMIT 0,1', ['Back-end developer']);

        $professionId = DB::table('professions')
            ->where('title', 'Back-end developer')
            ->value('id');

        DB::table('users')->insert([
           'name' => 'Edwin Ibañez',
           'email' => 'edwin.ibanez@tooducks.com',
           'password' => bcrypt('laravel'),
           'profession_id' => $professionId,
        ]);
    }
}
