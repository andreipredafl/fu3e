<script setup lang="ts">
import { nextTick, ref, useTemplateRef } from 'vue';
import axios from 'axios';
import Button from '../components/ui/Button.vue';
import Input from '../components/ui/Input.vue';
import { downloadFile } from '../utils/downloadFile';

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

const removeColumn = (columnIndex: number) => {
    if (columns.value.length === 1) {
        return;
    }

    const [column] = columns.value.splice(columnIndex, 1);

    data.value.forEach((row) => {
        delete row.cells[column.id];
    });
};

const isExporting = ref(false);
const exportError = ref('');

const submit = async () => {
    isExporting.value = true;
    exportError.value = '';

    try {
        const response = await axios.post(
            '/api/csv-export',
            {
                headings: columns.value.map((column) => column.label),
                rows: data.value.map((row) => columns.value.map((column) => row.cells[column.id])),
            },
            { responseType: 'blob' },
        );

        downloadFile(response.data, 'project-register.csv');
    } catch {
        exportError.value = 'The export failed. Please try again.';
    } finally {
        isExporting.value = false;
    }
};
</script>

<template>
    <section class="mx-auto max-w-4xl space-y-6 px-4">
        <div>
            <h2 class="text-2xl font-semibold text-slate-900">Table to CSV Generator</h2>
            <p class="text-sm text-slate-600">
                Adjust column headings and cell values before exporting.
            </p>
        </div>

        <div class="rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-4">
                <h3 class="text-sm font-medium text-slate-700">Editable Table</h3>
            </div>

            <div class="px-6 py-6">
                <div ref="tableScroller" class="overflow-x-auto scroll-pr-19">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th
                                    v-for="(column, columnIndex) in columns"
                                    :key="column.id"
                                    class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    <div class="flex items-center gap-1">
                                        <Input
                                            v-model="column.label"
                                            placeholder="Heading"
                                            :aria-label="`Heading for column ${columnIndex + 1}`"
                                            class="min-w-24"
                                        />
                                        <Button
                                            variant="danger"
                                            :aria-label="`Remove column ${columnIndex + 1}`"
                                            :disabled="columns.length === 1"
                                            @click="removeColumn(columnIndex)"
                                        >
                                            &times;
                                        </Button>
                                    </div>
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
                                    <Input
                                        v-model="row.cells[column.id]"
                                        :aria-label="`Row ${rowIndex + 1}, column ${columnIndex + 1}`"
                                        class="min-w-24"
                                    />
                                </td>
                                <td class="sticky right-0 bg-white px-4 py-3 text-right">
                                    <Button
                                        variant="danger"
                                        :aria-label="`Remove row ${rowIndex + 1}`"
                                        @click="removeRow(rowIndex)"
                                    >
                                        &times;
                                    </Button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-6 flex gap-3">
                    <Button variant="secondary" @click="addColumn">Add Column</Button>
                    <Button variant="secondary" @click="addRow">Add Row</Button>
                </div>
            </div>

            <div class="flex items-center justify-end gap-4 border-t border-slate-200 px-6 py-4">
                <p role="status" class="sr-only">{{ isExporting ? 'Exporting the table…' : '' }}</p>
                <p v-if="exportError" role="alert" class="text-sm text-red-600">
                    {{ exportError }}
                </p>
                <Button variant="primary" :is-loading="isExporting" @click="submit">Export</Button>
            </div>
        </div>
    </section>
</template>
