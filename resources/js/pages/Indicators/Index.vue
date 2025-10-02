<script setup lang="ts">
import { Head, Link } from "@inertiajs/vue3"
import AppLayout from "../../App.vue"
import { route } from "ziggy-js"
import { ref } from "vue"

interface Indicator {
    id: number
    name: string
    slug: string
    description: string
}

const indicators = ref<Indicator[]>([
    {
        id: 1,
        name: "RSI (Relative Strength Index)",
        slug: "rsi",
        description: "Misst die Geschwindigkeit und Veränderung von Preisbewegungen.",
    },
    {
        id: 2,
        name: "MACD (Moving Average Convergence Divergence)",
        slug: "macd",
        description: "Trendfolge-Indikator, zeigt Stärke, Richtung und Dauer eines Trends.",
    },
    {
        id: 3,
        name: "SMA (Simple Moving Average)",
        slug: "sma",
        description: "Einfacher gleitender Durchschnitt zur Glättung von Kursbewegungen.",
    },
    {
        id: 4,
        name: "Bollinger Bands",
        slug: "bollinger",
        description: "Zeigen Volatilität und mögliche Trendwenden durch obere/untere Bänder.",
    },
])
</script>

<template>
    <Head title="Indikatoren" />

    <div class="space-y-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
            Technische Indikatoren
        </h1>

        <p class="text-gray-600 dark:text-gray-300">
            Wähle einen Indikator aus, um weitere Informationen und Berechnungen zu erhalten.
        </p>

        <div class="grid gap-6 md:grid-cols-2">
            <div
                v-for="indicator in indicators"
                :key="indicator.id"
                class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow transition-colors"
            >
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ indicator.name }}
                </h2>
                <p class="text-gray-600 dark:text-gray-300 text-sm mb-3">
                    {{ indicator.description }}
                </p>

                <!-- Mehr erfahren Link -->
                <Link
                    :href="route('indicators.show', indicator.slug)"
                    class="px-3 py-1 rounded bg-blue-500 hover:bg-blue-600 text-white text-sm transition-colors"
                >
                    Mehr erfahren →
                </Link>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
export default {
    layout: AppLayout,
}
</script>
