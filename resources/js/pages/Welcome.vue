<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import JulishMark from '@/components/focusflow/JulishMark.vue';
import MaterialIcon from '@/components/focusflow/MaterialIcon.vue';
import { dashboard, login, register } from '@/routes';

const features = [
    {
        icon: 'timer',
        title: 'Focus sessions',
        body: 'A fullscreen countdown built for single-tasking. One task, one timer, zero clutter — with gentle audio cues when time is up.',
    },
    {
        icon: 'monitoring',
        title: 'Daily progress',
        body: 'Watch completed tasks, deep-work minutes, and your focus score accumulate through the day. Momentum you can see.',
    },
    {
        icon: 'local_fire_department',
        title: 'Streaks & history',
        body: 'Every finished session is logged. Filter your history, total your focus hours, and keep the streak alive.',
    },
];

const steps = [
    {
        n: '01',
        title: 'Capture the task',
        body: 'Add what matters today — priority, category, and how long you want to focus on it.',
    },
    {
        n: '02',
        title: 'Enter focus mode',
        body: 'Start the timer and the interface falls away. Just you, the clock, and the work.',
    },
    {
        n: '03',
        title: 'Bank the win',
        body: 'Complete the session, strike the task through, and watch your daily score climb.',
    },
];

const proof = [
    ['25m', 'Default session'],
    ['1', 'Task at a time'],
    ['0', 'Notifications mid-flow'],
    ['∞', 'Sessions per day'],
];
</script>

<template>
    <Head title="Julish — Deep work, on purpose" />

    <div class="min-h-screen bg-surface font-sans text-on-surface antialiased">
        <!-- ======= HERO (dark forest) ======= -->
        <div
            class="relative isolate overflow-hidden bg-primary-fx text-on-primary-fx"
        >
            <!-- atmosphere: mint radial glow + faint grid -->
            <div
                aria-hidden="true"
                class="pointer-events-none absolute inset-0 opacity-60"
                style="
                    background:
                        radial-gradient(
                            52rem 30rem at 78% 18%,
                            rgba(160, 244, 200, 0.14),
                            transparent 62%
                        ),
                        radial-gradient(
                            38rem 26rem at 8% 92%,
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

            <!-- nav -->
            <header
                class="relative z-10 mx-auto flex max-w-6xl items-center justify-between px-6 py-6 lg:px-8"
            >
                <div
                    class="ff-reveal flex items-center gap-2.5"
                    style="--d: 0ms"
                >
                    <span
                        class="flex size-9 items-center justify-center rounded-xl bg-secondary-container-fx"
                    >
                        <JulishMark
                            class="size-7"
                            fill="#012d1d"
                            check="#a0f4c8"
                        />
                    </span>
                    <span
                        class="font-display text-lg font-extrabold tracking-tight"
                        >Julish</span
                    >
                </div>
                <nav
                    class="ff-reveal flex items-center gap-2"
                    style="--d: 60ms"
                >
                    <Link
                        v-if="$page.props.auth.user"
                        :href="dashboard()"
                        class="rounded-full bg-secondary-container-fx px-5 py-2 font-display text-sm font-bold text-primary-fx transition hover:opacity-90 active:scale-95"
                    >
                        Open dashboard
                    </Link>
                    <template v-else>
                        <Link
                            :href="login()"
                            class="rounded-full px-4 py-2 text-sm font-medium text-on-primary-container-fx transition hover:bg-white/10 hover:text-on-primary-fx"
                        >
                            Log in
                        </Link>
                        <Link
                            :href="register()"
                            class="rounded-full bg-secondary-container-fx px-5 py-2 font-display text-sm font-bold text-primary-fx transition hover:opacity-90 active:scale-95"
                        >
                            Get started
                        </Link>
                    </template>
                </nav>
            </header>

            <!-- hero body -->
            <div
                class="relative z-10 mx-auto grid max-w-6xl items-center gap-14 px-6 pt-10 pb-20 lg:grid-cols-[1.15fr_1fr] lg:gap-8 lg:px-8 lg:pt-16 lg:pb-28"
            >
                <div>
                    <p
                        class="ff-reveal mb-5 inline-flex items-center gap-2 rounded-full border border-white/15 px-3.5 py-1.5 font-mono text-[11px] tracking-[0.22em] text-on-primary-container-fx uppercase"
                        style="--d: 120ms"
                    >
                        <span
                            class="size-1.5 rounded-full bg-secondary-container-fx"
                        />
                        Real-time task tracking
                    </p>
                    <h1
                        class="ff-reveal font-display text-5xl leading-[1.04] font-extrabold tracking-tight text-balance sm:text-6xl lg:text-7xl"
                        style="--d: 200ms"
                    >
                        Deep work,<br />
                        <span class="text-secondary-container-fx"
                            >on purpose.</span
                        >
                    </h1>
                    <p
                        class="ff-reveal mt-6 max-w-md text-lg leading-relaxed text-on-primary-container-fx"
                        style="--d: 300ms"
                    >
                        Julish turns your to-do list into timed focus sessions —
                        so progress is something you
                        <em class="font-medium text-on-primary-fx not-italic"
                            >measure</em
                        >, not something you hope for.
                    </p>
                    <div
                        class="ff-reveal mt-9 flex flex-wrap items-center gap-4"
                        style="--d: 400ms"
                    >
                        <Link
                            :href="
                                $page.props.auth.user ? dashboard() : register()
                            "
                            class="group inline-flex items-center gap-2 rounded-full bg-secondary-container-fx px-8 py-3.5 font-display text-base font-bold text-primary-fx shadow-lg shadow-black/25 transition hover:opacity-90 active:scale-95"
                        >
                            Start focusing — it's free
                            <MaterialIcon
                                name="arrow_forward"
                                class="transition-transform group-hover:translate-x-0.5"
                            />
                        </Link>
                        <span
                            class="font-mono text-[11px] tracking-[0.18em] text-on-primary-container-fx uppercase"
                        >
                            No card · No setup
                        </span>
                    </div>
                </div>

                <!-- timer ring visual (echoes the real focus mode) -->
                <div
                    class="ff-reveal relative mx-auto lg:mx-0 lg:justify-self-end"
                    style="--d: 350ms"
                >
                    <div
                        class="ff-ring-spin absolute -inset-5 rounded-full"
                        aria-hidden="true"
                    />
                    <div
                        class="relative flex size-64 flex-col items-center justify-center rounded-full border-4 border-primary-container-fx bg-primary-container-fx/40 shadow-2xl shadow-black/40 backdrop-blur sm:size-72"
                    >
                        <span
                            class="font-mono text-[11px] tracking-[0.3em] text-on-primary-container-fx uppercase"
                            >Remaining</span
                        >
                        <span
                            class="ff-tick mt-1 font-display text-6xl font-extrabold tracking-tight tabular-nums sm:text-7xl"
                            >25:00</span
                        >
                        <span
                            class="mt-3 inline-flex items-center gap-1.5 rounded-full bg-secondary-container-fx/15 px-3 py-1 font-mono text-[10px] tracking-[0.2em] text-secondary-container-fx uppercase"
                        >
                            <MaterialIcon name="bolt" class="!text-sm" fill />
                            Deep work
                        </span>
                    </div>
                    <!-- floating chips -->
                    <div
                        class="ff-float absolute -top-3 -left-6 hidden rounded-xl border border-white/10 bg-primary-container-fx/80 px-3.5 py-2 shadow-lg backdrop-blur sm:block"
                        style="--fd: 0s"
                    >
                        <p
                            class="font-mono text-[10px] tracking-[0.18em] text-on-primary-container-fx uppercase"
                        >
                            Focus score
                        </p>
                        <p
                            class="font-display text-xl font-extrabold text-secondary-container-fx"
                        >
                            92%
                        </p>
                    </div>
                    <div
                        class="ff-float absolute -right-4 -bottom-4 hidden rounded-xl border border-white/10 bg-primary-container-fx/80 px-3.5 py-2 shadow-lg backdrop-blur sm:block"
                        style="--fd: 1.6s"
                    >
                        <p
                            class="font-mono text-[10px] tracking-[0.18em] text-on-primary-container-fx uppercase"
                        >
                            Streak
                        </p>
                        <p
                            class="font-display text-xl font-extrabold text-secondary-container-fx"
                        >
                            12 days
                        </p>
                    </div>
                </div>
            </div>

            <!-- proof strip -->
            <div class="relative z-10 border-t border-white/10">
                <div
                    class="mx-auto grid max-w-6xl grid-cols-2 gap-6 px-6 py-8 sm:grid-cols-4 lg:px-8"
                >
                    <div
                        v-for="(s, i) in proof"
                        :key="s[1]"
                        class="ff-reveal"
                        :style="`--d: ${480 + i * 70}ms`"
                    >
                        <p
                            class="font-display text-3xl font-extrabold text-secondary-container-fx"
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
        </div>

        <!-- ======= FEATURES (light) ======= -->
        <section class="mx-auto max-w-6xl px-6 py-20 lg:px-8 lg:py-28">
            <p
                class="font-mono text-[11px] tracking-[0.24em] text-secondary-fx uppercase"
            >
                Why Julish
            </p>
            <h2
                class="mt-3 max-w-xl font-display text-3xl font-extrabold tracking-tight text-balance sm:text-4xl"
            >
                Everything you need to protect your attention. Nothing you
                don't.
            </h2>
            <div class="mt-12 grid gap-5 md:grid-cols-3">
                <article
                    v-for="f in features"
                    :key="f.title"
                    class="group rounded-2xl border border-outline-variant/60 bg-surface-container-lowest p-7 transition duration-300 hover:-translate-y-1 hover:border-secondary-fx/40 hover:shadow-xl hover:shadow-primary-fx/5"
                >
                    <span
                        class="inline-flex size-12 items-center justify-center rounded-xl bg-secondary-container-fx/60 text-primary-container-fx transition group-hover:bg-secondary-container-fx"
                    >
                        <MaterialIcon :name="f.icon" />
                    </span>
                    <h3
                        class="mt-5 font-display text-xl font-bold tracking-tight"
                    >
                        {{ f.title }}
                    </h3>
                    <p class="mt-2.5 leading-relaxed text-on-surface-variant">
                        {{ f.body }}
                    </p>
                </article>
            </div>
        </section>

        <!-- ======= HOW IT WORKS ======= -->
        <section
            class="border-y border-outline-variant/50 bg-surface-container-low"
        >
            <div class="mx-auto max-w-6xl px-6 py-20 lg:px-8 lg:py-24">
                <p
                    class="font-mono text-[11px] tracking-[0.24em] text-secondary-fx uppercase"
                >
                    How it works
                </p>
                <h2
                    class="mt-3 font-display text-3xl font-extrabold tracking-tight sm:text-4xl"
                >
                    Three steps to a focused day
                </h2>
                <ol class="mt-12 grid gap-10 md:grid-cols-3 md:gap-6">
                    <li v-for="s in steps" :key="s.n" class="relative">
                        <span
                            class="font-mono text-sm font-medium tracking-[0.2em] text-outline"
                            >{{ s.n }}</span
                        >
                        <div
                            class="mt-3 h-px w-full bg-outline-variant"
                            aria-hidden="true"
                        />
                        <h3
                            class="mt-4 font-display text-lg font-bold tracking-tight"
                        >
                            {{ s.title }}
                        </h3>
                        <p class="mt-2 leading-relaxed text-on-surface-variant">
                            {{ s.body }}
                        </p>
                    </li>
                </ol>
            </div>
        </section>

        <!-- ======= FINAL CTA ======= -->
        <section
            class="relative isolate overflow-hidden bg-primary-fx text-on-primary-fx"
        >
            <div
                aria-hidden="true"
                class="pointer-events-none absolute inset-0 opacity-50"
                style="
                    background: radial-gradient(
                        46rem 24rem at 50% 120%,
                        rgba(160, 244, 200, 0.16),
                        transparent 65%
                    );
                "
            />
            <div
                class="relative z-10 mx-auto max-w-3xl px-6 py-20 text-center lg:py-28"
            >
                <h2
                    class="font-display text-4xl font-extrabold tracking-tight text-balance sm:text-5xl"
                >
                    Your next deep-work session starts in
                    <span class="text-secondary-container-fx tabular-nums"
                        >00:30</span
                    >
                </h2>
                <p
                    class="mx-auto mt-5 max-w-md text-lg text-on-primary-container-fx"
                >
                    Create a free account, add one task, and press start. That's
                    the whole onboarding.
                </p>
                <Link
                    :href="$page.props.auth.user ? dashboard() : register()"
                    class="group mt-9 inline-flex items-center gap-2 rounded-full bg-secondary-container-fx px-9 py-4 font-display text-base font-bold text-primary-fx shadow-lg shadow-black/25 transition hover:opacity-90 active:scale-95"
                >
                    Create your account
                    <MaterialIcon
                        name="arrow_forward"
                        class="transition-transform group-hover:translate-x-0.5"
                    />
                </Link>
            </div>
        </section>

        <!-- ======= FOOTER ======= -->
        <footer class="bg-primary-fx text-on-primary-container-fx">
            <div
                class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-4 border-t border-white/10 px-6 py-7 lg:px-8"
            >
                <div class="flex items-center gap-2">
                    <JulishMark class="size-5" fill="#a0f4c8" check="#012d1d" />
                    <span
                        class="font-display text-sm font-bold text-on-primary-fx"
                        >Julish</span
                    >
                </div>
                <p class="font-mono text-[10px] tracking-[0.2em] uppercase">
                    Built for deep work · {{ new Date().getFullYear() }}
                </p>
            </div>
        </footer>
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

/* slow conic sweep around the timer ring */
.ff-ring-spin {
    background: conic-gradient(
        from 0deg,
        rgba(160, 244, 200, 0.85),
        rgba(160, 244, 200, 0.05) 55%,
        transparent 70%
    );
    -webkit-mask: radial-gradient(
        farthest-side,
        transparent calc(100% - 6px),
        #000 calc(100% - 5px)
    );
    mask: radial-gradient(
        farthest-side,
        transparent calc(100% - 6px),
        #000 calc(100% - 5px)
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

/* floating stat chips */
.ff-float {
    animation: ff-bob 5.5s ease-in-out infinite;
    animation-delay: var(--fd, 0s);
}
@keyframes ff-bob {
    0%,
    100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-8px);
    }
}

@media (prefers-reduced-motion: reduce) {
    .ff-reveal,
    .ff-ring-spin,
    .ff-tick,
    .ff-float {
        animation: none;
    }
}
</style>
