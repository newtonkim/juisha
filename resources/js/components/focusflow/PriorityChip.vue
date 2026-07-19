<script setup lang="ts">
defineProps<{ priority: 'high' | 'medium' | 'low' }>();
const styles = {
  high: 'bg-error-container text-on-error-container',
  medium: 'bg-secondary-container-fx/60 text-on-secondary-container-fx',
  low: 'bg-surface-container text-on-surface-variant',
} as const;
</script>
<template>
  <span class="rounded px-2 py-0.5 font-mono text-[11px] font-medium uppercase tracking-widest" :class="styles[priority]">
    {{ priority }} priority
  </span>
</template>
