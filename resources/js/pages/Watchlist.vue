<script setup lang="ts">
import { Head, router } from "@inertiajs/vue3"
import AppLayout from "../App.vue"
import { ref, onMounted } from "vue"
import { BellAlertIcon, ArrowTopRightOnSquareIcon, TrashIcon } from "@heroicons/vue/24/outline"

interface WatchlistItem {
    symbol: string
    price: number
    change24h: number
}

const watchlist = ref<WatchlistItem[]>([])

onMounted(() => {
    watchlist.value = [
        { symbol: "BTCUSDT", price: 67234.12, change24h: 2.3 },
        { symbol: "ETHUSDT", price: 2431.88, change24h: -1.1 },
    ]
})

function removeSymbol(symbol: string) {
    watchlist.value = watchlist.value.filter((item) => item.symbol !== symbol)
}

function goToCurrency(symbol: string) {
    router.visit(`/currencies?symbol=${symbol}`)
}

function createAlert(symbol: string) {
    alert(`Alert für ${symbol} erstellen`)
}
</script>

<template>
    <Head title="Watchlist" />

    <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">
        Meine Watchlist
    </h1>

    <div v-if="watchlist.length === 0" class="text-gray-500 dark:text-gray-400">
        Noch keine Symbole in der Watchlist.
    </div>

    <div
        v-else
        class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 overflow-x-auto
           transition-colors duration-300"
    >
        <table class="min-w-full text-sm">
            <thead>
            <tr
                class="border-b border-gray-200 dark:border-gray-700 text-left
                 text-gray-700 dark:text-gray-200"
            >
                <th class="py-2 px-3">Symbol</th>
                <th class="py-2 px-3">Preis</th>
                <th class="py-2 px-3">24h</th>
                <th class="py-2 px-3"></th>
            </tr>
            </thead>
            <tbody>
            <tr
                v-for="item in watchlist"
                :key="item.symbol"
                class="border-b border-gray-200 dark:border-gray-700
                 hover:bg-gray-50 dark:hover:bg-gray-700
                 transition-colors"
            >
                <td class="py-2 px-3 font-semibold text-gray-900 dark:text-white">
                    {{ item.symbol }}
                </td>
                <td class="py-2 px-3 text-gray-800 dark:text-gray-200">
                    {{ item.price.toLocaleString('de-DE', { minimumFractionDigits: 2 }) }} $
                </td>
                <td
                    class="py-2 px-3"
                    :class="item.change24h >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-500 dark:text-red-400'"
                >
                    {{ item.change24h }} %
                </td>
                <td class="py-2 px-4 text-right flex justify-end gap-4">
                    <!-- Alert Button -->
                    <button
                        @click="createAlert(item.symbol)"
                        class="flex items-center gap-1 text-yellow-500 hover:text-yellow-600 dark:text-yellow-400 dark:hover:text-yellow-300 text-sm"
                    >
                        <BellAlertIcon class="h-5 w-5" />
                        Alert
                    </button>

                    <!-- Springe zur Währung -->
                    <button
                        @click="goToCurrency(item.symbol)"
                        class="flex items-center gap-1 text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 text-sm"
                    >
                        <ArrowTopRightOnSquareIcon class="h-5 w-5" />
                        Zur Währung
                    </button>

                    <!-- Entfernen -->
                    <button
                        @click="removeSymbol(item.symbol)"
                        class="flex items-center gap-1 text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 text-sm"
                    >
                        <TrashIcon class="h-5 w-5" />
                        Entfernen
                    </button>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script lang="ts">
export default {
    layout: AppLayout,
}
</script>
