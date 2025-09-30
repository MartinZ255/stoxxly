<template>
    <nav
        class="bg-white dark:bg-gray-900 shadow-lg sticky top-0 z-40 transition-colors duration-300"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- 🔹 Logo -->
                <div class="flex items-center">
                    <a
                        :href="route('home')"
                        class="flex-shrink-0 text-xl md:text-2xl font-bold text-indigo-600 dark:text-indigo-400"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="w-8 h-8 inline-block mr-2 text-indigo-600 dark:text-indigo-400"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1
                   5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94
                   2.28-2.28 5.941"
                            />
                        </svg>
                        Stoxxly - wir tracken dein Casino-Portfolio
                    </a>
                </div>

                <!-- 🔹 Navigation -->
                <div class="hidden lg:flex items-center space-x-6">
                    <a :href="route('currencies')" class="nav-link text-gray-700 dark:text-gray-200 hover:text-gray-900 dark:hover:text-white">Currencies</a>
                    <a :href="route('communitys')" class="nav-link text-gray-700 dark:text-gray-200 hover:text-gray-900 dark:hover:text-white">Communitys</a>
                    <a :href="route('watchlist')" class="nav-link text-gray-700 dark:text-gray-200 hover:text-gray-900 dark:hover:text-white">Watchlist</a>

                    <!-- 🔹 Auth-Bereich -->
                    <template v-if="$page.props.auth && $page.props.auth.user">
                        <span class="text-gray-700 dark:text-gray-200">
                            Hallo, {{ $page.props.auth.user.name }}
                        </span>
                        <button
                            @click="logout"
                            class="px-3 py-2 rounded-md text-sm font-medium bg-red-500 text-white hover:bg-red-600 dark:bg-red-600 dark:hover:bg-red-700"
                        >
                            Logout
                        </button>
                    </template>
                    <template v-else>
                        <button
                            @click="showAuthModal = true"
                            class="text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700
                     hover:text-gray-900 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium"
                        >
                            Anmelden
                        </button>
                        <AuthModal v-if="showAuthModal" @close="showAuthModal = false" />
                    </template>

                    <!-- 🔹 Theme Toggle -->
                    <button
                        @click="toggleTheme"
                        type="button"
                        class="text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700
                   focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700
                   rounded-lg text-sm p-2.5 transition-colors"
                    >
                        <span v-if="isDark">🌙</span>
                        <span v-else>☀️</span>
                    </button>
                </div>
            </div>
        </div>
    </nav>
</template>


<script setup lang="ts">
import { ref, onMounted } from "vue"
import { route } from "ziggy-js"
import { router } from "@inertiajs/vue3"
import AuthModal from "../components/UserInterface/AuthModal.vue"

const showAuthModal = ref(false)
const isDark = ref(false)

onMounted(() => {
    const storedTheme = localStorage.getItem("color-theme")
    if (storedTheme === "dark") {
        isDark.value = true
        document.documentElement.classList.add("dark")
    } else if (storedTheme === "light") {
        isDark.value = false
        document.documentElement.classList.remove("dark")
    } else {
        if (window.matchMedia("(prefers-color-scheme: dark)").matches) {
            isDark.value = true
            document.documentElement.classList.add("dark")
        } else {
            isDark.value = false
            document.documentElement.classList.remove("dark")
        }
    }
})

function toggleTheme() {
    isDark.value = !isDark.value
    if (isDark.value) {
        document.documentElement.classList.add("dark")
        localStorage.setItem("color-theme", "dark")
    } else {
        document.documentElement.classList.remove("dark")
        localStorage.setItem("color-theme", "light")
    }
}

function logout() {
    router.post(route('logout'), {}, {
        onFinish: () => {
            router.visit(route('home')) // GET /
        },
    })
}


</script>
