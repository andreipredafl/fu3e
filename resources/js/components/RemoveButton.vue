<script setup lang="ts">
defineProps<{
    label: string;
}>();
</script>

<template>
    <button
        type="button"
        class="inline-flex size-9 shrink-0 items-center justify-center rounded-md bg-red-600 text-lg leading-none font-semibold text-white shadow-sm hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-200 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 cursor-pointer"
        :aria-label="label"
        :title="label"
    >
        <span aria-hidden="true">&times;</span>
    </button>
</template>
