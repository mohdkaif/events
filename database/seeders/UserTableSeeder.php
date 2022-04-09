<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = array(
            array('id' => '1', 'name' => 'Mohd Kaif', 'email' => 'mohdkaif984@gmail.com', 'email_verified_at' => NULL, 'password' => '$2y$10$gADs7eSzlcMUJ5Vrzh38cOBL6ry/a.fy8tVdxz4hUj04aMjkQ43W.', 'remember_token' => NULL, 'created_at' => '2022-02-08 12:38:53', 'updated_at' => '2022-02-08 12:38:53')
        );
        foreach ($users as $count) {
            \DB::table('users')->insert($count);
        }
    }
}
