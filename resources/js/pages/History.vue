<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import MaterialIcon from '@/components/focusflow/MaterialIcon.vue';
import PriorityChip from '@/components/focusflow/PriorityChip.vue';
import StatCard from '@/components/focusflow/StatCard.vue';
import WorkspaceLayout from '@/layouts/WorkspaceLayout.vue';
import type {
    CategoryData,
    HistoryFilters,
    HistoryRow,
    HistoryStats,
} from '@/types/focusflow';

const props = defineProps<{
    stats: HistoryStats;
    rows: HistoryRow[];
    filters: HistoryFilters;
    categories: CategoryData[];
}>();

const search = ref(props.filters.q ?? '');
const categoryId = ref(
    props.filters.category_id ? String(props.filters.category_id) : '',
);
const days = ref(props.filters.days ? String(props.filters.days) : '');
const priority = ref(props.filters.priority ?? '');

const applyFilters = () => {
    router.get(
        '/history',
        {
            q: search.value || undefined,
            category_id: categoryId.value || undefined,
            days: days.value || undefined,
            priority: priority.value || undefined,
        },
        { preserveState: true, replace: true },
    );
};

let searchTimeout: ReturnType<typeof setTimeout> | undefined;
watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilters, 300);
});
watch([categoryId, days, priority], applyFilters);

const totalFocusLabel = computed(() => {
    const seconds = props.stats.total_focus_seconds;

    return `${Math.floor(seconds / 3600)}h ${Math.floor((seconds % 3600) / 60)}m`;
});

const focusedLabel = (seconds: number) => {
    const h = Math.floor(seconds / 3600);
    const m = Math.floor((seconds % 3600) / 60);

    return h > 0 ? `${h}h ${m}m focused` : `${m}m focused`;
};

const completedLabel = (iso: string | null) => {
    if (!iso) {
        return '';
    }

    return new Date(iso).toLocaleString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
    });
};

const visibleCount = ref(10);
const visibleRows = computed(() => props.rows.slice(0, visibleCount.value));
const hasMore = computed(() => visibleCount.value < props.rows.length);
const loadMore = () => {
    visibleCount.value += 10;
};
</script>
<template>
    <WorkspaceLayout title="Task History">
        <div class="mb-6">
            <h1 class="font-display text-2xl font-bold text-on-surface">
                Task History
            </h1>
            <p class="text-sm text-on-surface-variant">
                Review and analyze your completed deep work sessions.
            </p>
        </div>

        <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-3">
            <StatCard
                label="Completed This Week"
                :value="String(stats.week_count)"
                :sub="`+${stats.week_change_percent}%`"
            />
            <StatCard label="Total Focus Time" :value="totalFocusLabel" />
            <StatCard
                label="Longest Streak"
                :value="String(stats.longest_streak_days)"
                sub="Days"
                highlight
            />
        </div>

        <div
            class="mb-6 flex flex-col items-center gap-4 rounded-xl border border-outline-variant/30 bg-surface-container-lowest p-2 shadow-sm lg:flex-row"
        >
            <div class="flex w-full flex-1 items-center px-2">
                <MaterialIcon
                    name="search"
                    class="mr-2 text-on-surface-variant"
                />
                <input
                    v-model="search"
                    type="text"
                    placeholder="Search history..."
                    class="w-full border-none bg-transparent p-2 text-sm text-on-surface placeholder:text-on-surface-variant focus:ring-0 focus:outline-none"
                />
            </div>
            <div class="hidden h-8 w-px bg-outline-variant/50 lg:block" />
            <div class="flex w-full gap-2 overflow-x-auto px-2 lg:w-auto">
                <select
                    v-model="categoryId"
                    class="cursor-pointer rounded-lg border-none bg-surface-container-low py-2 pr-8 pl-3 text-sm text-on-surface focus:ring-1 focus:ring-primary-fx"
                >
                    <option value="">All Categories</option>
                    <option
                        v-for="category in categories"
                        :key="category.id"
                        :value="category.id"
                    >
                        {{ category.name }}
                    </option>
                </select>
                <select
                    v-model="days"
                    class="cursor-pointer rounded-lg border-none bg-surface-container-low py-2 pr-8 pl-3 text-sm text-on-surface focus:ring-1 focus:ring-primary-fx"
                >
                    <option value="">All Time</option>
                    <option value="7">Last 7 Days</option>
                    <option value="30">Last 30 Days</option>
                    <option value="90">Last 90 Days</option>
                </select>
                <select
                    v-model="priority"
                    class="cursor-pointer rounded-lg border-none bg-surface-container-low py-2 pr-8 pl-3 text-sm text-on-surface focus:ring-1 focus:ring-primary-fx"
                >
                    <option value="">All Priorities</option>
                    <option value="high">High</option>
                    <option value="medium">Medium</option>
                    <option value="low">Low</option>
                </select>
            </div>
        </div>

        <div class="flex flex-col gap-3">
            <div
                v-for="row in visibleRows"
                :key="row.id"
                class="flex flex-col items-start gap-4 rounded-xl border border-outline-variant/40 bg-surface p-4 transition-all hover:border-outline-variant hover:bg-surface-container-lowest sm:flex-row sm:items-center"
            >
                <div
                    class="flex size-6 shrink-0 items-center justify-center rounded-md bg-primary-fx text-on-primary-fx opacity-50"
                >
                    <MaterialIcon name="check" class="text-base" />
                </div>
                <div class="flex min-w-0 flex-1 flex-col gap-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <h3
                            class="truncate font-display text-base text-on-surface line-through opacity-50"
                        >
                            {{ row.title }}
                        </h3>
                        <PriorityChip :priority="row.priority" />
                    </div>
                    <div
                        class="flex flex-wrap items-center gap-x-2 gap-y-1 font-mono text-[11px] text-on-surface-variant"
                    >
                        <span
                            v-if="row.category"
                            class="flex items-center gap-1"
                            ><MaterialIcon name="folder" class="text-sm" />
                            {{ row.category }}</span
                        >
                        <span
                            v-if="row.category"
                            class="hidden text-outline-variant sm:inline"
                            >•</span
                        >
                        <span
                            >Completed on
                            {{ completedLabel(row.completed_at) }}</span
                        >
                    </div>
                </div>
                <div
                    class="flex shrink-0 items-center gap-1 rounded-lg bg-surface-container px-3 py-1.5 font-mono text-[11px] text-on-surface-variant"
                >
                    <MaterialIcon name="timer" class="text-sm" />
                    {{ focusedLabel(row.focused_seconds) }}
                </div>
            </div>

            <p
                v-if="rows.length === 0"
                class="py-12 text-center text-sm text-on-surface-variant"
            >
                No completed tasks match your filters.
            </p>

            <button
                v-if="hasMore"
                type="button"
                class="mt-4 w-full rounded-lg border-2 border-dashed border-outline-variant/50 py-3 text-sm font-semibold text-on-surface-variant transition-colors hover:border-primary-fx hover:text-primary-fx"
                @click="loadMore"
            >
                Load More History
            </button>
        </div>
    </WorkspaceLayout>
</template>
