<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return [
        'info' => [
            'description' => 'Api para gestion turistica',
            'version' => '1.0.0',
            'status_description' => 'En desarrollo',
            'documentation' => 'http://127.0.0.1:8000/docs/api'
        ]
    ];
});
