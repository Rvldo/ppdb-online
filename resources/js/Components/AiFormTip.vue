<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    field: { type: String, required: true },
    value: { type: String, default: '' },
    context: { type: String, default: '' },
});

const tip = ref('');
const loading = ref(false);
const visible = ref(false);
let debounceTimer = null;

async function fetchTip() {
    if (loading.value) return;
    loading.value = true;
    visible.value = true;

    try {
        const res = await fetch('/api/v1/chat/form-assist', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
            },
            body: JSON.stringify({
                field: props.field,
                value: props.value,
                context: props.context,
            }),
        });
        const data = await res.json();
        if (data.success && data.data?.tip) {
            tip.value = data.data.tip;
        }
    } catch {
        visible.value = false;
    }
    loading.value = false;
}

function handleFocus() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        if (!tip.value) fetchTip();
        else visible.value = true;
    }, 500);
}

function handleBlur() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => { visible.value = false; }, 300);
}

defineExpose({ handleFocus, handleBlur });
</script>

<template>
    <Transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="opacity-0 translate-y-1"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition duration-150 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div v-if="visible && (tip || loading)" class="mt-1.5 flex items-start gap-1.5">
            <div class="w-4 h-4 rounded bg-gradient-to-br from-violet-500 to-indigo-500 flex items-center justify-center flex-shrink-0 mt-0.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-2.5 h-2.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5m4.75-11.396a24.301 24.301 0 014.5 0" /></svg>
            </div>
            <p v-if="loading" class="text-[11px] text-violet-400 italic">AI sedang berpikir...</p>
            <p v-else class="text-[11px] text-violet-600 leading-relaxed">{{ tip }}</p>
        </div>
    </Transition>
</template>
