<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Indicator;

class IndicatorSeeder extends Seeder
{
    public function run(): void
    {
        $indicators = [
            ['slug' => 'rsi', 'name' => 'Relative Strength Index (RSI)', 'timeframe' => '14d'],
            ['slug' => 'macd', 'name' => 'Moving Average Convergence Divergence (MACD)', 'timeframe' => null],
            ['slug' => 'sma', 'name' => 'Simple Moving Average (SMA)', 'timeframe' => null],
            ['slug' => 'bollinger', 'name' => 'Bollinger Bands', 'timeframe' => null],
        ];

        foreach ($indicators as $indicator) {
            \App\Models\Indicator::updateOrCreate(
                ['slug' => $indicator['slug']],
                $indicator
            );
        }
    }

}
