<script setup lang="ts">
import { Head } from "@inertiajs/vue3"
import AppLayout from "../App.vue"
import { ref, computed, onMounted } from "vue"
import {
    ChartBarIcon,
    InformationCircleIcon,
    UserMinusIcon,
    UserPlusIcon,
} from "@heroicons/vue/24/outline"

interface Community {
    id: number
    name: string
    description: string
    members: number
    joined: boolean
}

const communities = ref<Community[]>([])

onMounted(() => {
    communities.value = [
        {
            id: 1,
            name: "BTC Lovers",
            description: "Alles rund um Bitcoin",
            members: 124,
            joined: false,
        },
        {
            id: 2,
            name: "Altcoin Gang",
            description: "Ethereum, Solana & mehr",
            members: 87,
            joined: true,
        },
        {
            id: 3,
            name: "Daytraders",
            description: "Strategien & Signale",
            members: 205,
            joined: true,
        },
    ]
})

const joinedCommunities = computed(() =>
    communities.value.filter((c) => c.joined)
)
const otherCommunities = computed(() =>
    communities.value.filter((c) => !c.joined)
)

function joinCommunity(id: number) {
    const c = communities.value.find((c) => c.id === id)
    if (c) c.joined = true
}

function leaveCommunity(id: number) {
    const c = communities.value.find((c) => c.id === id)
    if (c) c.joined = false
}

function goToWatchlist(id: number) {
    console.log("Springe zu Watchlist der Community", id)
}

function viewCommunity(id: number) {
    console.log("Details der Community", id)
}
</script>

<template>
    <Head title="Communitys" />

    <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">
        Community Übersicht
    </h1>

    <!-- Block: Meine Communitys -->
    <section v-if="joinedCommunities.length > 0" class="mb-10">
        <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-200">
            Meine Communitys
        </h2>

        <div class="space-y-4">
            <div
                v-for="c in joinedCommunities"
                :key="c.id"
                class="bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-700
               rounded-lg shadow p-4 flex flex-col md:flex-row md:items-center md:justify-between
               transition-colors duration-300"
            >
                <div>
                    <h3 class="text-lg font-semibold text-blue-700 dark:text-blue-400">
                        {{ c.name }}
                    </h3>
                    <p class="text-gray-600 dark:text-gray-300 text-sm">
                        {{ c.description }}
                    </p>
                    <p class="text-gray-500 dark:text-gray-400 text-xs mt-1">
                        Mitglieder: {{ c.members }}
                    </p>
                </div>

                <div class="flex flex-col md:flex-row gap-2 mt-3 md:mt-0">
                    <button
                        @click="goToWatchlist(c.id)"
                        class="flex items-center gap-1 px-3 py-1 rounded bg-blue-500 hover:bg-blue-600
                   text-white text-sm transition-colors"
                    >
                        <ChartBarIcon class="h-4 w-4" />
                        Zur Watchlist
                    </button>
                    <button
                        @click="viewCommunity(c.id)"
                        class="flex items-center gap-1 px-3 py-1 rounded bg-gray-200 dark:bg-gray-700
                   text-gray-800 dark:text-gray-200 text-sm transition-colors"
                    >
                        <InformationCircleIcon class="h-4 w-4" />
                        Details
                    </button>
                    <button
                        @click="leaveCommunity(c.id)"
                        class="flex items-center gap-1 px-3 py-1 rounded bg-red-500 hover:bg-red-600
                   text-white text-sm transition-colors"
                        title="Community verlassen"
                    >
                        <UserMinusIcon class="h-4 w-4" />
                        Verlassen
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Trennlinie -->
    <hr class="border-gray-300 dark:border-gray-700 mb-10" />

    <!-- Block: Alle Communitys -->
    <section>
        <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-200">
            Alle Communitys
        </h2>

        <div v-if="communities.length === 0" class="text-gray-500 dark:text-gray-400">
            Noch keine Communities vorhanden.
        </div>

        <div v-else class="space-y-4">
            <div
                v-for="c in otherCommunities"
                :key="c.id"
                class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 flex flex-col
               md:flex-row md:items-center md:justify-between transition-colors duration-300"
            >
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ c.name }}
                    </h3>
                    <p class="text-gray-600 dark:text-gray-300 text-sm">
                        {{ c.description }}
                    </p>
                    <p class="text-gray-500 dark:text-gray-400 text-xs mt-1">
                        Mitglieder: {{ c.members }}
                    </p>
                </div>

                <div class="flex flex-col md:flex-row gap-2 mt-3 md:mt-0">
                    <button
                        @click="viewCommunity(c.id)"
                        class="flex items-center gap-1 px-3 py-1 rounded bg-gray-200 dark:bg-gray-700
                   text-gray-800 dark:text-gray-200 text-sm transition-colors"
                    >
                        <InformationCircleIcon class="h-4 w-4" />
                        Details
                    </button>
                    <button
                        v-if="!c.joined"
                        @click="joinCommunity(c.id)"
                        class="flex items-center gap-1 px-3 py-1 rounded bg-green-500 hover:bg-green-600
                   text-white text-sm transition-colors"
                    >
                        <UserPlusIcon class="h-4 w-4" />
                        Beitreten
                    </button>
                </div>
            </div>
        </div>
    </section>
</template>

<script lang="ts">
export default {
    layout: AppLayout,
}
</script>
