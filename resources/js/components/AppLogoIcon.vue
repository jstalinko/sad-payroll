<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
        <rect x="10" y="10" width="4" height="4" rx="1" fill="#3b82f6" />
        <rect x="6" y="10" width="4" height="4" rx="1" fill="#3b82f6" />
        <rect x="10" y="6" width="4" height="4" rx="1" fill="#3b82f6" />
        <rect x="14" y="10" width="4" height="4" rx="1" fill="#3b82f6" />
        <rect x="10" y="14" width="4" height="4" rx="1" fill="#3b82f6" />
        <rect x="18" y="10" width="4" height="4" rx="1" fill="#3b82f6" />
    </svg>
</template>
