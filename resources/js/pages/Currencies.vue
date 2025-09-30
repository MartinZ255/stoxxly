<!-- resources/js/Pages/HomePage.vue -->
<script setup lang="ts">
import { Head } from "@inertiajs/vue3"
import AppLayout from "../App.vue"
import CryptoSearch from "../components/crypto/CryptoSearch.vue"
import Chart from "../components/crypto/Chart.vue"
import AddToButtons from "../components/UserInterface/AddToButtons.vue"
import { ref } from "vue"

// States für Auswahl
const selectedSymbol = ref("BTCUSDT")
const selectedInterval = ref("1m")

// Handler für Suche
function searchCrypto(symbol: string) {
    console.log("Crypto gesucht:", symbol)
    selectedSymbol.value = symbol // 🔹 Chart aktualisiert sich automatisch
}
</script>

<template>
    <Head title="Home" />

    <!-- 🔎 CryptoSearch eingebunden -->
    <CryptoSearch @search="searchCrypto" />

    <div class="h-[20px]"></div>

    <div class="flex gap-8">
        <!-- Chart nur halbe Breite -->
        <div class="w-2/3">
            <label class="block mt-4 text-gray-700 dark:text-gray-200">
                Intervall:
                <select
                    v-model="selectedInterval"
                    class="ml-2 border rounded px-2 py-1
                 border-gray-300 dark:border-gray-600
                 bg-white dark:bg-gray-800
                 text-gray-800 dark:text-gray-200"
                >
                    <option value="1m">1m</option>
                    <option value="5m">5m</option>
                    <option value="15m">15m</option>
                    <option value="1h">1h</option>
                    <option value="4h">4h</option>
                    <option value="1d">1d</option>
                </select>
            </label>

            <div
                class="p-4 bg-gray-50 dark:bg-gray-900
               border border-gray-200 dark:border-gray-700
               rounded-lg shadow transition-colors"
            >
                <Chart :symbol="selectedSymbol" :interval="selectedInterval" />
            </div>
        </div>

        <!-- Rechts daneben Infos -->
        <div class="w-1/3 bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <h2 class="text-lg font-bold mb-2 text-gray-900 dark:text-white">
                Infos
            </h2>
            <p class="text-gray-700 dark:text-gray-300">
                Hier kannst du Details zum gewählten Coin anzeigen.
            </p>
        </div>
    </div>

    <div>
        <AddToButtons
            class="mt-4"
            :symbol-details="selectedSymbol"
            :add-disabled="true"
            :alert-disabled="true"
            @add-to-watchlist="123"
            @set-price-alert="123"
        />
    </div>
</template>

<script lang="ts">
export default {
    layout: AppLayout,
}
</script>
