<?php

namespace Database\Seeders;

use App\Models\Route;
use App\Models\Destination;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class RouteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Route::truncate();

        $destinations = Destination::all();
        $guides = User::role('Entrepreneur')->get();

        // Rutas reales en Córdoba
        $rutas_reales = [
            [
                'name' => 'Ruta del Río Sinú',
                'description' => 'Recorrido por los principales destinos a orillas del río Sinú, incluyendo Montería, Lorica y San Bernardo del Viento.',
                'total_distance' => 120.5,
                'estimated_duration' => 1440, // 24 horas en minutos
                'difficulty' => 'medium',
                'destinations' => ['ronda-del-sinu-3', 'malecon-turistico-de-lorica-17', 'playa-de-san-bernardo-del-viento-11']
            ],
            [
                'name' => 'Ruta de las Playas',
                'description' => 'Tour por las mejores playas de Córdoba, desde San Antero hasta Los Córdobas.',
                'total_distance' => 85.3,
                'estimated_duration' => 1080, // 18 horas en minutos
                'difficulty' => 'easy',
                'destinations' => ['playa-blanca-1', 'playa-de-moñitos-8', 'playa-de-los-cordobas-15']
            ],
            [
                'name' => 'Ruta de las Ciénagas',
                'description' => 'Recorrido por los principales cuerpos de agua dulce de Córdoba, ideales para el ecoturismo.',
                'total_distance' => 150.2,
                'estimated_duration' => 1800, // 30 horas en minutos
                'difficulty' => 'hard',
                'destinations' => ['cienaga-grande-de-lorica-2', 'cienaga-de-ayapel-7', 'cienaga-de-momil-12']
            ],
            [
                'name' => 'Ruta Cultural de Montería',
                'description' => 'Tour por los principales sitios culturales e históricos de la capital de Córdoba.',
                'total_distance' => 5.8,
                'estimated_duration' => 360, // 6 horas en minutos
                'difficulty' => 'easy',
                'destinations' => ['catedral-san-jeronimo-4', 'museo-del-rio-6', 'plaza-cultural-del-sinu-14']
            ],
            [
                'name' => 'Ruta Gastronómica de Lorica',
                'description' => 'Recorrido por los mejores lugares para degustar la gastronomía local en Lorica.',
                'total_distance' => 3.2,
                'estimated_duration' => 240, // 4 horas en minutos
                'difficulty' => 'easy',
                'destinations' => ['mercado-publico-de-lorica-9', 'malecon-turistico-de-lorica-17']
            ],
            [
                'name' => 'Ruta de Aventura en Cereté',
                'description' => 'Tour de aventura por los principales atractivos naturales de Cereté.',
                'total_distance' => 45.6,
                'estimated_duration' => 720, // 12 horas en minutos
                'difficulty' => 'hard',
                'destinations' => ['reserva-natural-el-garcero-10', 'cienaga-de-betanci-16']
            ],
            [
                'name' => 'Ruta Histórica de Montería',
                'description' => 'Recorrido por los sitios históricos más importantes de Montería.',
                'total_distance' => 4.5,
                'estimated_duration' => 300, // 5 horas en minutos
                'difficulty' => 'easy',
                'destinations' => ['catedral-san-jeronimo-4', 'parque-simon-bolivar-13', 'plaza-cultural-del-sinu-14']
            ],
            [
                'name' => 'Ruta de las Playas del Norte',
                'description' => 'Tour por las playas más hermosas del norte de Córdoba.',
                'total_distance' => 95.7,
                'estimated_duration' => 1200, // 20 horas en minutos
                'difficulty' => 'medium',
                'destinations' => ['playa-de-puerto-escondido-5', 'playa-de-canalete-18']
            ]
        ];

        // Crear rutas reales
        foreach ($rutas_reales as $ruta) {
            $route = Route::create([
                'name' => $ruta['name'],
                'slug' => Str::slug($ruta['name']),
                'description' => $ruta['description'],
                'total_distance' => $ruta['total_distance'],
                'estimated_duration' => $ruta['estimated_duration'],
                'difficulty' => $ruta['difficulty'],
                'user_id' => $guides->random()->id
            ]);

            // Asociar destinos a la ruta
            foreach (array_values($ruta['destinations']) as $idx => $destination_slug) {
                $destination = Destination::where('slug', $destination_slug)->first();
                if ($destination) {
                    $route->destinations()->attach($destination->id, ['order' => $idx + 1]);
                }
            }
        }

        // Generar rutas adicionales aleatorias
        $tipos_rutas = [
            'Ruta Gastronómica', 'Ruta Cultural', 'Ruta de Aventura', 'Ruta Histórica',
            'Ruta Natural', 'Ruta Religiosa', 'Ruta Urbana', 'Ruta Rural'
        ];

        for ($i = 0; $i < 20; $i++) {
            $tipo = $tipos_rutas[array_rand($tipos_rutas)];
            $ciudad = $destinations->random()->city;
            $route = Route::create([
                'name' => $tipo . ' de ' . $ciudad,
                'slug' => Str::slug($tipo . ' de ' . $ciudad) . '-' . ($i + 1),
                'description' => "Descubre lo mejor de {$ciudad} en esta {$tipo} única. Un recorrido inolvidable por los lugares más destacados.",
                'total_distance' => rand(5, 150) + (rand(0, 100) / 100),
                'estimated_duration' => rand(120, 1800),
                'difficulty' => collect(['easy', 'medium', 'hard'])->random(),
                'user_id' => $guides->random()->id
            ]);

            // Asociar 2-4 destinos aleatorios a la ruta
            $randomDestinations = $destinations->random(rand(2, 4));
            foreach (array_values($randomDestinations->all()) as $idx => $destination) {
                $route->destinations()->attach($destination->id, ['order' => $idx + 1]);
            }
        }
    }
} 