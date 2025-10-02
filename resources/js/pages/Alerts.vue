<script setup lang="ts">
import { Head } from "@inertiajs/vue3"
import AppLayout from "../App.vue"
import { ref } from "vue"
import { BellAlertIcon, BellSlashIcon, PencilSquareIcon, TrashIcon } from "@heroicons/vue/24/outline"

interface Alert {
    id: number
    symbol: string
    name: string
    conditions: string[]
    active: boolean
}

const alerts = ref<Alert[]>([
    {
        id: 1,
        symbol: "BTCUSDT",
        name: "BTC SMA Check",
        conditions: ["SMA(50) < BTC Preis", "RSI > 70"],
        active: true,
    },
    {
        id: 2,
        symbol: "ETHUSDT",
        name: "ETH Volatilität",
        conditions: ["Volatilität > 10%"],
        active: false,
    },
    {
        id: 3,
        symbol: "SOLUSDT",
        name: "SOL Preis-Alarm",
        conditions: ["Preis > 200 USD"],
        active: true,
    },
])

function toggleAlert(alert: Alert) {
    alert.active = !alert.active
}

function editAlert(alert: Alert) {
    window.alert(`Bearbeiten von: ${alert.name}`)
}

function deleteAlert(alertId: number) {
    alerts.value = alerts.value.filter((a) => a.id !== alertId)
}
</script>

<template>
    <Head title="Alerts" />

    <div class="space-y-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
            Meine Alerts
        </h1>

        <div v-if="alerts.length === 0" class="text-gray-500 dark:text-gray-400">
            Noch keine Alerts eingerichtet.
        </div>

        <div
            v-else
            class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-x-auto transition-colors"
        >
            <table class="min-w-full text-sm">
                <thead>
                <tr
                    class="border-b border-gray-200 dark:border-gray-700 text-left"
                >
                    <th class="py-2 px-3 text-gray-700 dark:text-gray-300">Symbol</th>
                    <th class="py-2 px-3 text-gray-700 dark:text-gray-300">Name</th>
                    <th class="py-2 px-3 text-gray-700 dark:text-gray-300">Bedingungen</th>
                    <th class="py-2 px-3 text-gray-700 dark:text-gray-300">Status</th>
                    <th class="py-2 px-3"></th>
                </tr>
                </thead>
                <tbody>
                <tr
                    v-for="alert in alerts"
                    :key="alert.id"
                    class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                >
                    <!-- Symbol -->
                    <td class="py-2 px-3 font-mono text-gray-900 dark:text-white">
                        {{ alert.symbol }}
                    </td>

                    <!-- Name -->
                    <td class="py-2 px-3 font-medium text-gray-900 dark:text-white">
                        {{ alert.name }}
                    </td>

                    <!-- Bedingungen -->
                    <td class="py-2 px-3 text-gray-800 dark:text-gray-200">
                        <ul class="list-disc list-inside space-y-1">
                            <li v-for="(cond, idx) in alert.conditions" :key="idx">
                                {{ cond }}
                            </li>
                        </ul>
                    </td>

                    <!-- Status -->
                    <td class="py-2 px-3">
                        <span
                            :class="alert.active ? 'text-green-600 dark:text-green-400' : 'text-yellow-600 dark:text-yellow-400'"
                        >
                            {{ alert.active ? 'Aktiv' : 'Inaktiv' }}
                        </span>
                    </td>

                    <!-- Aktionen -->
                    <td class="py-2 px-3 text-right flex justify-end gap-2">
                        <!-- Aktivieren/Deaktivieren -->
                        <button
                            @click="toggleAlert(alert)"
                            :class="alert.active
                                ? 'flex items-center gap-1 px-2 py-1 rounded bg-yellow-500 hover:bg-yellow-600 text-white text-xs'
                                : 'flex items-center gap-1 px-2 py-1 rounded bg-green-500 hover:bg-green-600 text-white text-xs'"
                        >
                            <component
                                :is="alert.active ? BellSlashIcon : BellAlertIcon"
                                class="h-4 w-4"
                            />
                            {{ alert.active ? 'Deaktivieren' : 'Aktivieren' }}
                        </button>


                        <!-- Bearbeiten -->
                        <button
                            @click="editAlert(alert)"
                            class="flex items-center gap-1 px-2 py-1 rounded bg-blue-500 hover:bg-blue-600 text-white text-xs"
                        >
                            <PencilSquareIcon class="h-4 w-4" />
                            Bearbeiten
                        </button>

                        <!-- Löschen -->
                        <button
                            @click="deleteAlert(alert.id)"
                            class="flex items-center gap-1 px-2 py-1 rounded bg-red-500 hover:bg-red-600 text-white text-xs"
                        >
                            <TrashIcon class="h-4 w-4" />
                            Löschen
                        </button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script lang="ts">
export default { layout: AppLayout }
</script>
