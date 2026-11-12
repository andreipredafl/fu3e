<script setup lang="ts">
type Variant = 'primary' | 'secondary' | 'danger';

const props = defineProps<{
    variant: Variant;
    isLoading?: boolean;
}>();

const emit = defineEmits<{
    click: [event: MouseEvent];
}>();

const onClick = (event: MouseEvent) => {
    if (!props.isLoading) {
        emit('click', event);
    }
};

const variantClasses: Record<Variant, string> = {
    primary: 'bg-blue-600 font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:ring-blue-200',
    secondary: 'border border-slate-300 font-medium text-slate-700 hover:bg-slate-100 focus-visible:ring-blue-200',
    danger: 'bg-red-600 font-semibold text-white shadow-sm hover:bg-red-500 focus-visible:ring-red-200',
};
</script>

<template>
    <button
        type="button"
        class="cursor-pointer rounded-md px-4 py-2 text-sm transition-opacity focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 aria-busy:cursor-progress aria-busy:opacity-50 aria-busy:delay-300"
        :class="variantClasses[variant]"
        :aria-busy="isLoading || undefined"
        :aria-disabled="isLoading || undefined"
        @click="onClick"
    >
        <slot />
    </button>
</template>
