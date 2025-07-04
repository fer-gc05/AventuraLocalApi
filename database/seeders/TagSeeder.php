<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Tag::truncate();

        $tags = [
            ['name' => 'Playa', 'slug' => 'playa', 'color' => '#FFB74D'],
            ['name' => 'Montaña', 'slug' => 'montana', 'color' => '#81C784'],
            ['name' => 'Ciudad', 'slug' => 'ciudad', 'color' => '#64B5F6'],
            ['name' => 'Histórico', 'slug' => 'historico', 'color' => '#BA68C8'],
            ['name' => 'Aventura', 'slug' => 'aventura', 'color' => '#FF8A65'],
            ['name' => 'Gastronomía', 'slug' => 'gastronomia', 'color' => '#FFD54F'],
            ['name' => 'Naturaleza', 'slug' => 'naturaleza', 'color' => '#4DB6AC'],
            ['name' => 'Cultural', 'slug' => 'cultural', 'color' => '#7986CB'],
            ['name' => 'Ecoturismo', 'slug' => 'ecoturismo', 'color' => '#388E3C'],
            ['name' => 'Senderismo', 'slug' => 'senderismo', 'color' => '#A1887F'],
            ['name' => 'Camping', 'slug' => 'camping', 'color' => '#8D6E63'],
            ['name' => 'Safari', 'slug' => 'safari', 'color' => '#FBC02D'],
            ['name' => 'Crucero', 'slug' => 'crucero', 'color' => '#0288D1'],
            ['name' => 'Isla', 'slug' => 'isla', 'color' => '#00ACC1'],
            ['name' => 'Desierto', 'slug' => 'desierto', 'color' => '#F9A825'],
            ['name' => 'Bosque', 'slug' => 'bosque', 'color' => '#388E3C'],
            ['name' => 'Lago', 'slug' => 'lago', 'color' => '#4FC3F7'],
            ['name' => 'Río', 'slug' => 'rio', 'color' => '#0288D1'],
            ['name' => 'Cascada', 'slug' => 'cascada', 'color' => '#81D4FA'],
            ['name' => 'Parque Nacional', 'slug' => 'parque-nacional', 'color' => '#43A047'],
            ['name' => 'Reserva Natural', 'slug' => 'reserva-natural', 'color' => '#689F38'],
            ['name' => 'Museo', 'slug' => 'museo', 'color' => '#8E24AA'],
            ['name' => 'Galería de Arte', 'slug' => 'galeria-arte', 'color' => '#D1C4E9'],
            ['name' => 'Ruinas', 'slug' => 'ruinas', 'color' => '#BCAAA4'],
            ['name' => 'Castillo', 'slug' => 'castillo', 'color' => '#6D4C41'],
            ['name' => 'Catedral', 'slug' => 'catedral', 'color' => '#B39DDB'],
            ['name' => 'Monasterio', 'slug' => 'monasterio', 'color' => '#9575CD'],
            ['name' => 'Templo', 'slug' => 'templo', 'color' => '#CE93D8'],
            ['name' => 'Iglesia', 'slug' => 'iglesia', 'color' => '#F8BBD0'],
            ['name' => 'Mirador', 'slug' => 'mirador', 'color' => '#90CAF9'],
            ['name' => 'Jardín Botánico', 'slug' => 'jardin-botanico', 'color' => '#AED581'],
            ['name' => 'Zoológico', 'slug' => 'zoologico', 'color' => '#FF7043'],
            ['name' => 'Acuario', 'slug' => 'acuario', 'color' => '#4DD0E1'],
            ['name' => 'Parque Temático', 'slug' => 'parque-tematico', 'color' => '#FFB300'],
            ['name' => 'Spa', 'slug' => 'spa', 'color' => '#F06292'],
            ['name' => 'Termas', 'slug' => 'termas', 'color' => '#B2EBF2'],
            ['name' => 'Balneario', 'slug' => 'balneario', 'color' => '#FFF176'],
            ['name' => 'Glaciar', 'slug' => 'glaciar', 'color' => '#B3E5FC'],
            ['name' => 'Volcán', 'slug' => 'volcan', 'color' => '#FF8A65'],
            ['name' => 'Cuevas', 'slug' => 'cuevas', 'color' => '#A1887F'],
            ['name' => 'Grutas', 'slug' => 'grutas', 'color' => '#8D6E63'],
            ['name' => 'Cueva de Hielo', 'slug' => 'cueva-hielo', 'color' => '#E1F5FE'],
            ['name' => 'Cueva de Lava', 'slug' => 'cueva-lava', 'color' => '#FF7043'],
            ['name' => 'Viñedo', 'slug' => 'vinedo', 'color' => '#8D6E63'],
            ['name' => 'Bodega', 'slug' => 'bodega', 'color' => '#6D4C41'],
            ['name' => 'Ruta del Vino', 'slug' => 'ruta-vino', 'color' => '#D7CCC8'],
            ['name' => 'Mercado', 'slug' => 'mercado', 'color' => '#FFB300'],
            ['name' => 'Festival', 'slug' => 'festival', 'color' => '#F06292'],
            ['name' => 'Carnaval', 'slug' => 'carnaval', 'color' => '#FFD54F'],
            ['name' => 'Feria', 'slug' => 'feria', 'color' => '#FFF176'],
            ['name' => 'Concierto', 'slug' => 'concierto', 'color' => '#7986CB'],
            ['name' => 'Teatro', 'slug' => 'teatro', 'color' => '#BA68C8'],
            ['name' => 'Danza', 'slug' => 'danza', 'color' => '#F8BBD0'],
            ['name' => 'Gastronomía Local', 'slug' => 'gastronomia-local', 'color' => '#FFD54F'],
            ['name' => 'Comida Callejera', 'slug' => 'comida-callejera', 'color' => '#FF7043'],
            ['name' => 'Restaurante', 'slug' => 'restaurante', 'color' => '#FFB74D'],
            ['name' => 'Bar', 'slug' => 'bar', 'color' => '#8D6E63'],
            ['name' => 'Café', 'slug' => 'cafe', 'color' => '#A1887F'],
            ['name' => 'Cervecería', 'slug' => 'cerveceria', 'color' => '#FFD54F'],
            ['name' => 'Montañismo', 'slug' => 'montanismo', 'color' => '#388E3C'],
            ['name' => 'Escalada', 'slug' => 'escalada', 'color' => '#8D6E63'],
            ['name' => 'Ciclismo', 'slug' => 'ciclismo', 'color' => '#4FC3F7'],
            ['name' => 'Buceo', 'slug' => 'buceo', 'color' => '#0288D1'],
            ['name' => 'Snorkel', 'slug' => 'snorkel', 'color' => '#4DD0E1'],
            ['name' => 'Surf', 'slug' => 'surf', 'color' => '#0288D1'],
            ['name' => 'Kayak', 'slug' => 'kayak', 'color' => '#4FC3F7'],
            ['name' => 'Rafting', 'slug' => 'rafting', 'color' => '#0288D1'],
            ['name' => 'Pesca', 'slug' => 'pesca', 'color' => '#81C784'],
            ['name' => 'Golf', 'slug' => 'golf', 'color' => '#AED581'],
            ['name' => 'Esquí', 'slug' => 'esqui', 'color' => '#B3E5FC'],
            ['name' => 'Snowboard', 'slug' => 'snowboard', 'color' => '#E1F5FE'],
            ['name' => 'Patinaje', 'slug' => 'patinaje', 'color' => '#90CAF9'],
            ['name' => 'Parapente', 'slug' => 'parapente', 'color' => '#FFD54F'],
            ['name' => 'Vuelo en Globo', 'slug' => 'vuelo-globo', 'color' => '#FFB300'],
            ['name' => 'Helicóptero', 'slug' => 'helicoptero', 'color' => '#7986CB'],
            ['name' => 'Safari Fotográfico', 'slug' => 'safari-fotografico', 'color' => '#FBC02D'],
            ['name' => 'Observación de Aves', 'slug' => 'observacion-aves', 'color' => '#43A047'],
            ['name' => 'Avistamiento de Ballenas', 'slug' => 'avistamiento-ballenas', 'color' => '#0288D1'],
            ['name' => 'Avistamiento de Delfines', 'slug' => 'avistamiento-delfines', 'color' => '#4DD0E1'],
            ['name' => 'Cabalgata', 'slug' => 'cabalgata', 'color' => '#A1887F'],
            ['name' => 'Globo Aerostático', 'slug' => 'globo-aerostatico', 'color' => '#FFD54F'],
            ['name' => 'Caminata Nocturna', 'slug' => 'caminata-nocturna', 'color' => '#212121'],
            ['name' => 'Observación de Estrellas', 'slug' => 'observacion-estrellas', 'color' => '#B3E5FC'],
            ['name' => 'Auroras Boreales', 'slug' => 'auroras-boreales', 'color' => '#81D4FA'],
            ['name' => 'Fotografía', 'slug' => 'fotografia', 'color' => '#FBC02D'],
            ['name' => 'Relax', 'slug' => 'relax', 'color' => '#F8BBD0'],
            ['name' => 'Lujo', 'slug' => 'lujo', 'color' => '#FFD700'],
            ['name' => 'Boutique', 'slug' => 'boutique', 'color' => '#BA68C8'],
            ['name' => 'Resort', 'slug' => 'resort', 'color' => '#FFB74D'],
            ['name' => 'All Inclusive', 'slug' => 'all-inclusive', 'color' => '#FFD54F'],
            ['name' => 'Hostal', 'slug' => 'hostal', 'color' => '#8D6E63'],
            ['name' => 'Bed & Breakfast', 'slug' => 'bed-breakfast', 'color' => '#FFF176'],
            ['name' => 'Glamping', 'slug' => 'glamping', 'color' => '#AED581'],
            ['name' => 'Casa Rural', 'slug' => 'casa-rural', 'color' => '#A1887F'],
            ['name' => 'Cabaña', 'slug' => 'cabana', 'color' => '#8D6E63'],
            ['name' => 'Refugio', 'slug' => 'refugio', 'color' => '#BCAAA4'],
            ['name' => 'Aventura Extrema', 'slug' => 'aventura-extrema', 'color' => '#FF7043'],
            ['name' => 'Deportes Acuáticos', 'slug' => 'deportes-acuaticos', 'color' => '#0288D1'],
            ['name' => 'Deportes de Nieve', 'slug' => 'deportes-nieve', 'color' => '#B3E5FC'],
            ['name' => 'Deportes de Montaña', 'slug' => 'deportes-montana', 'color' => '#388E3C'],
            ['name' => 'Deportes de Aventura', 'slug' => 'deportes-aventura', 'color' => '#FF8A65'],
            ['name' => 'Turismo Rural', 'slug' => 'turismo-rural', 'color' => '#A1887F'],
            ['name' => 'Turismo Urbano', 'slug' => 'turismo-urbano', 'color' => '#64B5F6'],
            ['name' => 'Turismo Religioso', 'slug' => 'turismo-religioso', 'color' => '#F8BBD0'],
            ['name' => 'Turismo de Salud', 'slug' => 'turismo-salud', 'color' => '#F06292'],
            ['name' => 'Turismo de Compras', 'slug' => 'turismo-compras', 'color' => '#FFD54F'],
            ['name' => 'Turismo de Negocios', 'slug' => 'turismo-negocios', 'color' => '#7986CB'],
            ['name' => 'Turismo de Eventos', 'slug' => 'turismo-eventos', 'color' => '#FFB300'],
            ['name' => 'Turismo de Aventura', 'slug' => 'turismo-aventura', 'color' => '#FF8A65'],
            ['name' => 'Turismo Gastronómico', 'slug' => 'turismo-gastronomico', 'color' => '#FFD54F'],
            ['name' => 'Turismo Cultural', 'slug' => 'turismo-cultural', 'color' => '#7986CB'],
            ['name' => 'Turismo Científico', 'slug' => 'turismo-cientifico', 'color' => '#4DB6AC'],
            ['name' => 'Turismo Deportivo', 'slug' => 'turismo-deportivo', 'color' => '#388E3C'],
            ['name' => 'Turismo de Naturaleza', 'slug' => 'turismo-naturaleza', 'color' => '#4DB6AC'],
            ['name' => 'Turismo Sostenible', 'slug' => 'turismo-sostenible', 'color' => '#388E3C'],
            ['name' => 'Turismo Accesible', 'slug' => 'turismo-accesible', 'color' => '#FFD54F'],
            ['name' => 'Turismo Familiar', 'slug' => 'turismo-familiar', 'color' => '#FFB74D'],
            ['name' => 'Turismo LGBT', 'slug' => 'turismo-lgbt', 'color' => '#E040FB'],
            ['name' => 'Turismo de Voluntariado', 'slug' => 'turismo-voluntariado', 'color' => '#43A047'],
            ['name' => 'Turismo de Estudio', 'slug' => 'turismo-estudio', 'color' => '#64B5F6'],
            ['name' => 'Turismo de Aventura Extrema', 'slug' => 'turismo-aventura-extrema', 'color' => '#FF7043'],
            ['name' => 'Turismo de Relax', 'slug' => 'turismo-relax', 'color' => '#F8BBD0'],
            ['name' => 'Turismo de Lujo', 'slug' => 'turismo-lujo', 'color' => '#FFD700'],
        ];

        foreach ($tags as $tag) {
            Tag::create($tag);
        }
    }
}
