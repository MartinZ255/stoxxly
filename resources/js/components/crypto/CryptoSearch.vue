<template>
    <div
        class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg
           transition-colors duration-300"
    >
        <!-- 🔹 Input + Button -->
        <div class="mb-6">
            <label
                for="crypto-search"
                class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1"
            >
                Search Crypto (e.g., BTC, ETH, SOL)
            </label>

            <div class="flex">
                <input
                    v-model="query"
                    type="text"
                    name="crypto-search"
                    id="crypto-search"
                    class="block w-full shadow-sm sm:text-sm p-3 rounded-l-md
                 border border-gray-300 dark:border-gray-600
                 bg-gray-50 dark:bg-gray-700
                 text-gray-900 dark:text-gray-100
                 placeholder-gray-400 dark:placeholder-gray-500
                 focus:ring-indigo-500 focus:border-indigo-500
                 transition-colors duration-300"
                    placeholder="Enter crypto symbol..."
                    @keypress.enter="onSearch"
                />

                <button
                    @click="onSearch"
                    class="inline-flex items-center px-4 py-2 border border-l-0
                 border-indigo-600 rounded-r-md
                 bg-indigo-600 text-white
                 hover:bg-indigo-700 dark:hover:bg-indigo-500
                 focus:outline-none focus:ring-2 focus:ring-offset-2
                 focus:ring-indigo-500
                 transition-colors duration-300"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89
                     3.476l4.817 4.817a1 1 0 01-1.414
                     1.414l-4.816-4.816A6 6 0 012 8z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    <span class="ml-2">Search</span>
                </button>
            </div>

            <div v-if="error" class="text-red-500 dark:text-red-400 text-sm mt-1">
                {{ error }}
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from "vue"

const emit = defineEmits(["search"])

const query = ref("")
const error = ref("")

function onSearch() {
    if (!query.value.trim()) {
        error.value = "Please enter a crypto symbol."
        return
    }
    error.value = ""
    emit("search", query.value.toUpperCase()) // 🔹 Event an Parent
}
</script>
