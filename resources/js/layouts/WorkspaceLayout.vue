<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import JulishMark from '@/components/focusflow/JulishMark.vue';
import MaterialIcon from '@/components/focusflow/MaterialIcon.vue';
import { Toaster } from '@/components/ui/sonner';

defineProps<{ title?: string }>();
const page = usePage();
const isActive = (href: string) => page.url.startsWith(href);
const today = new Date().toLocaleDateString('en-US', {
    weekday: 'long',
    month: 'long',
    day: 'numeric',
    year: 'numeric',
});
const nav = [
    { label: 'Tasks', icon: 'checklist', href: '/dashboard' },
    { label: 'History', icon: 'history', href: '/history' },
    { label: 'Timer Settings', icon: 'timer', href: '/settings/timer' },
];
</script>
<template>
    <div class="flex min-h-screen bg-surface font-sans text-on-surface">
        <aside
            class="flex w-64 shrink-0 flex-col justify-between border-r border-outline-variant/50 bg-surface-container-lowest p-4"
        >
            <div>
                <div class="mb-6 flex items-center gap-2 px-2">
                    <div
                        class="flex size-8 items-center justify-center rounded-lg bg-primary-fx"
                    >
                        <JulishMark class="size-6" />
                    </div>
                    <div>
                        <p
                            class="font-display text-base leading-tight font-bold"
                        >
                            Julish
                        </p>
                        <p
                            class="font-mono text-[10px] tracking-widest text-on-surface-variant uppercase"
                        >
                            Deep Work Session
                        </p>
                    </div>
                </div>
                <Link
                    href="/dashboard?new=1"
                    class="mb-6 flex w-full items-center justify-center gap-2 rounded-lg bg-primary-container-fx px-4 py-2.5 text-sm font-semibold text-on-primary-fx shadow-sm transition hover:opacity-90"
                >
                    <MaterialIcon name="add" /> New Task
                </Link>
                <nav class="space-y-1">
                    <Link
                        v-for="item in nav"
                        :key="item.href"
                        :href="item.href"
                        class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium"
                        :class="
                            isActive(item.href)
                                ? 'bg-secondary-container-fx/40 text-primary-fx'
                                : 'text-on-surface-variant hover:bg-surface-container-low'
                        "
                    >
                        <MaterialIcon
                            :name="item.icon"
                            :fill="isActive(item.href)"
                        />
                        {{ item.label }}
                    </Link>
                </nav>
            </div>
            <div class="space-y-1 border-t border-outline-variant/50 pt-4">
                <a
                    href="#"
                    class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-on-surface-variant hover:bg-surface-container-low"
                    ><MaterialIcon name="help" /> Help</a
                >
                <Link
                    href="/logout"
                    method="post"
                    as="button"
                    class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm text-on-surface-variant hover:bg-surface-container-low"
                    ><MaterialIcon name="logout" /> Logout</Link
                >
            </div>
        </aside>
        <main class="flex-1 overflow-y-auto">
            <header class="px-8 pt-6">
                <h1 class="font-display text-2xl font-bold">{{ today }}</h1>
                <p class="text-sm text-on-surface-variant">
                    Good morning, prepare for deep work.
                </p>
            </header>
            <div class="p-8 pt-6"><slot /></div>
        </main>
        <Toaster />
    </div>
</template>
