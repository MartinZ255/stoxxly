<script setup lang="ts">
import { ref } from "vue"
import { router } from "@inertiajs/vue3"

const props = defineProps<{ token: string, email: string }>()

const password = ref("")
const password_confirmation = ref("")

function resetPassword() {
    router.post("/reset-password", {
        token: props.token,
        email: props.email,
        password: password.value,
        password_confirmation: password_confirmation.value,
    })
}
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-100 dark:bg-gray-900">
        <form @submit.prevent="resetPassword" class="bg-white dark:bg-gray-800 p-6 rounded shadow-md w-96 space-y-4">
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">Neues Passwort</h1>
            <input type="password" v-model="password" placeholder="Neues Passwort"
                   class="w-full border rounded px-3 py-2" />
            <input type="password" v-model="password_confirmation" placeholder="Passwort bestätigen"
                   class="w-full border rounded px-3 py-2" />
            <button type="submit"
                    class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 rounded">Zurücksetzen</button>
        </form>
    </div>
</template>
