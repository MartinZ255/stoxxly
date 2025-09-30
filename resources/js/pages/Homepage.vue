<!-- resources/js/Pages/HomePage.vue -->
<script setup lang="ts">
import { Head } from "@inertiajs/vue3"
import AppLayout from "../App.vue"
import { ref } from "vue"

// Dummy-Daten für Dashboard-Boxen
const watchlistPreview = ref([
    { symbol: "BTCUSDT", change: +2.3 },
    { symbol: "ETHUSDT", change: -1.1 },
])

const joinedCommunities = ref([
    { id: 2, name: "Altcoin Gang", members: 87 },
    { id: 3, name: "Daytraders", members: 205 },
])
</script>

<template>
    <Head title="Home" />

    <div class="space-y-10">
        <!-- Begrüßung -->
        <section
            class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 transition-colors duration-300"
        >
            <h1 class="text-2xl font-bold mb-2 text-gray-900 dark:text-white">
                Willkommen zurück!
            </h1>
            <p class="text-gray-600 dark:text-gray-300">
                Überblick über deine Watchlist, Communitys und Markttrends.
            </p>
        </section>

        <!-- Watchlist Vorschau -->
        <section
            class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 transition-colors duration-300"
        >
            <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">
                Meine Watchlist (Kurz)
            </h2>
            <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                <li
                    v-for="item in watchlistPreview"
                    :key="item.symbol"
                    class="py-2 flex justify-between text-gray-800 dark:text-gray-200"
                >
                    <span>{{ item.symbol }}</span>
                    <span
                        :class="item.change >= 0
              ? 'text-green-600 dark:text-green-400'
              : 'text-red-500 dark:text-red-400'"
                    >
                        {{ item.change }}%
                    </span>
                </li>
            </ul>
            <div class="text-right mt-3">
                <a
                    href="/watchlist"
                    class="text-blue-500 dark:text-blue-400 hover:underline"
                >
                    Zur Watchlist →
                </a>
            </div>
        </section>

        <!-- Community Vorschau -->
        <section
            class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 transition-colors duration-300"
        >
            <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">
                Meine Communitys
            </h2>
            <div
                v-if="joinedCommunities.length === 0"
                class="text-gray-500 dark:text-gray-400"
            >
                Noch keiner Community beigetreten.
            </div>
            <div v-else class="space-y-2">
                <div
                    v-for="c in joinedCommunities"
                    :key="c.id"
                    class="p-3 bg-blue-50 dark:bg-blue-900/30 rounded
                 text-gray-800 dark:text-gray-200"
                >
                    {{ c.name }} ({{ c.members }} Mitglieder)
                </div>
            </div>
            <div class="text-right mt-3">
                <a
                    href="/communitys"
                    class="text-blue-500 dark:text-blue-400 hover:underline"
                >
                    Alle Communitys →
                </a>
            </div>
        </section>
    </div>
</template>

<script lang="ts">
export default {
    layout: AppLayout,
}
</script>
