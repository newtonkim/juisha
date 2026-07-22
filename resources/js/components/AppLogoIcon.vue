<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

type Props = {
    className?: HTMLAttributes['class'];
};

defineProps<Props>();
</script>

<template>
    <!-- Julish mark: negative-space "j" with a carved checkmark.
         The j fills with currentColor; override the carve color with
         the --jl-check CSS variable to match the surface behind it. -->
    <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 62 63"
        :class="className"
        v-bind="$attrs"
    >
        <circle cx="39" cy="11.5" r="6.5" fill="currentColor" />
        <path
            d="M 33.5 21 L 44.5 21 L 44.5 38 L 33.5 38 Z"
            fill="currentColor"
        />
        <path
            d="M 44.5 38 A 13.5 13.5 0 0 1 17.5 38 L 25.5 38 A 5.5 5.5 0 0 0 36.5 38 Z"
            fill="currentColor"
        />
        <path
            d="M 24.5 38.5 L 29.5 43.5 L 40.5 30"
            fill="none"
            stroke="var(--jl-check, #012d1d)"
            stroke-width="6"
            stroke-linecap="round"
            stroke-linejoin="round"
        />
    </svg>
</template>
