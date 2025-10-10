<template>
    <!-- Floating Chat Button -->
    <button
        @click="openSidebar"
        class="fixed bottom-6 right-6 bg-blue-600 hover:bg-blue-700 text-white rounded-full p-4 shadow-lg z-50"
    >
        💬
    </button>

    <!-- Overlay -->
    <div
        v-if="isOpen"
        @click="closeSidebar"
        class="fixed inset-0 bg-black/40 z-40 transition-opacity duration-300"
    ></div>

    <!-- Sidebar -->
    <div
        class="fixed top-0 right-0 h-full w-96 bg-white dark:bg-gray-800 shadow-lg z-50 flex flex-col transform transition-transform duration-300"
        :class="isOpen ? 'translate-x-0' : 'translate-x-full'"
    >
        <!-- Header -->
        <div class="flex items-center p-4 border-b border-gray-200 dark:border-gray-700">
            <button
                v-if="activeChat"
                @click="backToList"
                class="mr-3 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
            >
                <ArrowLeftIcon class="h-5 w-5" />
            </button>
            <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                {{ activeChat ? activeChat.name : "Chats" }}
            </h2>
            <button
                @click="closeSidebar"
                class="ml-auto text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
            >
                ✕
            </button>
        </div>

        <!-- Chat List -->
        <div v-if="!activeChat" class="flex-1 overflow-y-auto">
            <div
                v-for="chat in chats"
                :key="chat.id"
                @click="openChat(chat)"
                class="p-4 border-b border-gray-200 dark:border-gray-700 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
            >
                <div class="flex justify-between items-center">
                    <div>
                        <p class="font-semibold text-gray-900 dark:text-white">
                            {{ chat.name }}
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 truncate w-60">
                            {{ chat.last_message?.message || "Keine Nachrichten" }}
                        </p>
                    </div>
                    <span class="text-xs text-gray-400">
            {{ formatTime(chat.updated_at) }}
          </span>
                </div>
            </div>
        </div>

        <!-- Active Chat -->
        <div v-else class="flex flex-col flex-1">
            <!-- Messages -->
            <div ref="scrollBox" class="flex-1 overflow-y-auto p-4 space-y-4">
                <div
                    v-for="m in messages"
                    :key="m.id"
                    :class="m.user_id === currentUserId ? 'text-right' : 'text-left'"
                >
                    <div
                        v-if="activeChat.type !== 'private'"
                        class="text-xs text-gray-500 dark:text-gray-400 mb-1"
                        :class="m.user_id === currentUserId ? 'text-right' : 'text-left'"
                    >
                        {{ m.user?.name }}
                    </div>

                    <div
                        :class="m.user_id === currentUserId
              ? 'flex flex-col items-end'
              : 'flex flex-col items-start'"
                    >
            <span
                :class="m.user_id === currentUserId
                ? 'inline-block bg-blue-500 text-white px-3 py-2 rounded-lg'
                : 'inline-block bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 rounded-lg'"
            >
              {{ m.message }}
            </span>
                        <span class="text-xs text-gray-400 mt-1">
              {{ formatTime(m.created_at) }}
            </span>
                    </div>
                </div>
            </div>

            <!-- Message Input -->
            <form @submit.prevent="sendMessage" class="p-4 border-t border-gray-200 dark:border-gray-700 flex gap-2">
                <input
                    v-model="newMessage"
                    type="text"
                    placeholder="Nachricht schreiben..."
                    class="flex-1 border rounded px-3 py-2 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                />
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                    ➤
                </button>
            </form>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount, nextTick, computed } from "vue"
import axios from "axios"
import { ArrowLeftIcon } from "@heroicons/vue/24/outline"
import { route } from "ziggy-js"
import { usePage } from '@inertiajs/vue3'

const isOpen = ref(false)
const activeChat = ref<any | null>(null)
const chats = ref<any[]>([])
const messages = ref<any[]>([])
const newMessage = ref("")
const page = usePage()
const currentUserId = computed(() => page.props.auth.user?.id ?? null)

const scrollBox = ref<HTMLDivElement | null>(null)

let pollInterval: number | null = null

function formatTime(t: string | null) {
    if (!t) return ""
    return new Date(t).toLocaleTimeString("de-DE", { hour: "2-digit", minute: "2-digit" })
}

function openSidebar() {
    isOpen.value = true
    loadChats()
}
function closeSidebar() {
    isOpen.value = false
    activeChat.value = null
    messages.value = []
    clearPoll()
}
function backToList() {
    activeChat.value = null
    messages.value = []
    clearPoll()
}

/** Lädt alle Chats des eingeloggten Users */
async function loadChats() {
    try {
        const res = await axios.get(route("chats.index"))
        chats.value = res.data
    } catch (e) {
        console.error("Fehler beim Laden der Chats", e)
    }
}

/** Öffnet einen Chat und startet das Polling */
async function openChat(chat: any) {
    activeChat.value = chat
    await loadMessages()
    startPoll()
}

/** Lädt Nachrichten eines Chats */
async function loadMessages() {
    if (!activeChat.value) return
    try {
        const res = await axios.get(route("chats.show", activeChat.value.id))
        messages.value = res.data.messages || []
        await nextTick()
        scrollToBottom()
    } catch (e) {
        console.error("Fehler beim Laden der Nachrichten", e)
    }
}

/** Nachricht senden */
async function sendMessage() {
    if (!newMessage.value.trim() || !activeChat.value) return

    const messageText = newMessage.value
    newMessage.value = ""

    try {
        const res = await axios.post(route("chats.messages.store", activeChat.value.id), {
            content: messageText,
        })
        messages.value.push(res.data)
        await nextTick()
        scrollToBottom()
    } catch (e) {
        console.error("Fehler beim Senden der Nachricht", e)
    }
}

/** Polling für neue Nachrichten */
function startPoll() {
    clearPoll()
    pollInterval = window.setInterval(loadMessages, 4000)
}
function clearPoll() {
    if (pollInterval) {
        clearInterval(pollInterval)
        pollInterval = null
    }
}

/** Scrollt ans Ende der Nachrichtenliste */
function scrollToBottom() {
    if (scrollBox.value) {
        scrollBox.value.scrollTop = scrollBox.value.scrollHeight
    }
}

onMounted(() => {
    // optional: Chats direkt laden, wenn Sidebar offen
})
onBeforeUnmount(() => clearPoll())
</script>
