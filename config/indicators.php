<?php

return [

    'rsi' => [
        'name' => 'Relative Strength Index (RSI)',
        'description' => 'Der RSI misst die Geschwindigkeit und Veränderung von Kursbewegungen, um überkaufte oder überverkaufte Situationen zu identifizieren.',
        'formula' => 'RSI = 100 - (100 / (1 + RS)), wobei RS = Durchschnitt der Aufwärtsbewegungen / Durchschnitt der Abwärtsbewegungen.',
        'use_cases' => 'Erkennung von Marktphasen, in denen eine Trendumkehr wahrscheinlich ist.',
        'complexity' => 'basic',
        'category' => 'Momentum',
    ],

    'sma' => [
        'name' => 'Simple Moving Average (SMA)',
        'description' => 'Ein einfacher gleitender Durchschnitt über die letzten n Schlusskurse.',
        'formula' => 'SMA = (Summe der Schlusskurse der letzten n Perioden) / n',
        'use_cases' => 'Glättung von Kursbewegungen, Trendbestätigung.',
        'complexity' => 'basic',
        'category' => 'Trend',
    ],

    'ema' => [
        'name' => 'Exponential Moving Average (EMA)',
        'description' => 'Der EMA gewichtet die jüngsten Kurse stärker und reagiert schneller auf Kursänderungen.',
        'formula' => 'EMA = (Schlusskurs - EMA_vorher) * (2 / (n+1)) + EMA_vorher',
        'use_cases' => 'Bessere Reaktion auf neue Trends im Vergleich zum SMA.',
        'complexity' => 'basic',
        'category' => 'Trend',
    ],

    'macd' => [
        'name' => 'Moving Average Convergence Divergence (MACD)',
        'description' => 'Der MACD zeigt die Differenz zwischen zwei EMAs und wird mit einer Signallinie verglichen.',
        'formula' => 'MACD = EMA(12) - EMA(26), Signallinie = EMA(9) des MACD.',
        'use_cases' => 'Identifizierung von Kauf-/Verkaufssignalen anhand von Linienkreuzungen.',
        'complexity' => 'intermediate',
        'category' => 'Momentum',
    ],

    'bollinger' => [
        'name' => 'Bollinger Bands',
        'description' => 'Bänder, die auf dem SMA basieren und die Volatilität des Marktes anzeigen.',
        'formula' => 'Oberes Band = SMA + (k * Standardabweichung), Unteres Band = SMA - (k * Standardabweichung).',
        'use_cases' => 'Messung der Marktvolatilität, Identifizierung von Ausbrüchen.',
        'complexity' => 'intermediate',
        'category' => 'Volatilität',
    ],

    'test123' => [
        'name' => 'test',
        'description' => 'Bänder, die auf dem SMA basieren und die Volatilität des Marktes anzeigen.',
        'formula' => 'Oberes Band = SMA + (k * Standardabweichung), Unteres Band = SMA - (k * Standardabweichung).',
        'use_cases' => 'Messung der Marktvolatilität, Identifizierung von Ausbrüchen.',
        'complexity' => 'intermediate',
        'category' => 'Volatilität',
    ],

];
