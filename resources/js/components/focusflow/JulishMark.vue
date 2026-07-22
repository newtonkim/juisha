<script setup lang="ts">
// Negative-space Julish mark: solid "j" with a checkmark carved out.
// `fill` is the j color; `check` should match the surface behind the mark.
withDefaults(defineProps<{ fill?: string; check?: string }>(), {
    fill: '#a0f4c8',
    check: '#012d1d',
});
</script>

<template>
    <svg
        viewBox="0 0 62 63"
        xmlns="http://www.w3.org/2000/svg"
        aria-hidden="true"
    >
        <circle cx="39" cy="11.5" r="6.5" :fill="fill" />
        <path d="M 33.5 21 L 44.5 21 L 44.5 38 L 33.5 38 Z" :fill="fill" />
        <path
            d="M 44.5 38 A 13.5 13.5 0 0 1 17.5 38 L 25.5 38 A 5.5 5.5 0 0 0 36.5 38 Z"
            :fill="fill"
        />
        <path
            d="M 24.5 38.5 L 29.5 43.5 L 40.5 30"
            fill="none"
            :stroke="check"
            stroke-width="6"
            stroke-linecap="round"
            stroke-linejoin="round"
        />
    </svg>
</template>
