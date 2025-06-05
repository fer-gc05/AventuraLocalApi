<?php

namespace Database\Seeders;

use App\Models\Reservation;
use App\Models\User;
use App\Models\Tour;
use App\Models\Destination;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ReservationSeeder extends Seeder
{
    public function run(): void
    {
        //Reservation::truncate();

        $users = User::role('Traveler')->get();
        $tours = Tour::all();
        $destinations = Destination::all();
        $estados = ['pending', 'confirmed', 'cancelled', 'completed'];

        foreach ($tours as $tour) {
            $num = rand(2, 5);
            for ($i = 0; $i < $num; $i++) {
                $user = $users->random();
                $start = Carbon::parse($tour->start_date)->subDays(rand(1, 10));
                $end = Carbon::parse($tour->end_date);
                Reservation::create([
                    'user_id' => $user->id,
                    'tour_id' => $tour->id,
                    'destination_id' => null,
                    'start_date' => $start,
                    'end_date' => $end,
                    'participants' => rand(1, 5),
                    'total_price' => $tour->price * rand(1, 5),
                    'currency' => 'COP',
                    'status' => $estados[array_rand($estados)],
                    'special_requests' => rand(0, 1) ? 'Solicito menú vegetariano.' : null
                ]);
            }
        }

        // Reservas de destinos (sin tour)
        foreach ($destinations as $destination) {
            $num = rand(1, 3);
            for ($i = 0; $i < $num; $i++) {
                $user = $users->random();
                $start = Carbon::now()->addDays(rand(1, 60));
                $end = (clone $start)->addDays(rand(1, 3));
                Reservation::create([
                    'user_id' => $user->id,
                    'tour_id' => null,
                    'destination_id' => $destination->id,
                    'start_date' => $start,
                    'end_date' => $end,
                    'participants' => rand(1, 4),
                    'total_price' => rand(50000, 300000),
                    'currency' => 'COP',
                    'status' => $estados[array_rand($estados)],
                    'special_requests' => rand(0, 1) ? 'Necesito acceso para silla de ruedas.' : null
                ]);
            }
        }
    }
} 