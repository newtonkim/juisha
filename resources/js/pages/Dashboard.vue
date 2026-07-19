<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import CreateTaskModal from '@/components/focusflow/CreateTaskModal.vue';
import DailyProgressCard from '@/components/focusflow/DailyProgressCard.vue';
import MaterialIcon from '@/components/focusflow/MaterialIcon.vue';
import TaskItem from '@/components/focusflow/TaskItem.vue';
import TimerCard from '@/components/focusflow/TimerCard.vue';
import WorkspaceLayout from '@/layouts/WorkspaceLayout.vue';
import type { CategoryData, DailyStats, TaskItemData } from '@/types/focusflow';

const props = defineProps<{ tasks: TaskItemData[]; categories: CategoryData[]; stats: DailyStats; focus_minutes: number }>();
const showModal = ref(false);
onMounted(() => {
  if (new URLSearchParams(window.location.search).get('new') === '1') {
    showModal.value = true;
  }
});
const firstPending = computed(() => props.tasks.find((t) => t.status === 'pending') ?? null);
</script>
<template>
  <WorkspaceLayout title="Dashboard">
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
      <div class="xl:col-span-2"><TimerCard :minutes="firstPending?.session_minutes ?? focus_minutes" :task-id="firstPending?.id ?? null" /></div>
      <DailyProgressCard :stats="stats" />
    </div>
    <section class="mt-8">
      <div class="mb-4 flex items-center justify-between">
        <h2 class="font-display text-lg font-bold">Today's Tasks</h2>
      </div>
      <div class="space-y-3">
        <TaskItem v-for="task in tasks" :key="task.id" :task="task" />
        <button class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-primary-fx hover:bg-surface-container-low" @click="showModal = true">
          <MaterialIcon name="add_circle" /> Add a new task
        </button>
      </div>
    </section>
    <CreateTaskModal :open="showModal" :categories="categories" @close="showModal = false" />
  </WorkspaceLayout>
</template>
