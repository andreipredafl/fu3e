<script setup lang="ts">
import { ref } from 'vue';
import axios from 'axios';

interface Column {
    key: string;
}

type Row = Record<string, string>;

const data = ref<Row[]>([
    { project: 'Riverside', contractor: 'North & Co', notes: 'Phase 1' },
    { project: 'Station Yard', contractor: 'West Build', notes: '' },
    { project: 'Orchard House', contractor: 'Green Works', notes: 'Review, then approve' },
]);

const columns = ref<Column[]>([
    { key: 'project' },
    { key: 'contractor' },
    { key: 'notes' },
]);

const addRow = () => {
    // Add new row to data with column keys
};

const removeRow = (rowIndex: number) => {
    // remove the given row
};

const addColumn = () => {
    // Add new column to the dataset
};

const updateColumnKey = (column: Column, event: Event) => {
    const target = event.target as HTMLInputElement;
    const oldKey = column.key;

    const columnKeyExists = !!columns.value.find((item) => item.key === target.value);

    column.key = target.value;

    if (columnKeyExists) {
        column.key = target.value.substring(0, target.value.length - 1);
        return;
    }

    data.value.forEach((row) => {
        if (row[oldKey]) {
            row[column.key] = row[oldKey];
            delete row[oldKey];
        }
    });
};

const submit = () => {
    console.log('Submitting data:', data.value);
    return axios.patch('/api/csv-export', data.value);
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
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th
                                    v-for="column in columns"
                                    :key="column.key"
                                    class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    <input
                                        type="text"
                                        class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                        :value="column.key"
                                        @input="updateColumnKey(column, $event)"
                                    />
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            <tr
                                v-for="(row, rowIndex) in data"
                                :key="rowIndex"
                                class="bg-white text-sm text-slate-700"
                            >
                                <td
                                    v-for="(dataColumn, columnName) in row"
                                    :key="columnName"
                                    class="px-4 py-3"
                                >
                                    <input
                                        v-model="row[columnName]"
                                        type="text"
                                        class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                    />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-6 flex gap-3">
                    <button
                        type="button"
                        class="rounded-md border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:ring-offset-2"
                    >
                        Add Column
                    </button>
                    <button
                        type="button"
                        class="rounded-md border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:ring-offset-2"
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
