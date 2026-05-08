<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\ModelHasRole;
use Spatie\Permission\Models\ModelHasPermission;
use Spatie\Permission\Models\RoleHasPermission;
use App\Models\EventCategory;
use App\Models\Event;
use App\Models\Form;
use App\Models\FormField;
use App\Models\FormAnswer;
use App\Models\FormSubmission;
use App\Models\EventAttendance;
use App\Models\EventAttendanceScan;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => 'admin password'
        ]);

        $superAdmin = User::create([
            'name' => 'super admin',
            'email' => 'superadmin@gmail.com',
            'password' => 'superadmin password'
        ]);

        $this->call([
            RoleSeeder::class,
            EventSeeder::class,
            FormSeeder::class,
        ]);

        $admin->assignRole('admin');
        $superAdmin->assignRole('super-admin');

        $memberData = [
            ['name' => 'Ahmad Fauzi', 'email' => 'ahmad@student.dinus.ac.id'],
            ['name' => 'Siti Nurhaliza', 'email' => 'siti@student.dinus.ac.id'],
            ['name' => 'Budi Santoso', 'email' => 'budi@student.dinus.ac.id'],
            ['name' => 'Dewi Lestari', 'email' => 'dewi@student.dinus.ac.id'],
            ['name' => 'Rizky Pratama', 'email' => 'rizky@student.dinus.ac.id'],
        ];

        foreach ($memberData as $data) {
            $member = User::create([...$data, 'password' => 'password']);
            $member->assignRole('member');
        }
    }
}
