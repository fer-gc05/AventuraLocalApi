<?php

namespace Database\Seeders;

use App\Models\Tour;
use App\Models\Route;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TourSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Tour::truncate();

        $routes = Route::all();
        $guides = User::role('Entrepreneur')->get();

        // Tours reales basados en las rutas existentes
        $tours_reales = [
            [
                'name' => 'Tour Completo del Río Sinú',
                'description' => 'Experiencia completa de 3 días recorriendo los principales atractivos del río Sinú, incluyendo alojamiento y comidas típicas.',
                'price' => 450000,
                'duration_days' => 3,
                'max_participants' => 15,
                'route_slug' => 'ruta-del-rio-sinu'
            ],
            [
                'name' => 'Tour Express de Playas',
                'description' => 'Recorrido de un día por las playas más hermosas de Córdoba, ideal para quienes buscan una experiencia rápida pero completa.',
                'price' => 150000,
                'duration_days' => 1,
                'max_participants' => 20,
                'route_slug' => 'ruta-de-las-playas'
            ],
            [
                'name' => 'Tour Ecológico de Ciénagas',
                'description' => 'Experiencia de 2 días enfocada en el ecoturismo y la observación de aves en las principales ciénagas de Córdoba.',
                'price' => 280000,
                'duration_days' => 2,
                'max_participants' => 12,
                'route_slug' => 'ruta-de-las-cienagas'
            ],
            [
                'name' => 'Tour Cultural Montería',
                'description' => 'Recorrido de un día por los principales sitios culturales e históricos de Montería, incluyendo guía especializado.',
                'price' => 120000,
                'duration_days' => 1,
                'max_participants' => 25,
                'route_slug' => 'ruta-cultural-de-monteria'
            ],
            [
                'name' => 'Tour Gastronómico Lorica',
                'description' => 'Experiencia culinaria de un día en Lorica, degustando los mejores platos típicos de la región.',
                'price' => 180000,
                'duration_days' => 1,
                'max_participants' => 15,
                'route_slug' => 'ruta-gastronomica-de-lorica'
            ],
            [
                'name' => 'Tour de Aventura Cereté',
                'description' => 'Tour de 2 días con actividades de aventura y contacto con la naturaleza en Cereté.',
                'price' => 320000,
                'duration_days' => 2,
                'max_participants' => 10,
                'route_slug' => 'ruta-de-aventura-en-cerete'
            ],
            [
                'name' => 'Tour Histórico Montería',
                'description' => 'Recorrido guiado por los sitios históricos más importantes de Montería, incluyendo museos y monumentos.',
                'price' => 95000,
                'duration_days' => 1,
                'max_participants' => 20,
                'route_slug' => 'ruta-historica-de-monteria'
            ],
            [
                'name' => 'Tour Playas del Norte',
                'description' => 'Experiencia de 2 días recorriendo las playas más hermosas del norte de Córdoba, con actividades acuáticas incluidas.',
                'price' => 350000,
                'duration_days' => 2,
                'max_participants' => 15,
                'route_slug' => 'ruta-de-las-playas-del-norte'
            ]
        ];

        // Crear tours reales
        foreach ($tours_reales as $tour) {
            $route = Route::where('slug', $tour['route_slug'])->first();
            if (!$route) continue;

            $startDate = Carbon::now()->addDays(rand(1, 30));
            $endDate = (clone $startDate)->addDays($tour['duration_days']);

            Tour::create([
                'name' => $tour['name'],
                'slug' => Str::slug($tour['name']),
                'description' => $tour['description'],
                'price' => $tour['price'],
                'currency' => 'COP',
                'duration_days' => $tour['duration_days'],
                'max_participants' => $tour['max_participants'],
                'start_date' => $startDate,
                'end_date' => $endDate,
                'route_id' => $route->id,
                'user_id' => $guides->random()->id,
                'is_active' => true
            ]);
        }

        // Generar tours adicionales aleatorios
        $tipos_tours = [
            'Tour Express', 'Tour Premium', 'Tour Familiar', 'Tour Grupal',
            'Tour Privado', 'Tour Aventura', 'Tour Cultural', 'Tour Gastronómico'
        ];

        for ($i = 0; $i < 30; $i++) {
            $route = $routes->random();
            $tipo = $tipos_tours[array_rand($tipos_tours)];
            $startDate = Carbon::now()->addDays(rand(1, 60));
            $duration = rand(1, 5);
            $endDate = (clone $startDate)->addDays($duration);

            Tour::create([
                'name' => $tipo . ' ' . $route->name,
                'slug' => Str::slug($tipo . ' ' . $route->name) . '-' . ($i + 1),
                'description' => "Experiencia única de {$tipo} por la {$route->name}. Incluye guía especializado y actividades exclusivas.",
                'price' => rand(80000, 500000),
                'currency' => 'COP',
                'duration_days' => $duration,
                'max_participants' => rand(10, 30),
                'start_date' => $startDate,
                'end_date' => $endDate,
                'route_id' => $route->id,
                'user_id' => $guides->random()->id,
                'is_active' => rand(0, 1)
            ]);
        }
    }
} 