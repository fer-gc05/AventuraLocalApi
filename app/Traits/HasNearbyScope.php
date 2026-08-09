<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasNearbyScope
{
    protected function nearbyQuery(Builder $query, float $lat, float $lng, float $radius): Builder
    {
        $haversine = "(6371 * acos(
            cos(radians(?)) * cos(radians(latitude))
            * cos(radians(longitude) - radians(?))
            + sin(radians(?)) * sin(radians(latitude))
        ))";

        $distanceAlias = 'distance';

        return $query
            ->selectRaw("*, {$haversine} AS {$distanceAlias}", [$lat, $lng, $lat])
            ->whereRaw("{$haversine} < ?", [$lat, $lng, $lat, $radius])
            ->orderByRaw("{$haversine}", [$lat, $lng, $lat]);
    }
}
