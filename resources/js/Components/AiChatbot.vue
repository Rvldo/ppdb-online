<script setup>
import { ref, nextTick, computed } from 'vue';

const isOpen = ref(false);
const message = ref('');
const messages = ref([]);
const loading = ref(false);
const chatBody = ref(null);
const hasInteracted = ref(false);

const welcomeMessage = {
    role: 'assistant',
    content: 'Halo! Saya **PPDB AI Assistant** yang didukung oleh Claude. Saya bisa membantu kamu tentang:\n\n- Informasi jurusan & kuota\n- Syarat dan prosedur pendaftaran\n- Jadwal dan periode PPDB\n- Cek status pendaftaran\n\nSilakan bertanya!',
};

const quickQuestions = [
    { icon: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', text: 'Apa saja jurusan yang tersedia?' },
    { icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01', text: 'Apa syarat pendaftarannya?' },
    { icon: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', text: 'Kapan batas pendaftaran?' },
    { icon: 'M13 7l5 5m0 0l-5 5m5-5H6', text: 'Bagaimana cara mendaftar?' },
];

// Simple markdown-ish rendering
function renderText(text) {
    return text
        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
        .replace(/\*(.*?)\*/g, '<em>$1</em>')
        .replace(/^- (.+)/gm, '<span class="flex gap-1.5 items-start"><span class="text-violet-400 mt-0.5">&#8226;</span><span>$1</span></span>')
        .replace(/^(\d+)\. (.+)/gm, '<span class="flex gap-1.5 items-start"><span class="text-violet-400 font-semibold">$1.</span><span>$2</span></span>')
        .replace(/\n/g, '<br/>');
}

// Build history for API (last 10 messages)
function buildHistory() {
    return messages.value
        .filter(m => m.role !== 'system')
        .slice(-10)
        .map(m => ({ role: m.role, content: m.content }));
}

function scrollToBottom() {
    nextTick(() => {
        if (chatBody.value) {
            chatBody.value.scrollTo({ top: chatBody.value.scrollHeight, behavior: 'smooth' });
        }
    });
}

async function send(text) {
    const msg = text || message.value.trim();
    if (!msg || loading.value) return;

    hasInteracted.value = true;
    messages.value.push({ role: 'user', content: msg });
    message.value = '';
    loading.value = true;
    scrollToBottom();

    const history = buildHistory().slice(0, -1); // exclude the one we just pushed

    try {
        const res = await fetch('/api/v1/chat', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
            },
            body: JSON.stringify({ message: msg, history }),
        });

        const data = await res.json();

        if (data.success) {
            messages.value.push({ role: 'assistant', content: data.data.reply });
        } else {
            messages.value.push({ role: 'assistant', content: data.message || 'Maaf, terjadi kesalahan. Silakan coba lagi.' });
        }
    } catch {
        messages.value.push({ role: 'assistant', content: 'Maaf, tidak dapat terhubung ke server. Coba lagi nanti.' });
    }

    loading.value = false;
    scrollToBottom();
}

function toggle() {
    isOpen.value = !isOpen.value;
    if (isOpen.value) {
        nextTick(() => scrollToBottom());
    }
}

function clearChat() {
    messages.value = [];
    hasInteracted.value = false;
}
</script>

<template>
    <!-- Floating Button -->
    <button
        @click="toggle"
        class="fixed bottom-6 right-6 z-50 group"
    >
        <div
            class="relative w-14 h-14 rounded-2xl shadow-2xl flex items-center justify-center transition-all duration-500"
            :class="isOpen
                ? 'bg-slate-800 rotate-0 rounded-xl'
                : 'bg-gradient-to-br from-violet-600 via-purple-600 to-indigo-600 hover:scale-110 hover:shadow-violet-500/50'"
        >
            <!-- Morphing icon -->
            <transition name="icon-morph" mode="out-in">
                <svg v-if="!isOpen" key="chat" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
                <svg v-else key="close" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </transition>
        </div>
        <!-- Ping -->
        <span v-if="!isOpen && !hasInteracted" class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-rose-500 rounded-full border-2 border-white animate-pulse"></span>
        <!-- Glow -->
        <div v-if="!isOpen" class="absolute inset-0 w-14 h-14 rounded-2xl bg-violet-500/30 blur-xl animate-pulse-slow -z-10"></div>
    </button>

    <!-- Chat Window -->
    <Transition
        enter-active-class="transition duration-400 ease-[cubic-bezier(0.34,1.56,0.64,1)]"
        enter-from-class="opacity-0 translate-y-8 scale-90"
        enter-to-class="opacity-100 translate-y-0 scale-100"
        leave-active-class="transition duration-250 ease-in"
        leave-from-class="opacity-100 translate-y-0 scale-100"
        leave-to-class="opacity-0 translate-y-8 scale-90"
    >
        <div
            v-if="isOpen"
            class="fixed bottom-24 right-6 z-50 w-[400px] max-w-[calc(100vw-2rem)] rounded-3xl shadow-2xl shadow-slate-900/20 border border-white/80 bg-white overflow-hidden flex flex-col backdrop-blur-sm"
            style="height: 560px; max-height: calc(100vh - 8rem);"
        >
            <!-- Header -->
            <div class="relative bg-gradient-to-r from-violet-600 via-purple-600 to-indigo-600 px-5 py-4 flex items-center gap-3 flex-shrink-0 overflow-hidden">
                <!-- Subtle pattern -->
                <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 16px 16px;"></div>
                <div class="relative w-10 h-10 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center ring-2 ring-white/20">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5" />
                    </svg>
                </div>
                <div class="relative flex-1">
                    <div class="text-white font-bold text-sm">PPDB AI Assistant</div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span class="text-violet-200 text-[11px]">Powered by Claude &middot; Anthropic</span>
                    </div>
                </div>
                <button v-if="hasInteracted" @click="clearChat" class="relative text-white/60 hover:text-white transition-colors p-1" title="Mulai ulang">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                </button>
            </div>

            <!-- Messages -->
            <div ref="chatBody" class="flex-1 overflow-y-auto p-4 space-y-4 scroll-smooth" style="background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);">
                <!-- Welcome card (only when no messages) -->
                <div v-if="!hasInteracted" class="space-y-4">
                    <div class="flex justify-start">
                        <div class="max-w-[90%] px-4 py-3 rounded-2xl rounded-bl-lg bg-white text-slate-700 shadow-sm border border-slate-100 text-sm leading-relaxed" v-html="renderText(welcomeMessage.content)"></div>
                    </div>

                    <!-- Quick actions grid -->
                    <div class="grid grid-cols-2 gap-2">
                        <button
                            v-for="q in quickQuestions"
                            :key="q.text"
                            @click="send(q.text)"
                            class="flex items-start gap-2 text-left text-xs p-3 rounded-xl bg-white border border-slate-200/80 text-slate-600 hover:border-violet-300 hover:bg-violet-50 hover:text-violet-700 transition-all duration-200 group"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-300 group-hover:text-violet-400 flex-shrink-0 mt-0.5 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" :d="q.icon" /></svg>
                            <span>{{ q.text }}</span>
                        </button>
                    </div>
                </div>

                <!-- Chat messages -->
                <template v-for="(msg, i) in messages" :key="i">
                    <div class="flex" :class="msg.role === 'user' ? 'justify-end' : 'justify-start'">
                        <!-- AI avatar -->
                        <div v-if="msg.role === 'assistant'" class="w-6 h-6 rounded-lg bg-gradient-to-br from-violet-500 to-indigo-500 flex items-center justify-center flex-shrink-0 mr-2 mt-1 shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5m4.75-11.396a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3" /></svg>
                        </div>
                        <div
                            class="max-w-[80%] px-4 py-2.5 text-sm leading-relaxed"
                            :class="msg.role === 'user'
                                ? 'bg-gradient-to-br from-violet-600 to-indigo-600 text-white rounded-2xl rounded-br-lg shadow-md shadow-violet-500/10'
                                : 'bg-white text-slate-700 rounded-2xl rounded-bl-lg shadow-sm border border-slate-100'"
                            v-html="msg.role === 'assistant' ? renderText(msg.content) : msg.content"
                        ></div>
                    </div>
                </template>

                <!-- Typing indicator -->
                <div v-if="loading" class="flex justify-start">
                    <div class="w-6 h-6 rounded-lg bg-gradient-to-br from-violet-500 to-indigo-500 flex items-center justify-center flex-shrink-0 mr-2 mt-1 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5m4.75-11.396a24.301 24.301 0 014.5 0" /></svg>
                    </div>
                    <div class="bg-white px-4 py-3 rounded-2xl rounded-bl-lg shadow-sm border border-slate-100">
                        <div class="flex items-center gap-1">
                            <span class="w-2 h-2 bg-violet-400 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
                            <span class="w-2 h-2 bg-violet-400 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                            <span class="w-2 h-2 bg-violet-400 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Input -->
            <div class="border-t border-slate-100 px-4 py-3 bg-white flex-shrink-0">
                <form @submit.prevent="send()" class="flex gap-2 items-end">
                    <div class="flex-1 relative">
                        <input
                            v-model="message"
                            type="text"
                            placeholder="Ketik pertanyaanmu..."
                            class="w-full text-sm border border-slate-200 rounded-xl px-4 py-3 pr-3 focus:outline-none focus:ring-2 focus:ring-violet-500/30 focus:border-violet-400 transition-all bg-slate-50 focus:bg-white"
                            :disabled="loading"
                            maxlength="1000"
                            @keydown.enter.prevent="send()"
                        />
                    </div>
                    <button
                        type="submit"
                        :disabled="!message.trim() || loading"
                        class="w-11 h-11 rounded-xl bg-gradient-to-br from-violet-600 to-indigo-600 text-white flex items-center justify-center hover:from-violet-700 hover:to-indigo-700 disabled:opacity-30 disabled:cursor-not-allowed transition-all duration-200 shadow-lg shadow-violet-500/20 hover:shadow-violet-500/40 active:scale-95 flex-shrink-0"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                        </svg>
                    </button>
                </form>
                <div class="text-center mt-2">
                    <span class="text-[10px] text-slate-300">AI dapat membuat kesalahan. Verifikasi informasi penting.</span>
                </div>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
.icon-morph-enter-active,
.icon-morph-leave-active {
    transition: all 0.2s ease;
}
.icon-morph-enter-from { opacity: 0; transform: scale(0.5) rotate(-90deg); }
.icon-morph-leave-to { opacity: 0; transform: scale(0.5) rotate(90deg); }
</style>
