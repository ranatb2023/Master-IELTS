<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name' => 'Admin G',
            'email' => 'admin@admin.com',
            'password' => Hash::make('12345678'),
        ]);

        Role::create(['name' => 'Super Admin']);
        Role::create(['name' => 'Tutor']);
        Role::create(['name' => 'Student']);
        Role::create(['name' => 'Manager']);

        $user->assignRole('Super Admin');
    }
}
