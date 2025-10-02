<template>
    <!-- Button zum Öffnen -->
    <button
        @click="isOpen = true"
        class="fixed bottom-6 right-6 bg-blue-600 hover:bg-blue-700 text-white rounded-full p-4 shadow-lg z-50"
    >
        💬
    </button>

    <!-- Overlay -->
    <div
        v-if="isOpen"
        @click="closeChat"
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
                @click="activeChat = null"
                class="mr-3 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
            >
                <ArrowLeftIcon class="h-5 w-5" />
            </button>
            <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                {{ activeChat ? activeChat.name : "Chats" }}
            </h2>
            <button
                @click="closeChat"
                class="ml-auto text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
            >
                ✕
            </button>
        </div>

        <!-- Chat-Liste -->
        <div v-if="!activeChat" class="flex-1 overflow-y-auto">
            <div
                v-for="chat in chatList"
                :key="chat.id"
                @click="openChat(chat)"
                class="p-4 border-b border-gray-200 dark:border-gray-700 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
            >
                <div class="flex justify-between items-center">
                    <div>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ chat.name }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 truncate w-60">
                            {{ chat.lastMessage }}
                        </p>
                    </div>
                    <span class="text-xs text-gray-400">{{ chat.updatedAt }}</span>
                </div>
            </div>
        </div>

        <!-- Aktiver Chat -->
        <div v-else class="flex flex-col flex-1">
            <!-- Nachrichten -->
            <div class="flex-1 overflow-y-auto p-4 space-y-4">
                <div
                    v-for="(msg, idx) in activeChat.messages"
                    :key="idx"
                    :class="msg.from === 'me' ? 'text-right' : 'text-left'"
                >
                    <!-- Absender in Public & Community -->
                    <div
                        v-if="activeChat.type !== 'private'"
                        class="text-xs text-gray-500 dark:text-gray-400 mb-1"
                        :class="msg.from === 'me' ? 'text-right' : 'text-left'"
                    >
                        {{ msg.user }}
                    </div>

                    <!-- Bubble -->
                    <div
                        :class="msg.from === 'me' ? 'flex flex-col items-end' : 'flex flex-col items-start'"
                    >
            <span
                :class="msg.from === 'me'
                ? 'inline-block bg-blue-500 text-white px-3 py-2 rounded-lg'
                : 'inline-block bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 rounded-lg'"
            >
              {{ msg.text }}
            </span>
                        <span class="text-xs text-gray-400 mt-1">{{ msg.time }}</span>
                    </div>
                </div>
            </div>

            <!-- Eingabe -->
            <form
                @submit.prevent="sendMessage"
                class="p-4 border-t border-gray-200 dark:border-gray-700 flex gap-2"
            >
                <input
                    v-model="newMessage"
                    type="text"
                    placeholder="Nachricht schreiben..."
                    class="flex-1 border rounded px-3 py-2 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                />
                <button
                    type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded"
                >
                    ➤
                </button>
            </form>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from "vue"
import { ArrowLeftIcon } from "@heroicons/vue/24/outline"

const isOpen = ref(false)
const newMessage = ref("")
const activeChat = ref<any | null>(null)

function nowTime() {
    return new Date().toLocaleTimeString("de-DE", { hour: "2-digit", minute: "2-digit" })
}

const chatList = ref([
    {
        id: 1,
        type: "public",
        name: "Öffentlicher Chat",
        lastMessage: "Willkommen 👋",
        updatedAt: "10:30",
        messages: [
            { from: "bot", user: "System", text: "Willkommen im öffentlichen Chat 👋", time: "10:30" },
            { from: "me", user: "Ich", text: "Hallo zusammen!", time: "10:32" },
        ],
    },
    {
        id: 2,
        type: "private",
        name: "Alice",
        lastMessage: "Bis später!",
        updatedAt: "Gestern",
        messages: [
            { from: "me", text: "Hi Alice", time: "Gestern 14:05" },
            { from: "bot", text: "Bis später!", time: "Gestern 14:07" },
        ],
    },
    {
        id: 3,
        type: "community",
        name: "BTC Lovers (Community)",
        lastMessage: "Moon 🚀",
        updatedAt: "08:15",
        messages: [
            { from: "bot", user: "Admin", text: "Herzlich willkommen in BTC Lovers!", time: "08:15" },
            { from: "me", user: "Ich", text: "Moon 🚀", time: "08:16" },
        ],
    },
])

function openChat(chat: any) {
    activeChat.value = chat
}

function closeChat() {
    isOpen.value = false
    activeChat.value = null
}

function sendMessage() {
    if (!newMessage.value.trim() || !activeChat.value) return

    const msg = {
        from: "me",
        user: "Ich",
        text: newMessage.value,
        time: nowTime(),
    }

    activeChat.value.messages.push(msg)
    activeChat.value.lastMessage = newMessage.value
    activeChat.value.updatedAt = "Jetzt"
    newMessage.value = ""

    setTimeout(() => {
        const reply = {
            from: "bot",
            user: activeChat.value.type === "private" ? activeChat.value.name : "System",
            text: "Antwort 🤖",
            time: nowTime(),
        }
        activeChat.value?.messages.push(reply)
        activeChat.value.lastMessage = reply.text
        activeChat.value.updatedAt = "Jetzt"
    }, 1000)
}
</script>
