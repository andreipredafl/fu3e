<template>
    <div class="min-h-screen bg-slate-100 text-slate-900">
        <header class="bg-white shadow-sm">
            <nav class="mx-auto flex max-w-4xl items-center justify-between px-4 py-4">
                <h1 class="text-lg font-semibold">fu3e Developer Test</h1>
                <div class="flex gap-4 text-sm font-medium">
                    <RouterLink
                        class="transition hover:text-blue-600"
                        to="/"
                    >
                        Home
                    </RouterLink>
                    <RouterLink
                        class="transition hover:text-blue-600"
                        to="/table"
                    >
                        Table
                    </RouterLink>
                </div>
            </nav>
        </header>
        <main class="mx-auto max-w-4xl px-4 py-10">
            <RouterView />
        </main>
    </div>
</template>

<script setup lang="ts">
import { RouterLink, RouterView } from 'vue-router';
</script>
