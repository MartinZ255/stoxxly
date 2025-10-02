<!-- resources/js/Pages/Settings.vue -->
<script setup lang="ts">
import { Head, router } from "@inertiajs/vue3"
import AppLayout from "../App.vue"
import { ref } from "vue"
import "flag-icons/css/flag-icons.min.css";


// 🔹 States
const notificationChannels = ref<string[]>(["email"]) // mehrere möglich
const language = ref("de")

function toggleChannel(channel: string) {
    if (notificationChannels.value.includes(channel)) {
        notificationChannels.value = notificationChannels.value.filter(c => c !== channel)
    } else {
        notificationChannels.value.push(channel)
    }
}

function toggleLanguage() {
    language.value = language.value === "de" ? "en" : "de"
}

function saveSettings() {
    router.post("/settings", {
        notificationChannels: notificationChannels.value,
        language: language.value,
    })
    window.alert("Einstellungen gespeichert ✅")
}

function deleteAccount() {
    if (confirm("Bist du sicher, dass du dein Konto unwiderruflich löschen möchtest?")) {
        router.delete("/account")
    }
}
</script>

<template>
    <Head title="Einstellungen" />

    <div class="space-y-8 max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
            Einstellungen
        </h1>

        <!-- 🔹 Benachrichtigungskanäle -->
        <section class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 space-y-4">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                Benachrichtigungskanäle
            </h2>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Wähle die Kanäle, über die du Alerts erhalten möchtest.
            </p>
            <div class="flex gap-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" value="email" :checked="notificationChannels.includes('email')" @change="toggleChannel('email')" />
                    <span class="text-gray-800 dark:text-gray-200">E-Mail</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" value="sms" :checked="notificationChannels.includes('sms')" @change="toggleChannel('sms')" />
                    <span class="text-gray-800 dark:text-gray-200">SMS</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" value="push" :checked="notificationChannels.includes('push')" @change="toggleChannel('push')" />
                    <span class="text-gray-800 dark:text-gray-200">Push</span>
                </label>
            </div>
        </section>

        <!-- Sprachblock mit Flaggen-Icons (ohne Text) -->
        <section class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 space-y-4">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                Sprache
            </h2>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Wähle deine Bevorzugte Sprache
            </p>
            <div class="flex items-center gap-4">
                <button
                    @click="toggleLanguage"
                    class="relative w-24 h-12 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center transition-colors"
                >
      <span
          class="absolute top-1 left-1 w-10 h-10 rounded-full bg-white dark:bg-gray-900 shadow transition-transform"
          :class="language === 'en' ? 'translate-x-12' : ''"
      ></span>

                    <span class="absolute left-3">🇩🇪</span>
                    <span class="absolute right-3">🇬🇧</span>
                </button>

                <!-- nur Flagge anzeigen -->
                <span class="text-2xl">
                    <span v-if="language === 'de'" class="fi fi-de w-10 h-7 rounded shadow"></span>
                    <span v-else class="fi fi-gb w-10 h-7 rounded shadow"></span>
                </span>
            </div>
        </section>




        <!-- 🔹 Konto löschen -->
        <section class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 space-y-4">
            <h2 class="text-lg font-semibold text-red-600 dark:text-red-400">
                Konto löschen
            </h2>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Dein Konto und alle Daten werden dauerhaft gelöscht. Diese Aktion kann
                nicht rückgängig gemacht werden.
            </p>
            <button
                @click="deleteAccount"
                class="px-4 py-2 rounded bg-red-500 hover:bg-red-600 text-white text-sm"
            >
                Konto löschen
            </button>
        </section>

        <!-- 🔹 Speichern Button -->
        <div class="flex justify-end">
            <button
                @click="saveSettings"
                class="px-4 py-2 rounded bg-blue-500 hover:bg-blue-600 text-white"
            >
                Änderungen speichern
            </button>
        </div>
    </div>
</template>

<script lang="ts">
export default { layout: AppLayout }
</script>
