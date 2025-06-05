<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //User::truncate();
        // Crear usuarios administradores
        $admins = [
            ['name' => 'Carlos Rodríguez', 'email' => 'carlos.rodriguez@example.com'],
            ['name' => 'Laura Martínez', 'email' => 'laura.martinez@example.com'],
            ['name' => 'Diego Fernández', 'email' => 'diego.fernandez@example.com'],
        ];

        foreach ($admins as $admin) {
            $user = User::create([
                'name' => $admin['name'],
                'email' => $admin['email'],
                'password' => Hash::make('password'),
            ]);
            $user->assignRole('Administrator');
        }

        // Crear usuarios viajeros
        $travelers = [
            ['name' => 'María González', 'email' => 'maria.gonzalez@example.com'],
            ['name' => 'Sofía Ramírez', 'email' => 'sofia.ramirez@example.com'],
            ['name' => 'Lucas Torres', 'email' => 'lucas.torres@example.com'],
        ];

        foreach ($travelers as $traveler) {
            $user = User::create([
                'name' => $traveler['name'],
                'email' => $traveler['email'],
                'password' => Hash::make('password'),
            ]);
            $user->assignRole('Traveler');
        }

        // Crear usuarios emprendedores
        $entrepreneurs = [
            ['name' => 'Juan Martínez', 'email' => 'juan.martinez@example.com'],
            ['name' => 'Carmen Vega', 'email' => 'carmen.vega@example.com'],
            ['name' => 'Roberto Díaz', 'email' => 'roberto.diaz@example.com'],
        ];

        foreach ($entrepreneurs as $entrepreneur) {
            $user = User::create([
                'name' => $entrepreneur['name'],
                'email' => $entrepreneur['email'],
                'password' => Hash::make('password'),
            ]);
            $user->assignRole('Entrepreneur');
        }

        // Crear usuarios organizadores de eventos
        $eventOrganizers = [
            ['name' => 'Ana López', 'email' => 'ana.lopez@example.com'],
            ['name' => 'Miguel Sánchez', 'email' => 'miguel.sanchez@example.com'],
            ['name' => 'Isabel Ruiz', 'email' => 'isabel.ruiz@example.com'],
        ];

        foreach ($eventOrganizers as $organizer) {
            $user = User::create([
                'name' => $organizer['name'],
                'email' => $organizer['email'],
                'password' => Hash::make('password'),
            ]);
            $user->assignRole('Event Organizer');
        }

        // Crear usuarios participantes de eventos
        $eventParticipants = [
            ['name' => 'Pedro Sánchez', 'email' => 'pedro.sanchez@example.com'],
            ['name' => 'Elena Morales', 'email' => 'elena.morales@example.com'],
            ['name' => 'David Castro', 'email' => 'david.castro@example.com'],
        ];

        foreach ($eventParticipants as $participant) {
            $user = User::create([
                'name' => $participant['name'],
                'email' => $participant['email'],
                'password' => Hash::make('password'),
            ]);
            $user->assignRole('Event Participant');
        }
    }
}
