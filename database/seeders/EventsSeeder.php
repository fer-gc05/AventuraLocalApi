<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Destination;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EventsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Event::truncate();

        $destinations = Destination::all();
        $organizers = User::role('Event Organizer')->get();
        $participants = User::role('Event Participant')->get();

        // Eventos reales relacionados con los destinos
        $eventos_reales = [
            [
                'title' => 'Festival del Río Sinú',
                'description' => 'Gran celebración cultural con música, gastronomía y actividades tradicionales a orillas del río Sinú.',
                'location' => 'Ronda del Sinú, Montería',
                'latitude' => 8.7557,
                'longitude' => -75.8906,
                'price' => 50000,
                'max_attendees' => 1000,
                'destination_slug' => 'ronda-del-sinu-3'
            ],
            [
                'title' => 'Tour Gastronómico en Lorica',
                'description' => 'Recorrido por los mejores restaurantes y puestos de comida tradicional en el Mercado Público de Lorica.',
                'location' => 'Mercado Público de Lorica',
                'latitude' => 9.2361,
                'longitude' => -75.8139,
                'price' => 35000,
                'max_attendees' => 50,
                'destination_slug' => 'mercado-publico-de-lorica-9'
            ],
            [
                'title' => 'Avistamiento de Aves en El Garcero',
                'description' => 'Tour guiado para observar aves migratorias y especies locales en la Reserva Natural El Garcero.',
                'location' => 'Reserva Natural El Garcero, Cereté',
                'latitude' => 8.8847,
                'longitude' => -75.7900,
                'price' => 25000,
                'max_attendees' => 30,
                'destination_slug' => 'reserva-natural-el-garcero-10'
            ],
            [
                'title' => 'Festival de Playa en San Antero',
                'description' => 'Celebración con música, deportes acuáticos y actividades culturales en Playa Blanca.',
                'location' => 'Playa Blanca, San Antero',
                'latitude' => 9.3747,
                'longitude' => -75.7556,
                'price' => 40000,
                'max_attendees' => 500,
                'destination_slug' => 'playa-blanca-1'
            ],
            [
                'title' => 'Exposición de Arte Contemporáneo',
                'description' => 'Muestra de artistas locales y nacionales en el Museo Zenú de Arte Contemporáneo.',
                'location' => 'Museo Zenú de Arte Contemporáneo, Montería',
                'latitude' => 8.7523,
                'longitude' => -75.8816,
                'price' => 20000,
                'max_attendees' => 100,
                'destination_slug' => 'museo-zenu-de-arte-contemporaneo-19'
            ],
            [
                'title' => 'Paseo en Canoa por la Ciénaga de Ayapel',
                'description' => 'Recorrido guiado por la ciénaga para observar la flora y fauna local.',
                'location' => 'Ciénaga de Ayapel',
                'latitude' => 8.3136,
                'longitude' => -75.1456,
                'price' => 30000,
                'max_attendees' => 20,
                'destination_slug' => 'cienaga-de-ayapel-7'
            ],
            [
                'title' => 'Concierto en la Plaza Cultural',
                'description' => 'Presentación de artistas locales en la Plaza Cultural del Sinú.',
                'location' => 'Plaza Cultural del Sinú, Montería',
                'latitude' => 8.7525,
                'longitude' => -75.8815,
                'price' => 45000,
                'max_attendees' => 300,
                'destination_slug' => 'plaza-cultural-del-sinu-14'
            ],
            [
                'title' => 'Tour Histórico por la Catedral',
                'description' => 'Recorrido guiado por la historia y arquitectura de la Catedral San Jerónimo.',
                'location' => 'Catedral San Jerónimo, Montería',
                'latitude' => 8.7522,
                'longitude' => -75.8817,
                'price' => 15000,
                'max_attendees' => 40,
                'destination_slug' => 'catedral-san-jeronimo-4'
            ]
        ];

        // Crear eventos reales
        foreach ($eventos_reales as $evento) {
            $destination = Destination::where('slug', $evento['destination_slug'])->first();
            if (!$destination) continue;

            $startDate = Carbon::now()->addDays(rand(1, 30));
            $endDate = (clone $startDate)->addHours(rand(2, 6));

            $event = Event::create([
                'title' => $evento['title'],
                'slug' => Str::slug($evento['title']),
                'description' => $evento['description'],
                'start_datetime' => $startDate,
                'end_datetime' => $endDate,
                'location' => $evento['location'],
                'latitude' => $evento['latitude'],
                'longitude' => $evento['longitude'],
                'price' => $evento['price'],
                'currency' => 'COP',
                'max_attendees' => $evento['max_attendees'],
                'user_id' => $organizers->random()->id,
                'destination_id' => $destination->id
            ]);

            // Asignar algunos participantes aleatorios
            $numParticipants = min(rand(2, 5), $participants->count());
            if ($numParticipants > 0) {
                $randomParticipants = $participants->random($numParticipants);
                foreach ($randomParticipants as $participant) {
                    $event->attendees()->attach($participant->id, [
                        'status' => collect(['registered', 'attended', 'cancelled'])->random()
                    ]);
                }
            }
        }

        // Crear eventos reales para cada destino real
        foreach ($destinations as $destination) {
            $numEventos = rand(5, 10);
            for ($i = 1; $i <= $numEventos; $i++) {
                $tipos = [
                    'Festival', 'Tour Guiado', 'Concierto', 'Exposición', 'Taller',
                    'Feria Gastronómica', 'Competencia Deportiva', 'Seminario', 'Conferencia', 'Encuentro Cultural'
                ];
                $tipo = $tipos[array_rand($tipos)];
                $titulo = $tipo . ' en ' . $destination->name;
                $descripciones = [
                    "Disfruta de una experiencia única en {$destination->name} con actividades para toda la familia.",
                    "Evento especial de {$tipo} en {$destination->name}, no te lo pierdas.",
                    "Ven y participa en el {$tipo} más esperado en {$destination->city}.",
                    "Sumérgete en la cultura y tradiciones de {$destination->city} en este gran evento.",
                    "Una oportunidad para conocer más sobre {$destination->name} y su gente.",
                    "Actividades, música y gastronomía en un solo lugar: {$destination->name}.",
                    "Vive una jornada inolvidable en {$destination->name} con el mejor ambiente.",
                    "Evento para toda la comunidad, ideal para compartir en familia y con amigos.",
                    "Descubre lo mejor de {$destination->city} en este {$tipo} imperdible.",
                    "Un evento que resalta la belleza y cultura de {$destination->name}."
                ];
                $descripcion = $descripciones[array_rand($descripciones)];
                $startDate = Carbon::now()->addDays(rand(1, 180));
                $endDate = (clone $startDate)->addHours(rand(2, 8));
                $event = Event::create([
                    'title' => $titulo,
                    'slug' => Str::slug($titulo) . '-' . $destination->id . '-' . $i,
                    'description' => $descripcion,
                    'start_datetime' => $startDate,
                    'end_datetime' => $endDate,
                    'location' => $destination->address,
                    'latitude' => $destination->latitude,
                    'longitude' => $destination->longitude,
                    'price' => rand(10000, 100000),
                    'currency' => 'COP',
                    'max_attendees' => rand(20, 500),
                    'user_id' => $organizers->random()->id,
                    'destination_id' => $destination->id
                ]);
                $numParticipants = min(rand(2, 5), $participants->count());
                if ($numParticipants > 0) {
                    $randomParticipants = $participants->random($numParticipants);
                    foreach ($randomParticipants as $participant) {
                        $event->attendees()->attach($participant->id, [
                            'status' => collect(['registered', 'attended', 'cancelled'])->random()
                        ]);
                    }
                }
            }
        }

        // Generar eventos adicionales aleatorios
        $tipos_eventos = [
            'Tour Guiado', 'Festival Cultural', 'Concierto', 'Exposición', 'Taller',
            'Feria Gastronómica', 'Competencia Deportiva', 'Seminario', 'Conferencia'
        ];

        for ($i = 0; $i < 200; $i++) {
            $destination = $destinations->random();
            $tipo = $tipos_eventos[array_rand($tipos_eventos)];
            $startDate = Carbon::now()->addDays(rand(1, 60));
            $endDate = (clone $startDate)->addHours(rand(2, 8));

            $event = Event::create([
                'title' => $tipo . ' en ' . $destination->name,
                'slug' => Str::slug($tipo . ' en ' . $destination->name) . '-' . ($i + 1),
                'description' => "Evento especial de {$tipo} en {$destination->name}. ¡No te lo pierdas!",
                'start_datetime' => $startDate,
                'end_datetime' => $endDate,
                'location' => $destination->address,
                'latitude' => $destination->latitude,
                'longitude' => $destination->longitude,
                'price' => rand(10000, 100000),
                'currency' => 'COP',
                'max_attendees' => rand(20, 200),
                'user_id' => $organizers->random()->id,
                'destination_id' => $destination->id
            ]);

            // Asignar algunos participantes aleatorios
            $numParticipants = min(rand(2, 5), $participants->count());
            if ($numParticipants > 0) {
                $randomParticipants = $participants->random($numParticipants);
                foreach ($randomParticipants as $participant) {
                    $event->attendees()->attach($participant->id, [
                        'status' => collect(['registered', 'attended', 'cancelled'])->random()
                    ]);
                }
            }
        }
    }
}
