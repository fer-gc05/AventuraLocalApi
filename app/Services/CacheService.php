<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;

class CacheService
{
    /**
     * Obtiene datos del caché o los genera si no existen
     */
    public static function remember(string $key, $callback, int $minutes = 10)
    {
        return Cache::remember($key, now()->addMinutes($minutes), $callback);
    }

    /**
     * Limpia el caché de un modelo específico
     */
    public static function flushModelCache(string $modelName)
    {
        Cache::tags([strtolower($modelName)])->flush();
    }

    /**
     * Genera una clave única para el caché basada en los parámetros
     */
    public static function generateKey(string $prefix, array $params = []): string
    {
        return $prefix . '_' . md5(json_encode($params));
    }

    /**
     * Limpia el caché relacionado con un modelo
     */
    public static function clearModelCache(Model $model)
    {
        $modelName = strtolower(class_basename($model));
        self::flushModelCache($modelName);
    }
} 