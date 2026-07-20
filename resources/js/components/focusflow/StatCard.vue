<script setup lang="ts">
defineProps<{ label: string; value: string; sub?: string; highlight?: boolean }>();
</script>
<template>
  <div
    class="flex flex-col gap-2 rounded-xl border p-4 shadow-sm"
    :class="highlight ? 'border-primary-fx/30 bg-primary-container-fx/10' : 'border-outline-variant/50 bg-surface-container-lowest'"
  >
    <span
      class="font-mono text-[11px] uppercase tracking-widest"
      :class="highlight ? 'text-primary-fx' : 'text-on-surface-variant'"
    >
      {{ label }}
    </span>
    <div class="flex items-baseline gap-2">
      <span class="font-display text-[40px] font-bold leading-none" :class="highlight ? 'text-primary-fx' : 'text-on-surface'">
        {{ value }}
      </span>
      <span v-if="sub" class="text-sm" :class="highlight ? 'text-primary-fx' : 'text-primary-fx'">{{ sub }}</span>
    </div>
  </div>
</template>
