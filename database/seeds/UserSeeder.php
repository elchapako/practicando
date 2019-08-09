<?php

use App\Profession;
use App\User;
use App\UserProfile;
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

        $professionId = Profession::where('title', 'Back-end developer')->value('id');

        $user = User::create([
           'name' => 'Edwin Ibañez',
           'email' => 'edwin.ibanez@tooducks.com',
           'password' => bcrypt('laravel'),
           'is_admin' => true,
        ]);

        $user->profile()->create([
           'bio' => 'Programador',
           'profession_id' => $professionId,
        ]);

        factory(User::class, 29)->create()->each(function ($user) {
            $user->profile()->create(
                factory(UserProfile::class)->raw()
            );
        });
    }
}
