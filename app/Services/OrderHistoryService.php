<?php

namespace App\Services;

use App\Models\OrdenRestaurante;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class OrderHistoryService
{
    public function applyPeriodFilter(\Illuminate\Database\Eloquent\Builder $query, string $period, ?string $date = null): \Illuminate\Database\Eloquent\Builder
    {
        $targetDate = $date ? Carbon::parse($date) : Carbon::now();

        switch ($period) {
            case 'diario':
                $query->whereDate('fecha_orden', $targetDate->toDateString());
                break;
            case 'semanal':
                $query->whereBetween('fecha_orden', [$targetDate->startOfWeek()->toDateString(), $targetDate->endOfWeek()->toDateString()]);
                break;
            case 'quincenal':
                if ($targetDate->day <= 15) {
                    $query->whereBetween('fecha_orden', [
                        $targetDate->copy()->startOfMonth()->toDateString(),
                        $targetDate->copy()->day(15)->toDateString()
                    ]);
                } else {
                    $query->whereBetween('fecha_orden', [
                        $targetDate->copy()->day(16)->toDateString(),
                        $targetDate->copy()->endOfMonth()->toDateString()
                    ]);
                }
                break;
            case 'mensual':
                $query->whereBetween('fecha_orden', [$targetDate->startOfMonth()->toDateString(), $targetDate->endOfMonth()->toDateString()]);
                break;
        }

        return $query;
    }
}
