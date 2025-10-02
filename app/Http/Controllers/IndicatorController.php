<?php

namespace App\Http\Controllers;

use App\Models\Indicator;
use Inertia\Inertia;

class IndicatorController extends Controller
{
    public function index()
    {
        $indicators = Indicator::select('id', 'name', 'slug')->get()
            ->map(function ($indicator) {
                $details = config("indicators.{$indicator->slug}");
                return [
                    'id' => $indicator->id,
                    'name' => $indicator->name,
                    'slug' => $indicator->slug,
                    'description' => $details['description'] ?? '',
                ];
            });

        return Inertia::render('Indicators/Index', [
            'indicators' => $indicators,
        ]);
    }


    public function show($slug)
    {
        $indicator = Indicator::where('slug', $slug)->firstOrFail();
        $details = config("indicators.$slug"); // Holt die Infos aus config/indicators.php

        return Inertia::render('Indicators/Show', [
            'indicator' => array_merge($indicator->toArray(), $details ?? []),
        ]);
    }
}
