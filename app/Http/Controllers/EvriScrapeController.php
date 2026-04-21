<?php

namespace App\Http\Controllers;

use App\Models\EvriScrape;
use Inertia\Inertia;
use Inertia\Response;

class EvriScrapeController extends Controller
{
    public function index(): Response
    {
        $scrapes = EvriScrape::orderByDesc('date')
            ->orderByDesc('created_at')
            ->paginate(20)
            ->through(fn (EvriScrape $s) => [
                'id' => $s->id,
                'date' => $s->date->toDateString(),
                'round' => $s->round ?: null,
                'earnings' => $s->earnings,
                'parcel_count' => $s->parcel_count,
                'execution_time' => $s->execution_time,
                'status' => $s->status,
                'execution_id' => $s->execution_id,
                'created_at' => $s->created_at->toDateTimeString(),
            ]);

        $completed = EvriScrape::completed();

        $stats = [
            'total_earnings' => (float) (clone $completed)->sum('earnings'),
            'total_parcels' => (int) (clone $completed)->sum('parcel_count'),
            'completed_count' => (clone $completed)->count(),
            'total_count' => EvriScrape::count(),
            'avg_earnings' => round((float) (clone $completed)->avg('earnings'), 2),
            'avg_parcels' => round((float) (clone $completed)->avg('parcel_count'), 1),
        ];

        return Inertia::render('scrapes/Index', [
            'scrapes' => $scrapes,
            'stats' => $stats,
        ]);
    }
}
