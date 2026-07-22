<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import JulishMark from '@/components/focusflow/JulishMark.vue';
import { home } from '@/routes';

defineProps<{
    title?: string;
    description?: string;
}>();

const proof = [
    ['25m', 'Default session'],
    ['1', 'Task at a time'],
    ['0', 'Mid-flow pings'],
];
</script>

<template>
    <div
        class="grid min-h-svh bg-surface font-sans text-on-surface antialiased lg:grid-cols-[1.05fr_1fr]"
    >
        <!-- ======= BRAND PANEL (dark forest, desktop only) ======= -->
        <div
            class="relative isolate hidden flex-col overflow-hidden bg-primary-fx p-10 text-on-primary-fx lg:flex xl:p-14"
        >
            <!-- atmosphere: mint radial glow + faint grid -->
            <div
                aria-hidden="true"
                class="pointer-events-none absolute inset-0 opacity-60"
                style="
                    background:
                        radial-gradient(
                            44rem 28rem at 82% 12%,
                            rgba(160, 244, 200, 0.14),
                            transparent 62%
                        ),
                        radial-gradient(
                            34rem 24rem at 4% 96%,
                            rgba(134, 175, 153, 0.12),
                            transparent 60%
                        );
                "
            />
            <div
                aria-hidden="true"
                class="pointer-events-none absolute inset-0 opacity-[0.05]"
                style="
                    background-image:
                        linear-gradient(
                            rgba(160, 244, 200, 0.5) 1px,
                            transparent 1px
                        ),
                        linear-gradient(
                            90deg,
                            rgba(160, 244, 200, 0.5) 1px,
                            transparent 1px
                        );
                    background-size: 56px 56px;
                "
            />

            <!-- logo -->
            <Link
                :href="home()"
                class="ff-reveal relative z-10 flex w-fit items-center gap-2.5"
                style="--d: 0ms"
            >
                <span
                    class="flex size-9 items-center justify-center rounded-xl bg-secondary-container-fx"
                >
                    <JulishMark class="size-7" fill="#012d1d" check="#a0f4c8" />
                </span>
                <span class="font-display text-lg font-extrabold tracking-tight"
                    >Julish</span
                >
            </Link>

            <!-- editorial statement -->
            <div class="relative z-10 my-auto py-14">
                <p
                    class="ff-reveal inline-flex items-center gap-2 rounded-full border border-white/15 px-3.5 py-1.5 font-mono text-[11px] tracking-[0.22em] text-on-primary-container-fx uppercase"
                    style="--d: 90ms"
                >
                    <span
                        class="size-1.5 rounded-full bg-secondary-container-fx"
                    />
                    Your focus, measured
                </p>
                <h2
                    class="ff-reveal mt-6 font-display text-4xl leading-[1.06] font-extrabold tracking-tight text-balance xl:text-5xl"
                    style="--d: 170ms"
                >
                    Deep work,<br />
                    <span class="text-secondary-container-fx">on purpose.</span>
                </h2>
                <p
                    class="ff-reveal mt-5 max-w-sm text-base leading-relaxed text-on-primary-container-fx"
                    style="--d: 250ms"
                >
                    One account, one task, one timer. Progress becomes something
                    you
                    <em class="font-medium text-on-primary-fx not-italic"
                        >measure</em
                    >, not something you hope for.
                </p>

                <!-- mini session card: echoes the real focus mode -->
                <div
                    class="ff-reveal mt-10 flex w-fit items-center gap-5 rounded-2xl border border-white/10 bg-primary-container-fx/40 py-4 pr-6 pl-4 shadow-lg shadow-black/25 backdrop-blur"
                    style="--d: 330ms"
                >
                    <span class="relative flex size-14 items-center">
                        <span
                            class="ff-ring-spin absolute -inset-1 rounded-full"
                            aria-hidden="true"
                        />
                        <span
                            class="relative flex size-14 items-center justify-center rounded-full border-2 border-primary-container-fx bg-primary-fx/60"
                        >
                            <JulishMark
                                class="size-8"
                                fill="#a0f4c8"
                                check="#012d1d"
                            />
                        </span>
                    </span>
                    <span>
                        <span
                            class="block font-mono text-[10px] tracking-[0.24em] text-on-primary-container-fx uppercase"
                            >Next session</span
                        >
                        <span
                            class="ff-tick block font-display text-3xl font-extrabold tracking-tight tabular-nums"
                            >25:00</span
                        >
                    </span>
                </div>
            </div>

            <!-- proof strip -->
            <div
                class="relative z-10 grid grid-cols-3 gap-6 border-t border-white/10 pt-7"
            >
                <div
                    v-for="(s, i) in proof"
                    :key="s[1]"
                    class="ff-reveal"
                    :style="`--d: ${420 + i * 70}ms`"
                >
                    <p
                        class="font-display text-2xl font-extrabold text-secondary-container-fx"
                    >
                        {{ s[0] }}
                    </p>
                    <p
                        class="mt-1 font-mono text-[10px] tracking-[0.2em] text-on-primary-container-fx uppercase"
                    >
                        {{ s[1] }}
                    </p>
                </div>
            </div>
        </div>

        <!-- ======= FORM PANEL (light) ======= -->
        <div class="relative flex flex-col">
            <!-- mobile brand bar -->
            <header
                class="flex items-center justify-center border-b border-outline-variant/50 py-5 lg:hidden"
            >
                <Link :href="home()" class="flex items-center gap-2.5">
                    <span
                        class="flex size-8 items-center justify-center rounded-lg bg-primary-fx"
                    >
                        <JulishMark
                            class="size-6"
                            fill="#a0f4c8"
                            check="#012d1d"
                        />
                    </span>
                    <span
                        class="font-display text-base font-extrabold tracking-tight"
                        >Julish</span
                    >
                </Link>
            </header>

            <div
                class="flex flex-1 items-center justify-center px-6 py-12 sm:px-10 lg:py-16"
            >
                <div class="w-full max-w-sm">
                    <div class="ff-reveal mb-9" style="--d: 80ms">
                        <p
                            class="font-mono text-[11px] tracking-[0.24em] text-secondary-fx uppercase"
                        >
                            Julish
                        </p>
                        <h1
                            class="mt-2.5 font-display text-3xl font-extrabold tracking-tight text-balance"
                            v-if="title"
                        >
                            {{ title }}
                        </h1>
                        <p
                            class="mt-2.5 leading-relaxed text-on-surface-variant"
                            v-if="description"
                        >
                            {{ description }}
                        </p>
                    </div>
                    <div class="ff-reveal" style="--d: 160ms">
                        <slot />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* staggered entrance */
.ff-reveal {
    animation: ff-rise 0.7s cubic-bezier(0.22, 1, 0.36, 1) both;
    animation-delay: var(--d, 0ms);
}
@keyframes ff-rise {
    from {
        opacity: 0;
        transform: translateY(18px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* slow conic sweep around the mini timer ring */
.ff-ring-spin {
    background: conic-gradient(
        from 0deg,
        rgba(160, 244, 200, 0.85),
        rgba(160, 244, 200, 0.05) 55%,
        transparent 70%
    );
    -webkit-mask: radial-gradient(
        farthest-side,
        transparent calc(100% - 4px),
        #000 calc(100% - 3px)
    );
    mask: radial-gradient(
        farthest-side,
        transparent calc(100% - 4px),
        #000 calc(100% - 3px)
    );
    animation: ff-spin 14s linear infinite;
}
@keyframes ff-spin {
    to {
        transform: rotate(1turn);
    }
}

/* soft pulse on the time readout */
.ff-tick {
    animation: ff-pulse 2.4s ease-in-out infinite;
}
@keyframes ff-pulse {
    0%,
    100% {
        opacity: 1;
    }
    50% {
        opacity: 0.82;
    }
}

@media (prefers-reduced-motion: reduce) {
    .ff-reveal,
    .ff-ring-spin,
    .ff-tick {
        animation: none;
    }
}
</style>
