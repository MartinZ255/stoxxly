<script setup lang="ts">
import { ref, computed } from "vue"
import { router } from "@inertiajs/vue3"

const activeTab = ref<"login" | "register">("login")
const email = ref("")
const password = ref("")
const password_confirmation = ref("")
const name = ref("")

function login() {
    router.post("/login", {
        email: email.value,
        password: password.value,
    })
}

function register() {
    router.post("/register", {
        name: name.value,
        email: email.value,
        password: password.value,
        password_confirmation: password_confirmation.value,
    })
}

// ✅ Regeln für das Passwort
const passwordRules = computed(() => ({
    length: password.value.length >= 8,
    number: /\d/.test(password.value),
    uppercase: /[A-Z]/.test(password.value),
    special: /[^A-Za-z0-9]/.test(password.value),
}))
</script>

<template>
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div
            class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-md p-6 relative transition-colors"
        >
            <button
                @click="$emit('close')"
                class="absolute top-3 right-3 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200"
            >
                ✕
            </button>

            <!-- Tabs -->
            <div class="flex mb-4 border-b border-gray-300 dark:border-gray-600">
                <button
                    class="flex-1 py-2 text-gray-700 dark:text-gray-200"
                    :class="activeTab==='login' ? 'border-b-2 border-blue-500' : ''"
                    @click="activeTab='login'"
                >
                    Login
                </button>
                <button
                    class="flex-1 py-2 text-gray-700 dark:text-gray-200"
                    :class="activeTab==='register' ? 'border-b-2 border-blue-500' : ''"
                    @click="activeTab='register'"
                >
                    Registrieren
                </button>
            </div>

            <!-- Login-Form -->
            <form v-if="activeTab==='login'" class="space-y-4" @submit.prevent="login">
                <input
                    v-model="email"
                    type="email"
                    placeholder="E-Mail"
                    class="w-full border rounded px-3 py-2
                 bg-gray-50 dark:bg-gray-700
                 text-gray-900 dark:text-gray-100
                 placeholder-gray-400 dark:placeholder-gray-500
                 border-gray-300 dark:border-gray-600
                 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                />
                <input
                    v-model="password"
                    type="password"
                    placeholder="Passwort"
                    class="w-full border rounded px-3 py-2
                 bg-gray-50 dark:bg-gray-700
                 text-gray-900 dark:text-gray-100
                 placeholder-gray-400 dark:placeholder-gray-500
                 border-gray-300 dark:border-gray-600
                 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                />
                <button
                    type="submit"
                    class="w-full bg-blue-500 hover:bg-blue-600
                 text-white py-2 rounded transition-colors"
                >
                    Login
                </button>
            </form>

            <!-- Register-Form -->
            <form v-else class="space-y-4" @submit.prevent="register">
                <input
                    v-model="name"
                    type="text"
                    placeholder="Name"
                    class="w-full border rounded px-3 py-2
                 bg-gray-50 dark:bg-gray-700
                 text-gray-900 dark:text-gray-100
                 placeholder-gray-400 dark:placeholder-gray-500
                 border-gray-300 dark:border-gray-600
                 focus:ring-2 focus:ring-green-500 focus:border-green-500"
                />
                <input
                    v-model="email"
                    type="email"
                    placeholder="E-Mail"
                    class="w-full border rounded px-3 py-2
                 bg-gray-50 dark:bg-gray-700
                 text-gray-900 dark:text-gray-100
                 placeholder-gray-400 dark:placeholder-gray-500
                 border-gray-300 dark:border-gray-600
                 focus:ring-2 focus:ring-green-500 focus:border-green-500"
                />
                <input
                    v-model="password"
                    type="password"
                    placeholder="Passwort"
                    class="w-full border rounded px-3 py-2
                 bg-gray-50 dark:bg-gray-700
                 text-gray-900 dark:text-gray-100
                 placeholder-gray-400 dark:placeholder-gray-500
                 border-gray-300 dark:border-gray-600
                 focus:ring-2 focus:ring-green-500 focus:border-green-500"
                />
                <input
                    v-model="password_confirmation"
                    type="password"
                    placeholder="Passwort bestätigen"
                    class="w-full border rounded px-3 py-2
                 bg-gray-50 dark:bg-gray-700
                 text-gray-900 dark:text-gray-100
                 placeholder-gray-400 dark:placeholder-gray-500
                 border-gray-300 dark:border-gray-600
                 focus:ring-2 focus:ring-green-500 focus:border-green-500"
                />
                <button
                    type="submit"
                    class="w-full bg-green-500 hover:bg-green-600
                 text-white py-2 rounded transition-colors"
                >
                    Registrieren
                </button>
                <!-- 🔹 Visuelle Anzeige -->
                <ul class="text-sm space-y-1">
                    <li v-if="!passwordRules.length" class="text-red-500">
                        ❌ Mindestens 8 Zeichen
                    </li>
                    <li v-if="!passwordRules.number" class="text-red-500">
                        ❌ Mindestens eine Zahl
                    </li>
                    <li v-if="!passwordRules.uppercase" class="text-red-500">
                        ❌ Mindestens ein Großbuchstabe
                    </li>
                    <li v-if="!passwordRules.special" class="text-red-500">
                        ❌ Mindestens ein Sonderzeichen
                    </li>
                </ul>
            </form>
        </div>
    </div>
</template>
