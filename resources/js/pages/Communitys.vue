<script setup lang="ts">
import { Head, useForm, router } from "@inertiajs/vue3"
import AppLayout from "../App.vue"
import { ref, computed } from "vue"
import { route } from "ziggy-js"
import {
    ChartBarIcon,
    InformationCircleIcon,
    UserMinusIcon,
    UserPlusIcon,
    PlusCircleIcon,
    XMarkIcon,
} from "@heroicons/vue/24/outline"

interface Community {
    id: number
    name: string
    description: string
    users_count: number
    joined: boolean
}

const props = defineProps<{ communities: Community[] }>()
const showCreateForm = ref(false)
const form = useForm({ name: "", description: "" })

function toggleCreateForm() {
    showCreateForm.value = !showCreateForm.value
}

function createCommunity() {
    form.post(route("communitys.store"), {
        onSuccess: () => {
            form.reset()
            showCreateForm.value = false
        },
    })
}

function joinCommunity(id: number) {
    router.post(route("communitys.join", id), {}, { preserveScroll: true })
}

function leaveCommunity(id: number, members: number) {
    if (members === 1) {
        const confirmed = confirm(
            "⚠️ Du bist das letzte Mitglied dieser Community.\n" +
            "Wenn du sie verlässt, wird sie dauerhaft gelöscht.\n\n" +
            "Möchtest du wirklich fortfahren?"
        )
        if (!confirmed) return
    }
    router.delete(route("communitys.leave", id), { preserveScroll: true })
}

function goToWatchlist(id: number) {
    router.visit(route("watchlist", { community: id }))
}

// 🔹 Communities sortieren
const joinedCommunities = computed(() =>
    props.communities.filter((c) => c.joined)
)
const otherCommunities = computed(() =>
    props.communities.filter((c) => !c.joined)
)
</script>

<template>
    <Head title="Communitys" />
    <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">
        Community Übersicht
    </h1>

    <!-- 🟢 Community erstellen -->
    <div class="mb-6">
        <button
            @click="toggleCreateForm"
            class="flex items-center gap-2 px-4 py-2 rounded bg-green-600 hover:bg-green-700 text-white transition-colors"
        >
            <component :is="showCreateForm ? XMarkIcon : PlusCircleIcon" class="h-5 w-5" />
            <span>
                {{ showCreateForm ? "Abbrechen" : "Neue Community erstellen" }}
            </span>
        </button>
    </div>

    <!-- Formular -->
    <section
        v-if="showCreateForm"
        class="mb-10 bg-gray-50 dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700"
    >
        <h2 class="text-lg font-semibold mb-3 text-gray-800 dark:text-gray-200">
            Neue Community erstellen
        </h2>
        <form @submit.prevent="createCommunity" class="flex flex-col gap-3">
            <input
                v-model="form.name"
                type="text"
                placeholder="Community-Name"
                class="px-3 py-2 rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white"
            />
            <textarea
                v-model="form.description"
                placeholder="Kurzbeschreibung"
                rows="2"
                class="px-3 py-2 rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white"
            ></textarea>
            <button
                type="submit"
                class="self-start flex items-center gap-1 px-4 py-2 rounded bg-green-600 hover:bg-green-700 text-white text-sm transition-colors"
            >
                <PlusCircleIcon class="h-5 w-5" />
                Erstellen
            </button>
        </form>
    </section>

    <!-- 🔷 Meine Communitys -->
    <section v-if="joinedCommunities.length > 0" class="mb-10">
        <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-200">
            Meine Communitys
        </h2>

        <div class="space-y-4">
            <div
                v-for="c in joinedCommunities"
                :key="c.id"
                class="bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-700 rounded-lg shadow p-4 flex flex-col md:flex-row md:items-center md:justify-between transition-colors"
            >
                <div>
                    <h3 class="text-lg font-semibold text-blue-700 dark:text-blue-400">
                        {{ c.name }}
                    </h3>
                    <p class="text-gray-600 dark:text-gray-300 text-sm">{{ c.description }}</p>
                    <p class="text-gray-500 dark:text-gray-400 text-xs mt-1">
                        Mitglieder: {{ c.users_count }}
                    </p>
                </div>

                <div class="flex flex-col md:flex-row gap-2 mt-3 md:mt-0">
                    <button
                        @click="goToWatchlist(c.id)"
                        class="flex items-center gap-1 px-3 py-1 rounded bg-blue-500 hover:bg-blue-600 text-white text-sm"
                    >
                        <ChartBarIcon class="h-4 w-4" />
                        Zur Watchlist
                    </button>
                    <button
                        @click="() => router.visit(`/communitys/${c.id}`)"
                        class="flex items-center gap-1 px-3 py-1 rounded bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-sm"
                    >
                        <InformationCircleIcon class="h-4 w-4" />
                        Details
                    </button>
                    <button
                        @click="leaveCommunity(c.id, c.users_count)"
                        class="flex items-center gap-1 px-3 py-1 rounded bg-red-500 hover:bg-red-600 text-white text-sm"
                    >
                        <UserMinusIcon class="h-4 w-4" />
                        Verlassen
                    </button>
                </div>
            </div>
        </div>
    </section>

    <hr class="border-gray-300 dark:border-gray-700 mb-10" />

    <!-- 🔹 Alle anderen Communitys -->
    <section>
        <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-200">
            Alle Communitys
        </h2>

        <div v-if="otherCommunities.length === 0" class="text-gray-500 dark:text-gray-400">
            Keine weiteren Communitys vorhanden.
        </div>

        <div v-else class="space-y-4">
            <div
                v-for="c in otherCommunities"
                :key="c.id"
                class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 flex flex-col md:flex-row md:items-center md:justify-between transition-colors"
            >
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ c.name }}</h3>
                    <p class="text-gray-600 dark:text-gray-300 text-sm">{{ c.description }}</p>
                    <p class="text-gray-500 dark:text-gray-400 text-xs mt-1">
                        Mitglieder: {{ c.users_count }}
                    </p>
                </div>

                <div class="flex flex-col md:flex-row gap-2 mt-3 md:mt-0">
                    <button
                        @click="joinCommunity(c.id)"
                        class="flex items-center gap-1 px-3 py-1 rounded bg-green-500 hover:bg-green-600 text-white text-sm"
                    >
                        <UserPlusIcon class="h-4 w-4" />
                        Beitreten
                    </button>
                    <button
                        @click="() => router.visit(`/communitys/${c.id}`)"
                        class="flex items-center gap-1 px-3 py-1 rounded bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-sm"
                    >
                        <InformationCircleIcon class="h-4 w-4" />
                        Details
                    </button>
                </div>
            </div>
        </div>
    </section>
</template>

<script lang="ts">
export default { layout: AppLayout }
</script>
