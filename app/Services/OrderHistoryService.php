<?php

namespace App\Services;

use App\Models\OrdenRestaurante;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class OrderHistoryService
{
    public function applyDailyFilter(\Illuminate\Database\Eloquent\Builder $query, ?string $date = null): \Illuminate\Database\Eloquent\Builder
    {
        $targetDate = $date ? Carbon::parse($date) : Carbon::today();

        return $query->whereDate('fecha_orden', $targetDate->toDateString());
    }
}
