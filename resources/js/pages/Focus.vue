<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { onMounted, ref, watch } from 'vue';
import MaterialIcon from '@/components/focusflow/MaterialIcon.vue';
import { useAlerts } from '@/composables/useAlerts';
import { useCountdown } from '@/composables/useCountdown';
import type { TaskItemData } from '@/types/focusflow';

const props = defineProps<{
    task: TaskItemData;
    active_session: {
        id: number;
        started_at: string;
        planned_seconds: number;
    } | null;
    preferences: {
        browser_notifications: boolean;
        zen_mode: boolean;
        focus_tone: string;
        break_tone: string;
    };
}>();

const alerts = useAlerts(props.preferences);
const showWarning = ref(false);
const { display, running, start, pause, resume, remaining } = useCountdown({
    onWarning: () => {
        showWarning.value = true;
    }, // final-minute banner, always
    onFinish: () => {
        if (props.task.notify_on_end) {
            alerts.chime('focus');
            alerts.notify(
                'Session complete',
                `"${props.task.title}" focus block finished.`,
            );
        }

        finish(true, false);
    },
});

// Optional 5-minute heads-up chime, per task toggle
watch(remaining, (v) => {
    if (v === 300 && props.task.five_minute_warning) {
        alerts.chime('break');
        alerts.notify('5 minutes left', `Wrap up "${props.task.title}" soon.`);
    }
});

const elapsed = () =>
    props.active_session
        ? props.active_session.planned_seconds - remaining.value
        : 0;
function finish(completed: boolean, completeTask: boolean) {
    if (!props.active_session) {
        return;
    }

    router.patch(`/sessions/${props.active_session.id}/finish`, {
        elapsed_seconds: elapsed(),
        completed,
        complete_task: completeTask,
    });
}

onMounted(() => {
    if (!props.active_session) {
        router.post(`/tasks/${props.task.id}/sessions`);

        return;
    }

    const already = Math.floor(
        (Date.now() - new Date(props.active_session.started_at).getTime()) /
            1000,
    );
    start(Math.max(0, props.active_session.planned_seconds - already));
});
</script>
<template>
    <div
        class="flex min-h-screen flex-col bg-surface font-sans text-on-surface"
    >
        <div
            v-if="showWarning"
            class="flex items-center gap-2 bg-primary-container-fx px-4 py-2 font-mono text-xs text-on-primary-fx"
        >
            <MaterialIcon name="info" /> 1 minute remaining in current focus
            session. Wrap up your thought.
            <button class="ml-auto" @click="showWarning = false">
                <MaterialIcon name="close" />
            </button>
        </div>
        <div
            class="flex flex-1 flex-col items-center justify-center gap-10 p-6"
        >
            <div
                class="flex size-64 flex-col items-center justify-center rounded-full border-4 border-primary-container-fx bg-surface-container-lowest shadow-xl"
            >
                <span class="font-display text-6xl font-bold tracking-tight">{{
                    display
                }}</span>
                <span
                    class="mt-1 font-mono text-[11px] tracking-widest text-on-surface-variant uppercase"
                    >Remaining</span
                >
            </div>
            <div
                class="w-full max-w-md rounded-2xl bg-surface-container-lowest p-6 text-center shadow-sm"
            >
                <span
                    v-if="task.category"
                    class="rounded bg-secondary-container-fx/60 px-2 py-0.5 font-mono text-[11px] tracking-widest text-on-secondary-container-fx uppercase"
                    >{{ task.category.name }}</span
                >
                <h2 class="mt-2 font-display text-xl font-bold">
                    {{ task.title }}
                </h2>
                <p
                    v-if="task.description"
                    class="mt-1 text-sm text-on-surface-variant"
                >
                    {{ task.description }}
                </p>
            </div>
            <div class="flex items-center gap-3">
                <button
                    class="flex items-center gap-2 rounded-lg border border-outline-variant bg-surface-container-lowest px-5 py-2.5 text-sm font-semibold"
                    @click="running ? pause() : resume()"
                >
                    <MaterialIcon :name="running ? 'pause' : 'play_arrow'" />
                    {{ running ? 'Pause' : 'Resume' }}
                </button>
                <button
                    class="flex items-center gap-2 rounded-lg bg-primary-container-fx px-5 py-2.5 text-sm font-semibold text-on-primary-fx"
                    @click="finish(true, true)"
                >
                    <MaterialIcon name="check_circle" /> Complete
                </button>
            </div>
        </div>
        <div class="p-4 text-right">
            <button
                class="font-mono text-xs text-on-surface-variant hover:text-on-surface"
                @click="finish(false, false)"
            >
                Exit Focus Mode
            </button>
        </div>
    </div>
</template>
