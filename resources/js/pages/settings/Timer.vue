<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import MaterialIcon from '@/components/focusflow/MaterialIcon.vue';
import WorkspaceLayout from '@/layouts/WorkspaceLayout.vue';
import type { TimerPreferences } from '@/types/focusflow';

const props = defineProps<{ preferences: TimerPreferences }>();

const form = useForm({
    focus_minutes: props.preferences.focus_minutes,
    short_break_minutes: props.preferences.short_break_minutes,
    long_break_minutes: props.preferences.long_break_minutes,
    focus_tone: props.preferences.focus_tone,
    break_tone: props.preferences.break_tone,
    browser_notifications: props.preferences.browser_notifications,
    zen_mode: props.preferences.zen_mode,
});

const focusTones = [
    { value: 'soft-bell', label: 'Soft Bell (Default)' },
    { value: 'deep-gong', label: 'Deep Gong' },
    { value: 'digital-ping', label: 'Digital Ping' },
];
const breakTones = [
    { value: 'upbeat-chime', label: 'Upbeat Chime (Default)' },
    { value: 'gentle-marimba', label: 'Gentle Marimba' },
    { value: 'forest-birds', label: 'Forest Birds' },
];

const save = () => {
    form.patch('/settings/timer', { preserveScroll: true });
};
</script>
<template>
    <WorkspaceLayout title="Timer Settings">
        <div class="mb-6">
            <h1 class="font-display text-2xl font-bold text-on-surface">
                Settings &amp; Preferences
            </h1>
            <p class="text-sm text-on-surface-variant">
                Fine-tune your environment for maximum productivity.
            </p>
        </div>

        <form
            class="grid grid-cols-1 gap-6 lg:grid-cols-12"
            @submit.prevent="save"
        >
            <div class="flex flex-col gap-6 lg:col-span-7">
                <section
                    class="rounded-xl border border-outline-variant/50 bg-surface-container-lowest p-6 shadow-sm"
                >
                    <div class="mb-6 flex items-center gap-2">
                        <MaterialIcon
                            name="hourglass_top"
                            class="text-xl text-primary-fx"
                        />
                        <h3
                            class="font-display text-lg font-bold text-on-surface"
                        >
                            Session Durations
                        </h3>
                    </div>
                    <div class="flex flex-col gap-6">
                        <div class="flex flex-col gap-2">
                            <div class="flex items-end justify-between">
                                <div>
                                    <label
                                        for="focus-range"
                                        class="block font-display text-base text-on-surface"
                                        >Focus Block</label
                                    >
                                    <span
                                        class="block text-sm text-on-surface-variant"
                                        >Deep work interval</span
                                    >
                                </div>
                                <span
                                    class="font-display text-3xl font-bold text-primary-fx"
                                    >{{ form.focus_minutes
                                    }}<span
                                        class="text-lg font-normal text-on-surface-variant"
                                        >m</span
                                    ></span
                                >
                            </div>
                            <div class="flex w-full items-center gap-3">
                                <span class="font-mono text-[11px] text-outline"
                                    >15</span
                                >
                                <input
                                    id="focus-range"
                                    v-model.number="form.focus_minutes"
                                    type="range"
                                    min="15"
                                    max="90"
                                    class="w-full flex-1 accent-primary-fx"
                                />
                                <span class="font-mono text-[11px] text-outline"
                                    >90</span
                                >
                            </div>
                            <p
                                v-if="form.errors.focus_minutes"
                                class="text-sm text-error"
                            >
                                {{ form.errors.focus_minutes }}
                            </p>
                        </div>
                        <hr class="border-outline-variant/50" />
                        <div class="flex flex-col gap-2">
                            <div class="flex items-end justify-between">
                                <div>
                                    <label
                                        for="short-break-range"
                                        class="block font-display text-base text-on-surface"
                                        >Short Break</label
                                    >
                                    <span
                                        class="block text-sm text-on-surface-variant"
                                        >Brief pause to recharge</span
                                    >
                                </div>
                                <span
                                    class="font-display text-3xl font-bold text-primary-fx"
                                    >{{ form.short_break_minutes
                                    }}<span
                                        class="text-lg font-normal text-on-surface-variant"
                                        >m</span
                                    ></span
                                >
                            </div>
                            <div class="flex w-full items-center gap-3">
                                <span class="font-mono text-[11px] text-outline"
                                    >1</span
                                >
                                <input
                                    id="short-break-range"
                                    v-model.number="form.short_break_minutes"
                                    type="range"
                                    min="1"
                                    max="15"
                                    class="w-full flex-1 accent-primary-fx"
                                />
                                <span class="font-mono text-[11px] text-outline"
                                    >15</span
                                >
                            </div>
                            <p
                                v-if="form.errors.short_break_minutes"
                                class="text-sm text-error"
                            >
                                {{ form.errors.short_break_minutes }}
                            </p>
                        </div>
                        <hr class="border-outline-variant/50" />
                        <div class="flex flex-col gap-2">
                            <div class="flex items-end justify-between">
                                <div>
                                    <label
                                        for="long-break-range"
                                        class="block font-display text-base text-on-surface"
                                        >Long Break</label
                                    >
                                    <span
                                        class="block text-sm text-on-surface-variant"
                                        >After completing a cycle</span
                                    >
                                </div>
                                <span
                                    class="font-display text-3xl font-bold text-primary-fx"
                                    >{{ form.long_break_minutes
                                    }}<span
                                        class="text-lg font-normal text-on-surface-variant"
                                        >m</span
                                    ></span
                                >
                            </div>
                            <div class="flex w-full items-center gap-3">
                                <span class="font-mono text-[11px] text-outline"
                                    >10</span
                                >
                                <input
                                    id="long-break-range"
                                    v-model.number="form.long_break_minutes"
                                    type="range"
                                    min="10"
                                    max="45"
                                    class="w-full flex-1 accent-primary-fx"
                                />
                                <span class="font-mono text-[11px] text-outline"
                                    >45</span
                                >
                            </div>
                            <p
                                v-if="form.errors.long_break_minutes"
                                class="text-sm text-error"
                            >
                                {{ form.errors.long_break_minutes }}
                            </p>
                        </div>
                    </div>
                </section>
            </div>

            <div class="flex flex-col gap-6 lg:col-span-5">
                <section
                    class="rounded-xl border border-outline-variant/50 bg-surface-container-lowest p-6 shadow-sm"
                >
                    <div class="mb-6 flex items-center gap-2">
                        <MaterialIcon
                            name="volume_up"
                            class="text-xl text-primary-fx"
                        />
                        <h3
                            class="font-display text-lg font-bold text-on-surface"
                        >
                            Sound Design
                        </h3>
                    </div>
                    <div class="flex flex-col gap-4">
                        <div class="flex flex-col gap-1">
                            <label
                                for="focus-tone"
                                class="font-mono text-[11px] tracking-widest text-on-surface-variant uppercase"
                                >Focus Complete Tone</label
                            >
                            <select
                                id="focus-tone"
                                v-model="form.focus_tone"
                                class="w-full cursor-pointer rounded-lg border border-outline-variant bg-surface-container-low px-3 py-2 text-sm text-on-surface focus:border-primary-fx focus:ring-1 focus:ring-primary-fx focus:outline-none"
                            >
                                <option
                                    v-for="tone in focusTones"
                                    :key="tone.value"
                                    :value="tone.value"
                                >
                                    {{ tone.label }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.focus_tone"
                                class="text-sm text-error"
                            >
                                {{ form.errors.focus_tone }}
                            </p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <label
                                for="break-tone"
                                class="font-mono text-[11px] tracking-widest text-on-surface-variant uppercase"
                                >Break Complete Tone</label
                            >
                            <select
                                id="break-tone"
                                v-model="form.break_tone"
                                class="w-full cursor-pointer rounded-lg border border-outline-variant bg-surface-container-low px-3 py-2 text-sm text-on-surface focus:border-primary-fx focus:ring-1 focus:ring-primary-fx focus:outline-none"
                            >
                                <option
                                    v-for="tone in breakTones"
                                    :key="tone.value"
                                    :value="tone.value"
                                >
                                    {{ tone.label }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.break_tone"
                                class="text-sm text-error"
                            >
                                {{ form.errors.break_tone }}
                            </p>
                        </div>
                    </div>
                </section>

                <section
                    class="rounded-xl border border-outline-variant/50 bg-surface-container-lowest p-6 shadow-sm"
                >
                    <div class="mb-6 flex items-center gap-2">
                        <MaterialIcon
                            name="notifications"
                            class="text-xl text-primary-fx"
                        />
                        <h3
                            class="font-display text-lg font-bold text-on-surface"
                        >
                            Permissions
                        </h3>
                    </div>
                    <div class="flex flex-col gap-4">
                        <label
                            class="flex cursor-pointer items-center justify-between py-1"
                        >
                            <span>
                                <span
                                    class="block font-display text-base text-on-surface"
                                    >Browser Notifications</span
                                >
                                <span
                                    class="block text-sm text-on-surface-variant"
                                    >Alerts when app is in background</span
                                >
                            </span>
                            <span
                                class="relative inline-flex h-6 w-11 shrink-0 items-center rounded-full bg-surface-container-high transition-colors"
                                :class="{
                                    'bg-primary-fx': form.browser_notifications,
                                }"
                            >
                                <input
                                    v-model="form.browser_notifications"
                                    type="checkbox"
                                    class="peer sr-only"
                                />
                                <span
                                    class="inline-block size-5 translate-x-0.5 rounded-full bg-white shadow transition-transform"
                                    :class="{
                                        'translate-x-5':
                                            form.browser_notifications,
                                    }"
                                />
                            </span>
                        </label>
                        <label
                            class="flex cursor-pointer items-center justify-between py-1"
                        >
                            <span>
                                <span
                                    class="block font-display text-base text-on-surface"
                                    >Zen Mode</span
                                >
                                <span
                                    class="block text-sm text-on-surface-variant"
                                    >Mute all sounds during focus blocks</span
                                >
                            </span>
                            <span
                                class="relative inline-flex h-6 w-11 shrink-0 items-center rounded-full bg-surface-container-high transition-colors"
                                :class="{ 'bg-primary-fx': form.zen_mode }"
                            >
                                <input
                                    v-model="form.zen_mode"
                                    type="checkbox"
                                    class="peer sr-only"
                                />
                                <span
                                    class="inline-block size-5 translate-x-0.5 rounded-full bg-white shadow transition-transform"
                                    :class="{ 'translate-x-5': form.zen_mode }"
                                />
                            </span>
                        </label>
                    </div>
                </section>
            </div>

            <div
                class="flex items-center justify-end gap-3 pt-2 lg:col-span-12"
            >
                <button
                    type="button"
                    class="rounded-lg bg-surface-container-low px-6 py-2.5 font-display text-sm font-semibold text-on-surface-variant transition-colors hover:bg-surface-container"
                    @click="form.reset()"
                >
                    Discard Changes
                </button>
                <button
                    type="submit"
                    class="rounded-lg bg-primary-fx px-6 py-2.5 font-display text-sm font-semibold text-on-primary-fx shadow-sm transition hover:opacity-90 disabled:opacity-50"
                    :disabled="form.processing"
                >
                    Save Preferences
                </button>
            </div>
        </form>
    </WorkspaceLayout>
</template>
