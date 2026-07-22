<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { computed } from 'vue';
import MaterialIcon from '@/components/focusflow/MaterialIcon.vue';

const props = defineProps<{ minutes: number; taskId: number | null }>();

const display = computed(() => `${String(props.minutes).padStart(2, '0')}:00`);

const startTimer = () => {
    router.visit(props.taskId ? `/focus/${props.taskId}` : '/dashboard?new=1');
};
</script>
<template>
    <div
        class="relative flex flex-col items-center justify-center overflow-hidden rounded-xl bg-surface-container-low p-8"
    >
        <span
            class="mb-2 font-mono text-xs font-bold tracking-[0.1em] text-primary-fx uppercase"
            >Current Session: Deep Focus</span
        >
        <div
            class="mb-6 font-display text-7xl leading-none font-bold tracking-tighter text-on-surface tabular-nums md:text-8xl"
        >
            {{ display }}
        </div>
        <button
            type="button"
            class="flex items-center gap-2 rounded-full bg-primary-fx px-8 py-3 font-display text-lg font-semibold text-on-primary-fx shadow-sm transition hover:opacity-90 active:scale-95"
            @click="startTimer"
        >
            <MaterialIcon name="play_arrow" fill /> Start Timer
        </button>
    </div>
</template>
