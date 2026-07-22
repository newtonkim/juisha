<script setup lang="ts">
import { computed } from 'vue';
import MaterialIcon from '@/components/focusflow/MaterialIcon.vue';
import type { DailyStats } from '@/types/focusflow';

const props = defineProps<{ stats: DailyStats }>();

const progressPercent = computed(() =>
    props.stats.total ? (props.stats.completed / props.stats.total) * 100 : 0,
);
const deepWorkHours = computed(() =>
    (props.stats.deep_work_seconds / 3600).toFixed(1),
);
</script>
<template>
    <div class="flex flex-col gap-4 rounded-xl bg-surface-container-low p-4">
        <h3
            class="flex items-center gap-2 font-display text-lg font-bold text-on-surface"
        >
            <MaterialIcon name="monitoring" class="text-primary-fx" /> Daily
            Progress
        </h3>
        <div class="flex flex-1 flex-col justify-center gap-2">
            <div class="mb-1 flex items-end justify-between">
                <span
                    class="font-display text-4xl leading-none font-bold text-on-surface"
                >
                    {{ stats.completed
                    }}<span class="text-xl font-medium text-on-surface-variant"
                        >/{{ stats.total }}</span
                    >
                </span>
                <span
                    class="font-mono text-xs tracking-widest text-on-surface-variant uppercase"
                    >Tasks Completed</span
                >
            </div>
            <div
                class="h-2 w-full overflow-hidden rounded-full bg-surface-container"
            >
                <div
                    class="h-full rounded-full bg-primary-fx transition-all"
                    :style="{ width: `${progressPercent}%` }"
                />
            </div>
            <div class="mt-3 flex justify-between text-center">
                <div>
                    <div class="font-display text-xl font-bold text-on-surface">
                        {{ deepWorkHours }}h
                    </div>
                    <div
                        class="mt-1 font-mono text-[11px] tracking-widest text-on-surface-variant uppercase"
                    >
                        Deep Work
                    </div>
                </div>
                <div class="w-px bg-outline-variant" />
                <div>
                    <div class="font-display text-xl font-bold text-on-surface">
                        {{ stats.focus_score }}%
                    </div>
                    <div
                        class="mt-1 font-mono text-[11px] tracking-widest text-on-surface-variant uppercase"
                    >
                        Focus Score
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
