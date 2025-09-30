<script setup lang="ts">
import { defineProps, defineEmits } from "vue";

const props = defineProps<{
    addDisabled?: boolean;
    alertDisabled?: boolean;
}>();

const emit = defineEmits<{
    (e: "add-to-watchlist"): void;
    (e: "set-price-alert"): void;
}>();
</script>

<template>
    <div class="lg:col-span-1 space-y-4">
        <!-- Add to Watchlist -->
        <button
            id="addToWatchlistBtn"
            class="w-full bg-green-500 hover:bg-green-600 text-white dark:bg-green-600 dark:hover:bg-green-700
             font-semibold py-2 px-4 rounded-md transition-colors duration-300
             disabled:opacity-50 disabled:cursor-not-allowed"
            :disabled="addDisabled"
            @click="emit('add-to-watchlist')"
        >
            Add to Watchlist
        </button>

        <!-- Set Price Alert -->
        <button
            id="setPriceAlertBtn"
            class="w-full bg-blue-500 hover:bg-blue-600 text-white dark:bg-blue-600 dark:hover:bg-blue-700
             font-semibold py-2 px-4 rounded-md transition-colors duration-300
             disabled:opacity-50 disabled:cursor-not-allowed"
            :disabled="alertDisabled"
            @click="emit('set-price-alert')"
        >
            Set Price Alert
        </button>
    </div>
</template>
