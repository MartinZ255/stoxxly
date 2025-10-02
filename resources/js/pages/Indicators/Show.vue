<script setup lang="ts">
import { Head, Link } from "@inertiajs/vue3"
import AppLayout from "../../App.vue"
import { route } from 'ziggy-js';

// Typisierung für den Indikator
interface Indicator {
    id: number
    name: string
    slug: string
    description: string
    formula: string
    use_cases: string
    category: string
    complexity: string
}

// Props von Laravel
defineProps<{
    indicator: Indicator
}>()
</script>

<template>
    <Head :title="indicator.name" />

    <div class="space-y-6">
        <!-- Zurück-Link -->
        <Link
            :href="route('indicators.index')"
            class="text-blue-500 hover:underline"
        >
            ← Zurück zu allen Indikatoren
        </Link>

        <!-- Titel -->
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
            {{ indicator.name }}
        </h1>

        <!-- Kurzbeschreibung -->
        <p class="text-gray-700 dark:text-gray-300">
            {{ indicator.description }}
        </p>

        <!-- Formel -->
        <section class="mt-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                Formel
            </h2>
            <pre
                class="bg-gray-100 dark:bg-gray-800 text-sm p-4 rounded mt-2 overflow-x-auto"
            >
{{ indicator.formula }}
            </pre>
        </section>

        <!-- Anwendungsfälle -->
        <section class="mt-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                Typische Anwendungsfälle
            </h2>
            <p class="text-gray-700 dark:text-gray-300 mt-2 whitespace-pre-line">
                {{ indicator.use_cases }}
            </p>
        </section>

        <!-- Kategorie & Komplexität -->
        <div class="flex items-center gap-2 mt-6">
            <span
                class="px-3 py-1 rounded bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200 text-xs font-medium"
            >
                {{ indicator.category }}
            </span>
            <span
                class="px-3 py-1 rounded bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-gray-200 text-xs font-medium"
            >
                {{ indicator.complexity }}
            </span>
        </div>
    </div>
</template>

<script lang="ts">
export default {
    layout: AppLayout,
}
</script>
