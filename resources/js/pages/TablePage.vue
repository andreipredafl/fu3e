<script setup lang="ts">
import { nextTick, ref, useTemplateRef } from 'vue';
import axios from 'axios';

interface Column {
    id: string;
    label: string;
}

interface Row {
    id: string;
    // Cell values keyed by column id.
    cells: Record<string, string>;
}

let nextId = 1;
const createId = (prefix: string) => `${prefix}-${nextId++}`;

const data = ref<Row[]>([
    { id: createId('row'), cells: { project: 'Riverside', contractor: 'North & Co', notes: 'Phase 1' } },
    { id: createId('row'), cells: { project: 'Station Yard', contractor: 'West Build', notes: '' } },
    { id: createId('row'), cells: { project: 'Orchard House', contractor: 'Green Works', notes: 'Review, then approve' } },
]);

const columns = ref<Column[]>([
    { id: 'project', label: 'project' },
    { id: 'contractor', label: 'contractor' },
    { id: 'notes', label: 'notes' },
]);

const addRow = () => {
    data.value.push({
        id: createId('row'),
        cells: Object.fromEntries(columns.value.map((column) => [column.id, ''])),
    });
};

const removeRow = (rowIndex: number) => {
    data.value.splice(rowIndex, 1);
};

const tableScroller = useTemplateRef<HTMLElement>('tableScroller');

const addColumn = async () => {
    const column: Column = { id: createId('column'), label: '' };

    columns.value.push(column);
    data.value.forEach((row) => {
        row.cells[column.id] = '';
    });

    // Bring the new column into view when the table is wider than its card.
    await nextTick();
    tableScroller.value?.scrollTo({ left: tableScroller.value.scrollWidth });
};

const submit = () => {
    return axios.post('/api/csv-export', {
        headings: columns.value.map((column) => column.label),
        rows: data.value.map((row) => columns.value.map((column) => row.cells[column.id])),
    });
};
</script>

<template>
    <section class="mx-auto max-w-4xl space-y-6 px-4">
        <div>
            <h2 class="text-2xl font-semibold text-slate-900">Table to CSV Generator</h2>
            <p class="text-sm text-slate-600">
                Adjust column keys and row values before exporting.
            </p>
        </div>

        <div class="rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-4">
                <h3 class="text-sm font-medium text-slate-700">Editable Table</h3>
            </div>

            <div class="px-6 py-6">
                <div ref="tableScroller" class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th
                                    v-for="(column, columnIndex) in columns"
                                    :key="column.id"
                                    class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    <input
                                        v-model="column.label"
                                        type="text"
                                        placeholder="Heading"
                                        :aria-label="`Heading for column ${columnIndex + 1}`"
                                        class="w-full min-w-32 rounded-md border border-slate-300 px-3 py-2 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                    />
                                </th>
                                <th class="sticky right-0 bg-slate-50 px-4 py-3">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            <tr
                                v-for="(row, rowIndex) in data"
                                :key="row.id"
                                class="bg-white text-sm text-slate-700"
                            >
                                <td
                                    v-for="(column, columnIndex) in columns"
                                    :key="column.id"
                                    class="px-4 py-3"
                                >
                                    <input
                                        v-model="row.cells[column.id]"
                                        type="text"
                                        :aria-label="`Row ${rowIndex + 1}, column ${columnIndex + 1}`"
                                        class="w-full min-w-32 rounded-md border border-slate-300 px-3 py-2 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                    />
                                </td>
                                <td class="sticky right-0 bg-white px-4 py-3 text-right">
                                    <button
                                        type="button"
                                        class="inline-flex size-9 items-center justify-center rounded-md bg-red-600 text-lg leading-none font-semibold text-white shadow-sm hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-200 focus:ring-offset-2"
                                        :aria-label="`Remove row ${rowIndex + 1}`"
                                        :title="`Remove row ${rowIndex + 1}`"
                                        @click="removeRow(rowIndex)"
                                    >
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-6 flex gap-3">
                    <button
                        type="button"
                        class="rounded-md border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:ring-offset-2"
                        @click="addColumn"
                    >
                        Add Column
                    </button>
                    <button
                        type="button"
                        class="rounded-md border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:ring-offset-2"
                        @click="addRow"
                    >
                        Add Row
                    </button>
                </div>
            </div>

            <div class="flex justify-end border-t border-slate-200 px-6 py-4">
                <button
                    type="button"
                    class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:ring-offset-2"
                    @click="submit()"
                >
                    Export
                </button>
            </div>
        </div>
    </section>
</template>
