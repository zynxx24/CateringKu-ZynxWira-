<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{
    modelValue?: number
    rating?: number
    readonly?: boolean
    size?: 'sm' | 'md' | 'lg'
    showValue?: boolean
}>()

const emit = defineEmits<{
    'update:modelValue': [value: number]
}>()

const displayRating = computed(() => props.modelValue ?? props.rating ?? 0)
const stars = [1, 2, 3, 4, 5]

const sizeClasses = computed(() => {
    switch (props.size) {
        case 'sm': return 'w-4 h-4'
        case 'lg': return 'w-7 h-7'
        default: return 'w-5 h-5'
    }
})

function setRating(value: number) {
    if (!props.readonly) {
        emit('update:modelValue', value)
    }
}
</script>

<template>
    <!-- Read-only: one image with a spoken value. Editable: a radio-like group of named buttons. -->
    <div
        class="flex items-center gap-1"
        :role="readonly ? 'img' : 'radiogroup'"
        :aria-label="readonly ? `Rating ${Number(displayRating).toFixed(1)} dari 5` : 'Pilih rating'"
    >
        <template v-for="star in stars" :key="star">
            <svg
                v-if="readonly"
                :class="[sizeClasses, star <= displayRating ? 'text-amber-500 fill-current' : 'text-gray-300 dark:text-gray-600 fill-current']"
                viewBox="0 0 20 20"
                aria-hidden="true"
            >
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
            </svg>
            <button
                v-else
                type="button"
                role="radio"
                :aria-checked="star === displayRating"
                :aria-label="`${star} bintang`"
                class="p-1 rounded-md transition-transform duration-150 hover:scale-110"
                @click="setRating(star)"
            >
                <svg
                    :class="[sizeClasses, star <= displayRating ? 'text-amber-500 fill-current' : 'text-gray-400 dark:text-gray-500 fill-current']"
                    viewBox="0 0 20 20"
                    aria-hidden="true"
                >
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
            </button>
        </template>
        <span v-if="showValue" class="text-sm font-medium text-[var(--ck-text-secondary)] ml-1" aria-hidden="true">
            {{ Number(displayRating).toFixed(1) }}
        </span>
    </div>
</template>
