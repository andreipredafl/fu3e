<script setup lang="ts">
import { useId, type HTMLAttributes } from 'vue';

defineOptions({ inheritAttrs: false });

const props = defineProps<{
    label?: string;
    error?: string;
    class?: HTMLAttributes['class'];
}>();

const model = defineModel<string>({ required: true });

const id = useId();
const errorId = `${id}-error`;
</script>

<template>
    <div class="w-full" :class="props.class">
        <label v-if="label" :for="id" class="mb-1 block text-sm font-medium text-slate-700">
            {{ label }}
        </label>
        <input
            type="text"
            v-bind="$attrs"
            :id="id"
            v-model="model"
            class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200 aria-invalid:border-red-500 aria-invalid:focus:border-red-500 aria-invalid:focus:ring-red-200"
            :aria-invalid="error ? true : undefined"
            :aria-describedby="error ? errorId : undefined"
        />
        <p v-if="error" :id="errorId" class="mt-1 text-sm text-red-600">{{ error }}</p>
    </div>
</template>
