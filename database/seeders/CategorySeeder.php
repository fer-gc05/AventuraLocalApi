<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Category::truncate();

        $categories = [
            ['name' => 'Turismo de Playa', 'slug' => 'turismo-playa', 'description' => 'Destinos y actividades relacionadas con playas y costas'],
            ['name' => 'Turismo de Montaña', 'slug' => 'turismo-montana', 'description' => 'Experiencias en zonas montañosas y altiplano'],
            ['name' => 'Turismo Urbano', 'slug' => 'turismo-urbano', 'description' => 'Exploración de ciudades y sus atractivos'],
            ['name' => 'Turismo Rural', 'slug' => 'turismo-rural', 'description' => 'Experiencias en entornos rurales y campestres'],
            ['name' => 'Turismo Cultural', 'slug' => 'turismo-cultural', 'description' => 'Visitas a sitios históricos y culturales'],
            ['name' => 'Turismo Gastronómico', 'slug' => 'turismo-gastronomico', 'description' => 'Experiencias culinarias y gastronómicas'],
            ['name' => 'Turismo de Aventura', 'slug' => 'turismo-aventura', 'description' => 'Actividades de aventura y deportes extremos'],
            ['name' => 'Turismo de Naturaleza', 'slug' => 'turismo-naturaleza', 'description' => 'Exploración de espacios naturales'],
            ['name' => 'Ecoturismo', 'slug' => 'ecoturismo', 'description' => 'Turismo responsable con el medio ambiente'],
            ['name' => 'Turismo Religioso', 'slug' => 'turismo-religioso', 'description' => 'Peregrinaciones y sitios religiosos'],
            ['name' => 'Turismo de Salud', 'slug' => 'turismo-salud', 'description' => 'Centros de bienestar y salud'],
            ['name' => 'Turismo de Negocios', 'slug' => 'turismo-negocios', 'description' => 'Viajes por motivos laborales'],
            ['name' => 'Turismo de Eventos', 'slug' => 'turismo-eventos', 'description' => 'Festivales y eventos especiales'],
            ['name' => 'Turismo de Compras', 'slug' => 'turismo-compras', 'description' => 'Destinos para compras y shopping'],
            ['name' => 'Turismo Deportivo', 'slug' => 'turismo-deportivo', 'description' => 'Eventos y actividades deportivas'],
            ['name' => 'Turismo Científico', 'slug' => 'turismo-cientifico', 'description' => 'Visitas a centros de investigación'],
            ['name' => 'Turismo Educativo', 'slug' => 'turismo-educativo', 'description' => 'Viajes con fines educativos'],
            ['name' => 'Turismo Familiar', 'slug' => 'turismo-familiar', 'description' => 'Destinos ideales para familias'],
            ['name' => 'Turismo LGBT', 'slug' => 'turismo-lgbt', 'description' => 'Destinos LGBT-friendly'],
            ['name' => 'Turismo Accesible', 'slug' => 'turismo-accesible', 'description' => 'Destinos adaptados para todos'],
            ['name' => 'Turismo de Lujo', 'slug' => 'turismo-lujo', 'description' => 'Experiencias premium y exclusivas'],
            ['name' => 'Turismo de Voluntariado', 'slug' => 'turismo-voluntariado', 'description' => 'Viajes con propósito social'],
            ['name' => 'Turismo de Estudio', 'slug' => 'turismo-estudio', 'description' => 'Programas de estudio en el extranjero'],
            ['name' => 'Turismo de Relax', 'slug' => 'turismo-relax', 'description' => 'Destinos para descanso y relajación'],
            ['name' => 'Turismo de Cruceros', 'slug' => 'turismo-cruceros', 'description' => 'Viajes en crucero'],
            ['name' => 'Turismo de Islas', 'slug' => 'turismo-islas', 'description' => 'Destinos insulares'],
            ['name' => 'Turismo de Desierto', 'slug' => 'turismo-desierto', 'description' => 'Experiencias en desiertos'],
            ['name' => 'Turismo de Bosques', 'slug' => 'turismo-bosques', 'description' => 'Exploración de bosques'],
            ['name' => 'Turismo de Lagos', 'slug' => 'turismo-lagos', 'description' => 'Destinos lacustres'],
            ['name' => 'Turismo de Ríos', 'slug' => 'turismo-rios', 'description' => 'Actividades fluviales'],
            ['name' => 'Turismo de Cascadas', 'slug' => 'turismo-cascadas', 'description' => 'Visitas a cascadas'],
            ['name' => 'Turismo de Parques Nacionales', 'slug' => 'turismo-parques-nacionales', 'description' => 'Exploración de parques nacionales'],
            ['name' => 'Turismo de Reservas Naturales', 'slug' => 'turismo-reservas-naturales', 'description' => 'Visitas a reservas naturales'],
            ['name' => 'Turismo de Museos', 'slug' => 'turismo-museos', 'description' => 'Visitas a museos'],
            ['name' => 'Turismo de Galerías', 'slug' => 'turismo-galerias', 'description' => 'Exploración de galerías de arte'],
            ['name' => 'Turismo de Ruinas', 'slug' => 'turismo-ruinas', 'description' => 'Visitas a sitios arqueológicos'],
            ['name' => 'Turismo de Castillos', 'slug' => 'turismo-castillos', 'description' => 'Exploración de castillos'],
            ['name' => 'Turismo de Catedrales', 'slug' => 'turismo-catedrales', 'description' => 'Visitas a catedrales'],
            ['name' => 'Turismo de Monasterios', 'slug' => 'turismo-monasterios', 'description' => 'Visitas a monasterios'],
            ['name' => 'Turismo de Templos', 'slug' => 'turismo-templos', 'description' => 'Exploración de templos'],
            ['name' => 'Turismo de Iglesias', 'slug' => 'turismo-iglesias', 'description' => 'Visitas a iglesias'],
            ['name' => 'Turismo de Miradores', 'slug' => 'turismo-miradores', 'description' => 'Vistas panorámicas'],
            ['name' => 'Turismo de Jardines', 'slug' => 'turismo-jardines', 'description' => 'Visitas a jardines'],
            ['name' => 'Turismo de Zoológicos', 'slug' => 'turismo-zoologicos', 'description' => 'Visitas a zoológicos'],
            ['name' => 'Turismo de Acuarios', 'slug' => 'turismo-acuarios', 'description' => 'Exploración de acuarios'],
            ['name' => 'Turismo de Parques Temáticos', 'slug' => 'turismo-parques-tematicos', 'description' => 'Visitas a parques temáticos'],
            ['name' => 'Turismo de Spas', 'slug' => 'turismo-spas', 'description' => 'Centros de bienestar'],
            ['name' => 'Turismo de Termas', 'slug' => 'turismo-termas', 'description' => 'Aguas termales'],
            ['name' => 'Turismo de Balnearios', 'slug' => 'turismo-balnearios', 'description' => 'Centros de salud'],
            ['name' => 'Turismo de Glaciares', 'slug' => 'turismo-glaciares', 'description' => 'Exploración de glaciares'],
            ['name' => 'Turismo de Volcanes', 'slug' => 'turismo-volcanes', 'description' => 'Visitas a volcanes'],
            ['name' => 'Turismo de Cuevas', 'slug' => 'turismo-cuevas', 'description' => 'Exploración de cuevas'],
            ['name' => 'Turismo de Grutas', 'slug' => 'turismo-grutas', 'description' => 'Visitas a grutas'],
            ['name' => 'Turismo de Viñedos', 'slug' => 'turismo-vinedos', 'description' => 'Rutas del vino'],
            ['name' => 'Turismo de Bodegas', 'slug' => 'turismo-bodegas', 'description' => 'Visitas a bodegas'],
            ['name' => 'Turismo de Mercados', 'slug' => 'turismo-mercados', 'description' => 'Exploración de mercados'],
            ['name' => 'Turismo de Festivales', 'slug' => 'turismo-festivales', 'description' => 'Eventos culturales'],
            ['name' => 'Turismo de Carnavales', 'slug' => 'turismo-carnavales', 'description' => 'Celebraciones populares'],
            ['name' => 'Turismo de Ferias', 'slug' => 'turismo-ferias', 'description' => 'Eventos comerciales'],
            ['name' => 'Turismo de Conciertos', 'slug' => 'turismo-conciertos', 'description' => 'Eventos musicales'],
            ['name' => 'Turismo de Teatro', 'slug' => 'turismo-teatro', 'description' => 'Espectáculos teatrales'],
            ['name' => 'Turismo de Danza', 'slug' => 'turismo-danza', 'description' => 'Espectáculos de danza'],
            ['name' => 'Turismo de Gastronomía Local', 'slug' => 'turismo-gastronomia-local', 'description' => 'Cocina tradicional'],
            ['name' => 'Turismo de Comida Callejera', 'slug' => 'turismo-comida-callejera', 'description' => 'Gastronomía popular'],
            ['name' => 'Turismo de Restaurantes', 'slug' => 'turismo-restaurantes', 'description' => 'Experiencias culinarias'],
            ['name' => 'Turismo de Bares', 'slug' => 'turismo-bares', 'description' => 'Vida nocturna'],
            ['name' => 'Turismo de Cafés', 'slug' => 'turismo-cafes', 'description' => 'Cultura del café'],
            ['name' => 'Turismo de Cervecerías', 'slug' => 'turismo-cervecerias', 'description' => 'Rutas de cerveza'],
            ['name' => 'Turismo de Montañismo', 'slug' => 'turismo-montanismo', 'description' => 'Escalada y montañismo'],
            ['name' => 'Turismo de Escalada', 'slug' => 'turismo-escalada', 'description' => 'Deportes de escalada'],
            ['name' => 'Turismo de Ciclismo', 'slug' => 'turismo-ciclismo', 'description' => 'Rutas en bicicleta'],
            ['name' => 'Turismo de Buceo', 'slug' => 'turismo-buceo', 'description' => 'Deportes acuáticos'],
            ['name' => 'Turismo de Snorkel', 'slug' => 'turismo-snorkel', 'description' => 'Exploración submarina'],
            ['name' => 'Turismo de Surf', 'slug' => 'turismo-surf', 'description' => 'Deportes de tabla'],
            ['name' => 'Turismo de Kayak', 'slug' => 'turismo-kayak', 'description' => 'Deportes de remo'],
            ['name' => 'Turismo de Rafting', 'slug' => 'turismo-rafting', 'description' => 'Deportes de río'],
            ['name' => 'Turismo de Pesca', 'slug' => 'turismo-pesca', 'description' => 'Deportes de pesca'],
            ['name' => 'Turismo de Golf', 'slug' => 'turismo-golf', 'description' => 'Campos de golf'],
            ['name' => 'Turismo de Esquí', 'slug' => 'turismo-esqui', 'description' => 'Deportes de nieve'],
            ['name' => 'Turismo de Snowboard', 'slug' => 'turismo-snowboard', 'description' => 'Deportes de tabla'],
            ['name' => 'Turismo de Patinaje', 'slug' => 'turismo-patinaje', 'description' => 'Deportes sobre hielo'],
            ['name' => 'Turismo de Parapente', 'slug' => 'turismo-parapente', 'description' => 'Deportes aéreos'],
            ['name' => 'Turismo de Vuelo en Globo', 'slug' => 'turismo-vuelo-globo', 'description' => 'Paseos en globo'],
            ['name' => 'Turismo de Helicóptero', 'slug' => 'turismo-helicoptero', 'description' => 'Vuelos panorámicos'],
            ['name' => 'Turismo de Safari', 'slug' => 'turismo-safari', 'description' => 'Exploración de fauna'],
            ['name' => 'Turismo de Observación de Aves', 'slug' => 'turismo-observacion-aves', 'description' => 'Birdwatching'],
            ['name' => 'Turismo de Avistamiento de Ballenas', 'slug' => 'turismo-avistamiento-ballenas', 'description' => 'Observación de cetáceos'],
            ['name' => 'Turismo de Avistamiento de Delfines', 'slug' => 'turismo-avistamiento-delfines', 'description' => 'Observación de delfines'],
            ['name' => 'Turismo de Cabalgata', 'slug' => 'turismo-cabalgata', 'description' => 'Paseos a caballo'],
            ['name' => 'Turismo de Globo Aerostático', 'slug' => 'turismo-globo-aerostatico', 'description' => 'Vuelos en globo'],
            ['name' => 'Turismo de Caminata Nocturna', 'slug' => 'turismo-caminata-nocturna', 'description' => 'Exploración nocturna'],
            ['name' => 'Turismo de Observación de Estrellas', 'slug' => 'turismo-observacion-estrellas', 'description' => 'Astroturismo'],
            ['name' => 'Turismo de Auroras Boreales', 'slug' => 'turismo-auroras-boreales', 'description' => 'Observación de auroras'],
            ['name' => 'Turismo de Fotografía', 'slug' => 'turismo-fotografia', 'description' => 'Fotografía de viajes'],
            ['name' => 'Turismo de Boutique', 'slug' => 'turismo-boutique', 'description' => 'Alojamientos exclusivos'],
            ['name' => 'Turismo de Resort', 'slug' => 'turismo-resort', 'description' => 'Complejos turísticos'],
            ['name' => 'Turismo All Inclusive', 'slug' => 'turismo-all-inclusive', 'description' => 'Paquetes todo incluido'],
            ['name' => 'Turismo de Hostal', 'slug' => 'turismo-hostal', 'description' => 'Alojamiento económico'],
            ['name' => 'Turismo de Bed & Breakfast', 'slug' => 'turismo-bed-breakfast', 'description' => 'Alojamiento familiar'],
            ['name' => 'Turismo de Glamping', 'slug' => 'turismo-glamping', 'description' => 'Camping de lujo'],
            ['name' => 'Turismo de Casa Rural', 'slug' => 'turismo-casa-rural', 'description' => 'Alojamiento rural'],
            ['name' => 'Turismo de Cabaña', 'slug' => 'turismo-cabana', 'description' => 'Alojamiento en cabañas'],
            ['name' => 'Turismo de Refugio', 'slug' => 'turismo-refugio', 'description' => 'Alojamiento de montaña'],
            ['name' => 'Turismo de Aventura Extrema', 'slug' => 'turismo-aventura-extrema', 'description' => 'Deportes extremos'],
            ['name' => 'Turismo de Deportes Acuáticos', 'slug' => 'turismo-deportes-acuaticos', 'description' => 'Actividades acuáticas'],
            ['name' => 'Turismo de Deportes de Nieve', 'slug' => 'turismo-deportes-nieve', 'description' => 'Actividades invernales'],
            ['name' => 'Turismo de Deportes de Montaña', 'slug' => 'turismo-deportes-montana', 'description' => 'Actividades de montaña'],
            ['name' => 'Turismo de Deportes de Aventura', 'slug' => 'turismo-deportes-aventura', 'description' => 'Deportes de aventura'],
            ['name' => 'Turismo Sostenible', 'slug' => 'turismo-sostenible', 'description' => 'Turismo responsable']
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
