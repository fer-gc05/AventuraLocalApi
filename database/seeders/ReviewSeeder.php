<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\User;
use App\Models\Destination;
use App\Models\Tour;
use App\Models\Route;
use App\Models\Event;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        //Review::truncate();

        $users = User::role('Traveler')->get();
        if ($users->isEmpty()) {
            throw new \Exception('No hay usuarios con rol Traveler. Por favor, ejecuta el UserSeeder primero.');
        }

        $destinations = Destination::all();
        if ($destinations->isEmpty()) {
            throw new \Exception('No hay destinos disponibles. Por favor, ejecuta el DestinationSeeder primero.');
        }

        $tours = Tour::all();
        if ($tours->isEmpty()) {
            throw new \Exception('No hay tours disponibles. Por favor, ejecuta el TourSeeder primero.');
        }

        $routes = Route::all();
        if ($routes->isEmpty()) {
            throw new \Exception('No hay rutas disponibles. Por favor, ejecuta el RouteSeeder primero.');
        }

        $events = Event::all();
        if ($events->isEmpty()) {
            throw new \Exception('No hay eventos disponibles. Por favor, ejecuta el EventSeeder primero.');
        }

        $comentarios = [
            '¡Una experiencia increíble en el río, totalmente recomendado!',
            'El servicio fue excelente, muy buena atención al cliente.',
            'El lugar es espectacular, ideal para relajarse y disfrutar de la naturaleza.',
            'Todo estaba perfectamente organizado, ¡felicitaciones al equipo!',
            'La comida típica de Córdoba fue lo mejor del viaje.',
            'El guía compartió datos históricos fascinantes, muy profesional.',
            'Perfecto para disfrutar en familia o con amigos.',
            'Superó todas mis expectativas, quiero volver pronto.',
            'Un poco caro, pero la experiencia valió cada peso.',
            'El lugar podría mejorar en limpieza, pero aún así lo recomiendo.',
        ];

        $estados = ['pending', 'approved', 'rejected'];

        foreach ($destinations as $destination) {
            $numReviews = rand(3, 6);
            for ($i = 0; $i < $numReviews; $i++) {
                $user = $users->random();
                Review::create([
                    'content' => $comentarios[array_rand($comentarios)],
                    'rating' => rand(3, 5),
                    'user_id' => $user->id,
                    'reviewable_id' => $destination->id,
                    'reviewable_type' => Destination::class,
                    'status' => $estados[array_rand($estados)],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        foreach ($tours as $tour) {
            $numReviews = rand(2, 5);
            for ($i = 0; $i < $numReviews; $i++) {
                $user = $users->random();
                Review::create([
                    'content' => $comentarios[array_rand($comentarios)],
                    'rating' => rand(3, 5),
                    'user_id' => $user->id, // Corregido: usar $user->id en lugar de $tour->id
                    'reviewable_id' => $tour->id,
                    'reviewable_type' => Tour::class,
                    'status' => $estados[array_rand($estados)],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        foreach ($routes as $route) {
            $numReviews = rand(1, 4);
            for ($i = 0; $i < $numReviews; $i++) {
                $user = $users->random();
                Review::create([
                    'content' => $comentarios[array_rand($comentarios)],
                    'rating' => rand(3, 5),
                    'user_id' => $user->id,
                    'reviewable_id' => $route->id,
                    'reviewable_type' => Route::class,
                    'status' => $estados[array_rand($estados)],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        foreach ($events as $event) {
            $numReviews = rand(1, 3);
            for ($i = 0; $i < $numReviews; $i++) {
                $user = $users->random();
                Review::create([
                    'content' => $comentarios[array_rand($comentarios)],
                    'rating' => rand(3, 5),
                    'user_id' => $user->id,
                    'reviewable_id' => $event->id,
                    'reviewable_type' => Event::class,
                    'status' => $estados[array_rand($estados)],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

    }
}