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
            'Entrepreneur',
            'Event Organizer',
            'Event Participant',
        ];

        foreach ($roles as $role) {
            Role::create(['name' => $role]);
        }

        $permissions = [
            'manage-users',
            'manage-tags',
            'manage-categories',
            'manage-destinations',
            'view-events',
            'create-events',
            'edit-events',
            'delete-events',
            'manage-reservations',
            'manage-reviews',
            'view-routes',
            'create-routes',
            'edit-routes',
            'delete-routes',
            'view-tours',
            'create-tours',
            'edit-tours',
            'delete-tours',
            'manage-media',
            'manage-communities',
            'manage-messages',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $adminRole = Role::where('name', 'Administrator')->first();
        $adminRole->givePermissionTo(Permission::all());


        $travelerRole = Role::where('name', 'Traveler')->first();
        $travelerRole->givePermissionTo([
            'view-events',         // Puede ver eventos
            'view-tours',         // Puede ver tours
            'view-routes',        // Puede ver rutas
            'manage-reservations', // Puede hacer reservas
            'manage-reviews',     // Puede escribir reseñas
            'manage-communities', // Puede participar en comunidades
            'manage-messages',    // Puede enviar/recibir mensajes
            'manage-media',       // Puede subir su foto de perfil
        ]);

        $entrepreneurRole = Role::where('name', 'Entrepreneur')->first();
        $entrepreneurRole->givePermissionTo([
            'manage-destinations', // Puede crear/editar destinos
            'manage-categories',  // Puede gestionar categorías
            'manage-tags',        // Puede gestionar etiquetas
            'view-events',        // Puede ver eventos
            'create-events',      // Puede crear eventos
            'edit-events',        // Puede editar eventos
            'delete-events',      // Puede eliminar eventos
            'view-tours',         // Puede ver tours
            'create-tours',       // Puede crear tours
            'edit-tours',         // Puede editar tours
            'delete-tours',       // Puede eliminar tours
            'view-routes',        // Puede ver rutas
            'create-routes',      // Puede crear rutas
            'edit-routes',        // Puede editar rutas
            'delete-routes',      // Puede eliminar rutas
            'manage-reservations', // Puede gestionar reservas
            'manage-reviews',     // Puede gestionar reseñas
            'manage-communities', // Puede crear/gestionar comunidades
            'manage-messages',    // Puede enviar/recibir mensajes
            'manage-media',       // Puede subir fotos (e.g., para destinos o tours)
        ]);


        $eventOrganizerRole = Role::where('name', 'Event Organizer')->first();
        $eventOrganizerRole->givePermissionTo([
            'view-events',        // Puede ver eventos
            'create-events',      // Puede crear eventos
            'edit-events',        // Puede editar eventos
            'delete-events',      // Puede eliminar eventos
            'manage-reservations', // Puede gestionar reservas de eventos
            'manage-reviews',     // Puede gestionar reseñas de eventos
            'manage-media',       // Puede subir fotos para eventos
        ]);


        $eventParticipantRole = Role::where('name', 'Event Participant')->first();
        $eventParticipantRole->givePermissionTo([
            'view-events',        // Puede ver eventos
            'manage-reservations', // Puede reservar eventos
            'manage-reviews',     // Puede reseñar eventos
        ]);
    }
}
