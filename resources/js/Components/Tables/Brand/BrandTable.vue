<script setup>
import dayjs from 'dayjs';
import { router } from '@inertiajs/vue3';
import DataTable from '@/Components/Shared/DataTable.vue';

defineProps({
    data: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits([
    'table:creating',
    'table:updating',
    'table:deleting',
    'table:page-change',
    'table:search'
]);

const dateFormat = (date) => {
    return dayjs(date).format('MMMM D, YYYY h:mm A');
};

const columns = [
    {
        key: 'id',
        label: '#',
    },
    {
        key: 'name',
        label: 'Nombre',
    },
    {
        key: 'created_at',
        label: 'Fecha de creación',
    },
];

const onSearch = (search) => {
    router.get(route('brands.index'), {
        'filter[search]': search,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const onPageChange = (query) => {
    router.reload({ data: query });
};
</script>
<template>
    <DataTable 
        :data="data"
        :columns="columns"
        createLabel="Nueva marca"
        @table:search="onSearch"
        @table:page-change="onPageChange"
        @table:creating="emit('table:creating')"
        @table:updating="emit('table:updating', $event)"
        @table:deleting="emit('table:deleting', $event)"
    >
        <template #name="{ item }">
            <div class="flex flex-col">
                <span class="text-lg font-medium text-gray-900 dark:text-white">
                    {{ item.name }}
                </span>
            </div>
        </template>
        <template #created_at="{ item }">
            {{ dateFormat(item.created_at) }}
        </template>
    </DataTable>
</template>