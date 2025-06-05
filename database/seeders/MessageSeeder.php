<?php

namespace Database\Seeders;

use App\Models\Message;
use App\Models\User;
use App\Models\Community;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MessageSeeder extends Seeder
{
    public function run(): void
    {
        Message::truncate();

        $users = User::all();
        $communities = Community::all();
        $mensajes = [
            '¡Hola! ¿Alguien ha visitado este lugar?',
            '¿Recomiendan algún restaurante cerca?',
            '¿Qué actividades hay para niños?',
            '¿Cómo es el clima en esta época?',
            '¿Alguien va este fin de semana?',
            '¿Se puede acampar en la zona?',
            '¿Cuánto cuesta la entrada?',
            '¿Es seguro viajar solo?',
            '¿Qué transporte recomiendan?',
            '¡Gracias por la información!'
        ];

        // Mensajes privados entre usuarios
        for ($i = 0; $i < 40; $i++) {
            $sender = $users->random();
            $receiver = $users->where('id', '!=', $sender->id)->random();
            Message::create([
                'content' => $mensajes[array_rand($mensajes)],
                'sender_id' => $sender->id,
                'receiver_id' => $receiver->id,
                'community_id' => null,
                'parent_id' => null,
                'is_read' => rand(0, 1)
            ]);
        }

        // Mensajes en comunidades
        foreach ($communities as $community) {
            $miembros = $community->users;
            $num = rand(5, 15);
            for ($i = 0; $i < $num; $i++) {
                $sender = $miembros->random();
                Message::create([
                    'content' => $mensajes[array_rand($mensajes)],
                    'sender_id' => $sender->id,
                    'receiver_id' => null,
                    'community_id' => $community->id,
                    'parent_id' => null,
                    'is_read' => rand(0, 1)
                ]);
            }
        }
    }
} 