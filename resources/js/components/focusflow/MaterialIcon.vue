<script setup lang="ts">
defineProps<{ name: string; fill?: boolean }>();
</script>
<template>
  <span class="material-symbols-outlined select-none" :class="{ 'is-filled': fill }" aria-hidden="true">{{ name }}</span>
</template>
