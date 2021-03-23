<?php

use Illuminate\Database\Seeder;
use Ramsey\Uuid\Uuid;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'id'  => \Ramsey\Uuid\Uuid::uuid4()->toString(),
            'name' => 'Developer',
            'identity_id' => '11122344555',
            'gender' => 1,
            'address' => 'Jl Raya KH. Noer Ali No. 1',
            'photo' => 'user.png', //note: tidak ada gambar
            'email' => 'developer@tora.co.id',
            'password' => app('hash')->make('secret'),
            'phone_number' => '082249933396',
            'active' => 1
        ]);
    }
}
