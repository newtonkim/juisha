<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import MaterialIcon from '@/components/focusflow/MaterialIcon.vue';
import PriorityChip from '@/components/focusflow/PriorityChip.vue';
import type { TaskItemData } from '@/types/focusflow';

const props = defineProps<{ task: TaskItemData }>();

const toggle = () => {
  router.patch(`/tasks/${props.task.id}/toggle`, {}, { preserveScroll: true });
};

const open = () => {
  if (props.task.status === 'pending') {
    router.visit(`/focus/${props.task.id}`);
  }
};
</script>
<template>
  <div
    class="task-card-hover flex items-start gap-4 rounded-xl border border-outline-variant bg-surface-container-lowest p-4"
    :class="{ 'opacity-60': task.status === 'completed' }"
  >
    <button
      type="button"
      class="mt-1 flex size-6 shrink-0 items-center justify-center rounded-md border-2 transition-colors"
      :class="task.status === 'completed' ? 'border-primary-fx bg-primary-fx' : 'border-outline-variant hover:border-primary-fx'"
      :aria-label="task.status === 'completed' ? 'Mark as pending' : 'Mark as completed'"
      @click="toggle"
    >
      <MaterialIcon v-if="task.status === 'completed'" name="check" class="text-[16px] text-on-primary-fx" />
    </button>
    <div class="flex-1 cursor-pointer" @click="open">
      <div class="flex items-start justify-between gap-2">
        <span class="font-medium text-on-surface" :class="{ 'line-through': task.status === 'completed' }">{{ task.title }}</span>
        <PriorityChip :priority="task.priority" />
      </div>
      <p v-if="task.description" class="mt-1 text-sm text-on-surface-variant" :class="{ 'line-through': task.status === 'completed' }">
        {{ task.description }}
      </p>
      <p v-if="task.category" class="mt-1 font-mono text-[11px] uppercase tracking-widest text-on-surface-variant">{{ task.category.name }}</p>
    </div>
  </div>
</template>
