<?php

namespace Database\Seeders;

use App\Models\Destination;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DestinationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Destination::truncate();

        $entrepreneurs = User::role('Entrepreneur')->get();
        $categories = Category::all();
        $tags = Tag::all();

        // Lugares turísticos reales de Córdoba
        $lugares_reales = [
            [
                'name' => 'Playa Blanca',
                'city' => 'San Antero',
                'address' => 'Vía a Playa Blanca, San Antero',
                'latitude' => 9.3747,
                'longitude' => -75.7556,
                'category_slug' => 'turismo-playa',
                'tags' => ['playa', 'relax', 'naturaleza'],
                'description' => 'Una de las playas más famosas de Córdoba, con arena blanca y aguas tranquilas, ideal para descansar y disfrutar en familia.'
            ],
            [
                'name' => 'Ciénaga Grande de Lorica',
                'city' => 'Lorica',
                'address' => 'Zona rural, Lorica',
                'latitude' => 9.2411,
                'longitude' => -75.8136,
                'category_slug' => 'turismo-naturaleza',
                'tags' => ['naturaleza', 'pesca', 'ecoturismo'],
                'description' => 'Un ecosistema de gran biodiversidad, hogar de aves y peces, perfecto para el ecoturismo y la observación de fauna.'
            ],
            [
                'name' => 'Ronda del Sinú',
                'city' => 'Montería',
                'address' => 'Avenida Primera, Montería',
                'latitude' => 8.7557,
                'longitude' => -75.8906,
                'category_slug' => 'turismo-urbano',
                'tags' => ['parque', 'ciudad', 'ciclismo'],
                'description' => 'Un parque lineal a orillas del río Sinú, ideal para caminar, montar bicicleta y disfrutar de la naturaleza en la ciudad.'
            ],
            [
                'name' => 'Catedral San Jerónimo',
                'city' => 'Montería',
                'address' => 'Calle 27 #3-03, Montería',
                'latitude' => 8.7522,
                'longitude' => -75.8817,
                'category_slug' => 'turismo-religioso',
                'tags' => ['catedral', 'historico', 'cultural'],
                'description' => 'Imponente catedral de estilo neoclásico, símbolo religioso y arquitectónico de la ciudad.'
            ],
            [
                'name' => 'Playa de Puerto Escondido',
                'city' => 'Puerto Escondido',
                'address' => 'Carrera 2, Puerto Escondido',
                'latitude' => 9.0167,
                'longitude' => -76.2500,
                'category_slug' => 'turismo-playa',
                'tags' => ['playa', 'relax', 'deportes-acuaticos'],
                'description' => 'Playa tranquila y poco concurrida, ideal para quienes buscan paz y contacto con la naturaleza.'
            ],
            [
                'name' => 'Museo del Río',
                'city' => 'Montería',
                'address' => 'Avenida Primera, Montería',
                'latitude' => 8.7550,
                'longitude' => -75.8900,
                'category_slug' => 'turismo-cultural',
                'tags' => ['museo', 'cultural', 'educativo'],
                'description' => 'Espacio dedicado a la historia y cultura del río Sinú y su importancia para la región.'
            ],
            [
                'name' => 'Ciénaga de Ayapel',
                'city' => 'Ayapel',
                'address' => 'Zona rural, Ayapel',
                'latitude' => 8.3136,
                'longitude' => -75.1456,
                'category_slug' => 'turismo-naturaleza',
                'tags' => ['naturaleza', 'pesca', 'ecoturismo'],
                'description' => 'Gran cuerpo de agua dulce, ideal para la pesca artesanal y la observación de aves.'
            ],
            [
                'name' => 'Playa de Moñitos',
                'city' => 'Moñitos',
                'address' => 'Carrera 1, Moñitos',
                'latitude' => 9.2500,
                'longitude' => -76.1333,
                'category_slug' => 'turismo-playa',
                'tags' => ['playa', 'relax', 'naturaleza'],
                'description' => 'Playa de ambiente familiar, con aguas cálidas y paisajes hermosos.'
            ],
            [
                'name' => 'Mercado Público de Lorica',
                'city' => 'Lorica',
                'address' => 'Calle 3 # 2-10, Lorica',
                'latitude' => 9.2361,
                'longitude' => -75.8139,
                'category_slug' => 'turismo-cultural',
                'tags' => ['mercado', 'gastronomia-local', 'cultural'],
                'description' => 'Mercado tradicional con arquitectura republicana, famoso por su gastronomía y ambiente colorido.'
            ],
            [
                'name' => 'Reserva Natural El Garcero',
                'city' => 'Cereté',
                'address' => 'Vereda El Garcero, Cereté',
                'latitude' => 8.8847,
                'longitude' => -75.7900,
                'category_slug' => 'turismo-naturaleza',
                'tags' => ['naturaleza', 'observacion-aves', 'ecoturismo'],
                'description' => 'Santuario de aves y fauna silvestre, ideal para el avistamiento y la fotografía.'
            ],
            [
                'name' => 'Playa de San Bernardo del Viento',
                'city' => 'San Bernardo del Viento',
                'address' => 'Vía a la playa, San Bernardo del Viento',
                'latitude' => 9.3500,
                'longitude' => -75.9500,
                'category_slug' => 'turismo-playa',
                'tags' => ['playa', 'relax', 'naturaleza'],
                'description' => 'Playa extensa y tranquila, perfecta para largas caminatas y disfrutar del mar Caribe.'
            ],
            [
                'name' => 'Ciénaga de Momil',
                'city' => 'Momil',
                'address' => 'Zona rural, Momil',
                'latitude' => 9.2333,
                'longitude' => -75.6833,
                'category_slug' => 'turismo-naturaleza',
                'tags' => ['naturaleza', 'pesca', 'ecoturismo'],
                'description' => 'Ciénaga rodeada de vegetación, hábitat de aves y peces, ideal para el turismo ecológico.'
            ],
            [
                'name' => 'Parque Simón Bolívar',
                'city' => 'Montería',
                'address' => 'Calle 27, Montería',
                'latitude' => 8.7520,
                'longitude' => -75.8820,
                'category_slug' => 'turismo-urbano',
                'tags' => ['parque', 'ciudad', 'relax'],
                'description' => 'Parque central de Montería, punto de encuentro para eventos y actividades culturales.'
            ],
            [
                'name' => 'Plaza Cultural del Sinú',
                'city' => 'Montería',
                'address' => 'Calle 27 #3-03, Montería',
                'latitude' => 8.7525,
                'longitude' => -75.8815,
                'category_slug' => 'turismo-cultural',
                'tags' => ['cultural', 'ciudad', 'historico'],
                'description' => 'Espacio para exposiciones, conciertos y actividades artísticas en el corazón de la ciudad.'
            ],
            [
                'name' => 'Playa de Los Córdobas',
                'city' => 'Los Córdobas',
                'address' => 'Vía a la playa, Los Córdobas',
                'latitude' => 8.9000,
                'longitude' => -76.3500,
                'category_slug' => 'turismo-playa',
                'tags' => ['playa', 'relax', 'naturaleza'],
                'description' => 'Playa poco explorada, ideal para quienes buscan tranquilidad y paisajes vírgenes.'
            ],
            [
                'name' => 'Ciénaga de Betancí',
                'city' => 'Cereté',
                'address' => 'Zona rural, Cereté',
                'latitude' => 8.9167,
                'longitude' => -75.8333,
                'category_slug' => 'turismo-naturaleza',
                'tags' => ['naturaleza', 'pesca', 'ecoturismo'],
                'description' => 'Ciénaga de gran importancia ecológica, refugio de aves y especies acuáticas.'
            ],
            [
                'name' => 'Malecón Turístico de Lorica',
                'city' => 'Lorica',
                'address' => 'Avenida del Malecón, Lorica',
                'latitude' => 9.2365,
                'longitude' => -75.8135,
                'category_slug' => 'turismo-urbano',
                'tags' => ['malecón', 'ciudad', 'relax'],
                'description' => 'Paseo peatonal junto al río Sinú, con vista a la arquitectura colonial y ambiente festivo.'
            ],
            [
                'name' => 'Playa de Canalete',
                'city' => 'Canalete',
                'address' => 'Vía a la playa, Canalete',
                'latitude' => 8.7000,
                'longitude' => -76.2500,
                'category_slug' => 'turismo-playa',
                'tags' => ['playa', 'relax', 'naturaleza'],
                'description' => 'Playa de aguas tranquilas y arena clara, perfecta para disfrutar en familia.'
            ],
            [
                'name' => 'Museo Zenú de Arte Contemporáneo',
                'city' => 'Montería',
                'address' => 'Calle 27 #3-03, Montería',
                'latitude' => 8.7523,
                'longitude' => -75.8816,
                'category_slug' => 'turismo-cultural',
                'tags' => ['museo', 'cultural', 'arte'],
                'description' => 'Museo dedicado al arte contemporáneo y la cultura Zenú.'
            ],
            [
                'name' => 'Ciénaga de Purísima',
                'city' => 'Purísima',
                'address' => 'Zona rural, Purísima',
                'latitude' => 9.2333,
                'longitude' => -75.7167,
                'category_slug' => 'turismo-naturaleza',
                'tags' => ['naturaleza', 'pesca', 'ecoturismo'],
                'description' => 'Ciénaga rodeada de vegetación, ideal para la pesca y el avistamiento de aves.'
            ],
        ];

        // Crear los destinos reales
        foreach ($lugares_reales as $i => $lugar) {
            $category = Category::where('slug', $lugar['category_slug'])->first();
            $user = $entrepreneurs->random();
            $slug = Str::slug($lugar['name']) . '-' . ($i+1);
            $short_description = "Un lugar destacado en {$lugar['city']} para el turismo en Córdoba.";
            $destino = Destination::create([
                'name' => $lugar['name'],
                'slug' => $slug,
                'short_description' => $short_description,
                'description' => $lugar['description'],
                'address' => $lugar['address'],
                'city' => $lugar['city'],
                'state' => 'Córdoba',
                'country' => 'Colombia',
                'latitude' => $lugar['latitude'],
                'longitude' => $lugar['longitude'],
                'category_id' => $category ? $category->id : $categories->random()->id,
                'user_id' => $user->id,
                'zip_code' => '57' . str_pad(mt_rand(100, 999), 3, '0', STR_PAD_LEFT),
                'currency' => 'COP',
                'opening_hours' => '08:00-18:00',
                'contact_phone' => '+57 310' . mt_rand(1000000, 9999999),
                'contact_email' => 'info@' . Str::slug($lugar['city']) . '.com',
            ]);
            $tags_ids = Tag::whereIn('slug', $lugar['tags'])->pluck('id')->toArray();
            $destino->tags()->attach($tags_ids);
        }

        // Generar el resto de destinos de forma plausible
        $municipios = [
            'Montería', 'Lorica', 'Cereté', 'Sahagún', 'Tierralta', 'Planeta Rica', 'Montelíbano', 'San Antero',
            'San Bernardo del Viento', 'Ciénaga de Oro', 'Ayapel', 'Puerto Escondido', 'Moñitos', 'Chinú', 'La Apartada',
            'Valencia', 'San Pelayo', 'Momil', 'Tuchín', 'Pueblo Nuevo', 'Buenavista', 'Canalete', 'Purísima', 'Los Córdobas',
            'San Andrés de Sotavento', 'San José de Uré', 'San Carlos', 'Cotorra', 'Chimá'
        ];
        $lugares = [
            'Parque Central', 'Iglesia Principal', 'Plaza de Mercado', 'Río', 'Reserva Natural', 'Museo', 'Centro Comercial',
            'Malecón', 'Playa', 'Ciénaga', 'Mirador', 'Zona Gastronómica', 'Sendero Ecológico', 'Zona Histórica', 'Avenida Principal'
        ];
        for ($i = count($lugares_reales) + 1; $i <= 100; $i++) {
            $municipio = $municipios[array_rand($municipios)];
            $lugar = $lugares[array_rand($lugares)];
            $category = $categories->random();
            $user = $entrepreneurs->random();
            $nombre = $lugar . ' de ' . $municipio;
            $slug = Str::slug($nombre) . '-' . $i;
            $descripcion = "Disfruta de $lugar en $municipio, un sitio especial para el turismo en Córdoba.";
            $short_description = "Un lugar destacado en $municipio para el turismo en Córdoba.";
            $address = "$municipio, Córdoba";
            $lat = 8.0 + mt_rand(500, 999) / 100; // Coordenadas aproximadas
            $lng = -76 + mt_rand(500, 999) / 100;
            $destino = Destination::create([
                'name' => $nombre,
                'slug' => $slug,
                'short_description' => $short_description,
                'description' => $descripcion,
                'address' => $address,
                'city' => $municipio,
                'state' => 'Córdoba',
                'country' => 'Colombia',
                'latitude' => $lat,
                'longitude' => $lng,
                'category_id' => $category->id,
                'user_id' => $user->id,
                'zip_code' => '57' . str_pad(mt_rand(100, 999), 3, '0', STR_PAD_LEFT),
                'currency' => 'COP',
                'opening_hours' => '08:00-18:00',
                'contact_phone' => '+57 310' . mt_rand(1000000, 9999999),
                'contact_email' => 'info@' . Str::slug($municipio) . '.com',
            ]);
            $tagsRandom = $tags->random(rand(2, 4));
            $destino->tags()->attach($tagsRandom->pluck('id')->toArray());
        }
    }
}
