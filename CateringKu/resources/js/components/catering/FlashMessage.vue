<script setup lang="ts">
import { ref, onMounted } from 'vue'

const props = defineProps<{
    type?: 'success' | 'error' | 'warning' | 'info'
    message: string
    autoDismiss?: boolean
    duration?: number
}>()

const emit = defineEmits<{
    dismiss: []
}>()

const visible = ref(true)

const iconMap = {
    success: { icon: '✓', bg: 'bg-green-50 dark:bg-green-500/10', border: 'border-green-200 dark:border-green-500/30', text: 'text-green-800 dark:text-green-300', iconBg: 'bg-green-100 text-green-800 dark:bg-green-500/20 dark:text-green-300' },
    error: { icon: '✕', bg: 'bg-red-50 dark:bg-red-500/10', border: 'border-red-200 dark:border-red-500/30', text: 'text-red-800 dark:text-red-300', iconBg: 'bg-red-100 text-red-800 dark:bg-red-500/20 dark:text-red-300' },
    warning: { icon: '!', bg: 'bg-amber-50 dark:bg-amber-500/10', border: 'border-amber-200 dark:border-amber-500/30', text: 'text-amber-800 dark:text-amber-300', iconBg: 'bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-300' },
    info: { icon: 'i', bg: 'bg-blue-50 dark:bg-blue-500/10', border: 'border-blue-200 dark:border-blue-500/30', text: 'text-blue-800 dark:text-blue-300', iconBg: 'bg-blue-100 text-blue-800 dark:bg-blue-500/20 dark:text-blue-300' },
}

const config = iconMap[props.type ?? 'success']

function dismiss() {
    visible.value = false
    emit('dismiss')
}

onMounted(() => {
    if (props.autoDismiss !== false) {
        setTimeout(dismiss, props.duration ?? 5000)
    }
})
</script>

<template>
    <Transition
        enter-active-class="transition-all duration-300 ease-out"
        leave-active-class="transition-all duration-200 ease-in"
        enter-from-class="opacity-0 -translate-y-2"
        leave-to-class="opacity-0 translate-y-2"
    >
        <div
            v-if="visible"
            :role="type === 'error' ? 'alert' : 'status'"
            :class="[
                'p-4 rounded-xl border flex items-center gap-3 shadow-sm',
                config.bg,
                config.border,
                config.text,
            ]"
        >
            <span aria-hidden="true" :class="['w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold shrink-0', config.iconBg]">
                {{ config.icon }}
            </span>
            <span class="text-sm flex-1">{{ message }}</span>
            <button type="button" aria-label="Tutup pesan" class="shrink-0 w-9 h-9 flex items-center justify-center rounded-lg hover:bg-black/5 dark:hover:bg-white/10 transition-colors" @click="dismiss">
                <svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
    </Transition>
</template>
