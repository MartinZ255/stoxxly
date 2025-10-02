<script setup lang="ts">
import { Head } from "@inertiajs/vue3"
import Navbar from "./layouts/Navbar.vue"
import Footer from "./layouts/Footer.vue"
import ChatSidebar from "./layouts/ChatSidebar.vue"

function onOpenLogin() {
    console.log("Login Modal öffnen (kommt später)")
}
</script>

<template>
    <div
        class="min-h-screen bg-gray-100 dark:bg-gray-900
           text-gray-900 dark:text-gray-100
           transition-colors duration-300"
    >
        <Head title="Stoxxly" />

        <Navbar @open-login="onOpenLogin" />

        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <slot /> <!-- Hier rendert Inertia die aktuelle Seite -->
        </main>

        <ChatSidebar /> <!-- 🔹 Sidebar-Chat immer verfügbar -->
    </div>
    <Footer />
</template>
