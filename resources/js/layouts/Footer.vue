<template>
    <footer
        class="transition-colors duration-300
           bg-gray-100 dark:bg-gray-900
           text-gray-800 dark:text-gray-200"
    >
        <div
            class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 text-center space-y-2"
        >
            <p>© {{ year }} {{ brand }}. All rights reserved.</p>

            <p v-if="disclaimer" class="text-sm text-gray-600 dark:text-gray-400">
                {{ disclaimer }}
            </p>

            <slot />
        </div>
    </footer>
</template>

<script setup lang="ts">
import { computed } from "vue"

const props = defineProps({
    brand: { type: String, default: "Stoxxly" },
    disclaimer: {
        type: String,
        default: "Data provided for informational purposes only.",
    },
    links: {
        type: Array,
        default: () => [],
    },
})

const year = computed(() => new Date().getFullYear())
</script>
