<script setup lang="ts">
import { router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import MaterialIcon from '@/components/focusflow/MaterialIcon.vue';
import type { CategoryData } from '@/types/focusflow';

const props = defineProps<{ open: boolean; categories: CategoryData[] }>();
const emit = defineEmits<{ close: [] }>();

const form = useForm({
    title: '',
    description: '',
    due_at: '',
    priority: 'medium' as 'high' | 'medium' | 'low',
    session_minutes: 25,
    notify_on_end: true,
    five_minute_warning: false,
    category_id: null as number | null,
});

const selectedCategoryValue = ref('');
const showNewCategory = ref(false);
const newCategoryName = ref('');
const creatingCategory = ref(false);

const onCategoryChange = (event: Event) => {
    const value = (event.target as HTMLSelectElement).value;
    selectedCategoryValue.value = value;

    if (value === '__new__') {
        showNewCategory.value = true;
        form.category_id = null;

        return;
    }

    showNewCategory.value = false;
    form.category_id = value ? Number(value) : null;
};

const createCategory = () => {
    const name = newCategoryName.value.trim();

    if (!name) {
        return;
    }

    creatingCategory.value = true;
    router.post(
        '/categories',
        { name },
        {
            preserveScroll: true,
            preserveState: true,
            only: ['categories'],
            onSuccess: () => {
                const created = props.categories.find((c) => c.name === name);

                if (created) {
                    form.category_id = created.id;
                    selectedCategoryValue.value = String(created.id);
                }

                newCategoryName.value = '';
                showNewCategory.value = false;
            },
            onFinish: () => {
                creatingCategory.value = false;
            },
        },
    );
};

const submit = () => {
    form.post('/tasks', {
        onSuccess: () => {
            form.reset();
            selectedCategoryValue.value = '';
            showNewCategory.value = false;
            emit('close');
        },
    });
};

const close = () => {
    form.reset();
    form.clearErrors();
    selectedCategoryValue.value = '';
    showNewCategory.value = false;
    emit('close');
};
</script>
<template>
    <div
        v-if="open"
        class="fixed inset-0 z-[100] flex items-center justify-center bg-black/30 p-4 backdrop-blur-sm"
        @click.self="close"
    >
        <div
            class="flex max-h-[90vh] w-full max-w-lg flex-col overflow-hidden rounded-2xl bg-surface-container-lowest p-6 shadow-xl"
        >
            <div class="mb-4 flex items-center justify-between">
                <h2
                    class="flex items-center gap-2 font-display text-xl font-bold text-on-surface"
                >
                    <MaterialIcon name="add_task" class="text-primary-fx" />
                    Create New Task
                </h2>
                <button
                    type="button"
                    class="rounded-full p-1 text-on-surface-variant hover:bg-surface-container-low"
                    @click="close"
                >
                    <MaterialIcon name="close" />
                </button>
            </div>

            <form
                class="flex flex-1 flex-col gap-4 overflow-y-auto"
                @submit.prevent="submit"
            >
                <div class="flex flex-col gap-1">
                    <label
                        for="task-title"
                        class="font-mono text-[11px] tracking-widest text-on-surface-variant uppercase"
                        >Task Name</label
                    >
                    <input
                        id="task-title"
                        v-model="form.title"
                        type="text"
                        placeholder="What needs to be done?"
                        class="w-full rounded-lg border border-outline-variant bg-surface-container-lowest px-3 py-2 font-display text-lg text-on-surface placeholder:text-outline-variant focus:border-primary-fx focus:outline-none"
                    />
                    <p v-if="form.errors.title" class="text-sm text-error">
                        {{ form.errors.title }}
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div class="flex flex-col gap-1">
                        <label
                            for="task-due-at"
                            class="font-mono text-[11px] tracking-widest text-on-surface-variant uppercase"
                            >Due Date</label
                        >
                        <input
                            id="task-due-at"
                            v-model="form.due_at"
                            type="datetime-local"
                            class="w-full rounded-lg border border-outline-variant bg-surface-container-lowest px-3 py-2 text-sm text-on-surface focus:border-primary-fx focus:outline-none"
                        />
                        <p v-if="form.errors.due_at" class="text-sm text-error">
                            {{ form.errors.due_at }}
                        </p>
                    </div>

                    <div class="flex flex-col gap-1">
                        <label
                            for="task-priority"
                            class="font-mono text-[11px] tracking-widest text-on-surface-variant uppercase"
                            >Priority</label
                        >
                        <select
                            id="task-priority"
                            v-model="form.priority"
                            class="w-full rounded-lg border border-outline-variant bg-surface-container-lowest px-3 py-2 text-sm text-on-surface focus:border-primary-fx focus:outline-none"
                        >
                            <option value="high">High</option>
                            <option value="medium">Medium</option>
                            <option value="low">Low</option>
                        </select>
                        <p
                            v-if="form.errors.priority"
                            class="text-sm text-error"
                        >
                            {{ form.errors.priority }}
                        </p>
                    </div>
                </div>

                <div class="flex flex-col gap-1">
                    <label
                        for="task-category"
                        class="font-mono text-[11px] tracking-widest text-on-surface-variant uppercase"
                        >Category</label
                    >
                    <select
                        id="task-category"
                        :value="selectedCategoryValue"
                        class="w-full rounded-lg border border-outline-variant bg-surface-container-lowest px-3 py-2 text-sm text-on-surface focus:border-primary-fx focus:outline-none"
                        @change="onCategoryChange"
                    >
                        <option value="">No category</option>
                        <option
                            v-for="category in categories"
                            :key="category.id"
                            :value="category.id"
                        >
                            {{ category.name }}
                        </option>
                        <option value="__new__">+ New category</option>
                    </select>
                    <div
                        v-if="showNewCategory"
                        class="mt-2 flex items-center gap-2"
                    >
                        <input
                            v-model="newCategoryName"
                            type="text"
                            placeholder="New category name"
                            class="flex-1 rounded-lg border border-outline-variant bg-surface-container-lowest px-3 py-2 text-sm text-on-surface focus:border-primary-fx focus:outline-none"
                            @keydown.enter.prevent="createCategory"
                        />
                        <button
                            type="button"
                            class="rounded-lg bg-secondary-container-fx px-3 py-2 text-sm font-medium text-on-secondary-container-fx disabled:opacity-50"
                            :disabled="creatingCategory"
                            @click="createCategory"
                        >
                            Add
                        </button>
                    </div>
                    <p
                        v-if="form.errors.category_id"
                        class="text-sm text-error"
                    >
                        {{ form.errors.category_id }}
                    </p>
                </div>

                <div
                    class="relative overflow-hidden rounded-xl bg-surface-container-low p-4"
                >
                    <h3
                        class="mb-3 flex items-center gap-2 font-display text-base font-bold text-on-surface"
                    >
                        <MaterialIcon name="timer" class="text-primary-fx" />
                        Focus Configuration
                    </h3>
                    <div class="flex items-center gap-4">
                        <span
                            class="w-10 text-right text-sm text-on-surface-variant"
                            >5m</span
                        >
                        <input
                            v-model.number="form.session_minutes"
                            type="range"
                            min="5"
                            max="180"
                            step="5"
                            class="w-full accent-primary-fx"
                        />
                        <span class="w-10 text-sm text-on-surface-variant"
                            >180m</span
                        >
                    </div>
                    <p
                        class="mt-2 text-center font-mono text-sm tracking-widest text-on-surface uppercase"
                    >
                        {{ form.session_minutes }} minutes
                    </p>
                    <p
                        v-if="form.errors.session_minutes"
                        class="text-sm text-error"
                    >
                        {{ form.errors.session_minutes }}
                    </p>
                </div>

                <div class="flex flex-col gap-2">
                    <label
                        class="flex cursor-pointer items-center justify-between rounded-lg border border-outline-variant p-3"
                    >
                        <span class="text-sm text-on-surface"
                            >Notify me when timer ends</span
                        >
                        <input
                            v-model="form.notify_on_end"
                            type="checkbox"
                            class="size-5 accent-primary-fx"
                        />
                    </label>
                    <label
                        class="flex cursor-pointer items-center justify-between rounded-lg border border-outline-variant p-3"
                    >
                        <span class="text-sm text-on-surface"
                            >5-minute warning</span
                        >
                        <input
                            v-model="form.five_minute_warning"
                            type="checkbox"
                            class="size-5 accent-primary-fx"
                        />
                    </label>
                </div>

                <div class="flex flex-col gap-1">
                    <label
                        for="task-description"
                        class="font-mono text-[11px] tracking-widest text-on-surface-variant uppercase"
                    >
                        Description (Optional)
                    </label>
                    <textarea
                        id="task-description"
                        v-model="form.description"
                        rows="3"
                        placeholder="Add any details, links, or sub-tasks here..."
                        class="w-full resize-none rounded-lg border border-outline-variant bg-surface-container-lowest px-3 py-2 text-sm text-on-surface placeholder:text-outline-variant focus:border-primary-fx focus:outline-none"
                    />
                    <p
                        v-if="form.errors.description"
                        class="text-sm text-error"
                    >
                        {{ form.errors.description }}
                    </p>
                </div>

                <div
                    class="mt-2 flex justify-end gap-3 border-t border-outline-variant pt-4"
                >
                    <button
                        type="button"
                        class="rounded-lg px-4 py-2 text-sm font-medium text-on-surface hover:bg-surface-container-low"
                        @click="close"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="flex items-center gap-2 rounded-lg bg-primary-fx px-4 py-2 text-sm font-semibold text-on-primary-fx shadow-sm transition hover:opacity-90 disabled:opacity-50"
                        :disabled="form.processing"
                    >
                        <MaterialIcon name="save" /> Save Task
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
