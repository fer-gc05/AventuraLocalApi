<?php

namespace Database\Seeders;

use App\Models\Community;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CommunitySeeder extends Seeder
{
    public function run(): void
    {
        //Community::truncate();
        //\DB::table('community_user')->truncate();

        $categories = Category::all();
        $users = User::all();

        $nombres = [
            'Viajeros por Córdoba',
            'Amantes de la Naturaleza',
            'Gastronomía Cordobesa',
            'Cultura y Tradición',
            'Aventureros del Sinú',
            'Playas y Ríos',
            'Fotografía de Viajes',
            'Turismo Responsable',
            'Exploradores Urbanos',
            'Rutas y Caminos'
        ];

        foreach ($nombres as $nombre) {
            $category = $categories->random();
            $admin = $users->random();
            $community = Community::create([
                'name' => $nombre,
                'slug' => Str::slug($nombre),
                'description' => 'Comunidad dedicada a ' . strtolower($nombre) . '.',
                'category_id' => $category->id,
                'user_id' => $admin->id,
                'is_public' => rand(0, 1)
            ]);

            // Asignar miembros
            $miembros = $users->random(rand(5, 15));
            foreach ($miembros as $miembro) {
                $role = 'member';
                if ($miembro->id === $admin->id) {
                    $role = 'admin';
                } elseif (rand(0, 4) === 0) {
                    $role = 'moderator';
                }
                $community->users()->attach($miembro->id, ['role' => $role]);
            }
        }
    }
} 