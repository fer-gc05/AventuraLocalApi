<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $roles = [
            'Administrator',
            'Traveler',
            'Guide'
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        $permissions = [
            //General Admin permissions
            'manage-users',
            'manage-guides',
            'manage-tours',
            'manage-categories',
            'manage-destinations',
            'manage-reviews',
            'manage-reservations',
            'manage-payouts',
            'view-dashboard',

            //Traveler permissions
            'view-tours',
            'create-reservations',
            'view-own-reservations',
            'cancel-own-reservations',
            'create-reviews',
            'manage-own-media',

            //Guide permissions
            'view-own-dashboard',
            'manage-own-tours',
            'manage-own-schedules',
            'view-own-reservations',
            'manage-own-reviews',
            'manage-own-payouts',
            'manage-own-media'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $adminRole = Role::where('name', 'Administrator')->first();
        $adminRole->givePermissionTo(Permission::all());

        $travelerRole = Role::where('name', 'Traveler')->first();
        $travelerRole->givePermissionTo(
            'view-tours',
            'create-reservations',
            'view-own-reservations',
            'cancel-own-reservations',
            'create-reviews',
            'manage-own-media'
        );

        $guideRole = Role::where('name', 'Guide')->first();
        $guideRole->givePermissionTo(
            'view-own-dashboard',
            'manage-own-tours',
            'manage-own-schedules',
            'view-own-reservations',
            'manage-own-reviews',
            'manage-own-payouts',
            'manage-own-media'
        );
    }
}
