<script setup>
import { ref } from 'vue';
import { toast } from 'vue3-toastify';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ConfirmationModal from '@/Components/Shared/ConfirmationModal.vue';
import FormDialog from '@/Components/Shared/FormDialog.vue';
import InputError from '@/Components/InputError.vue';
import { useForm } from '@inertiajs/vue3';
import UnitTable from '@/Components/Tables/Unit/UnitTable.vue';

defineProps({
    list: Object,
});

const confirm = ref(null);

const formDialog = ref({
    visible: false,
    title: null,
    data: null,
});

const onCreate = () => {
    console.log('onCreate');
    formDialog.value.visible = true;
    formDialog.value.title = 'Crear nueva Unidad de Medida';
};

const onEdit = (model) => {
    formDialog.value.visible = true;
    formDialog.value.title = 'Editar Unidad de Medida';

    form.id = model.id;
    form.name = model.name;
    form.symbol = model.symbol;
    form.status = Boolean(model.status);
};


const handleDialogClose = () => {
    formDialog.value.visible = false;
    formDialog.value.title = null;
    form.clearErrors();
    form.reset();
};

const form = useForm({
    id: null,
    name: null,
    symbol: null,
    status: true,
});

const formSuccess = (message) => {
    handleDialogClose();
    form.reset();
    toast.success(message);
};

const formError = (message) => {
    toast.error(message);
};

const submitForm = () => {
    if (form.id) {
        form.put(route('units.update', form.id), {
            preserveScroll: true,
            onSuccess: () => formSuccess('La unidad de medida se actualizó correctamente!'),
            onError: () => formError('Se produjo un error al actualizar la unidad de medida.'),
        });
    } else {
        form.post(route('units.store'), {
            preserveScroll: true,
            onSuccess: () => formSuccess('La unidad de medida se creó correctamente!'),
            onError: () => formError('Se produjo un error al crear la unidad de medida.'),
        });
    }
};

const confirmDelete = async (id) => {
    const ok = await confirm.value.show({
        title: 'Eliminar Unidad de Medida',
        message: '¿Estás seguro que quieres borrarlo? Recuerda que tu acción no se puede deshacer.',
        okButton: 'Delete',
    });

    if (ok) {
        form.delete(route('units.destroy', id), {
            preserveScroll: true,
            onSuccess: () => formSuccess('La unidad de medida se eliminó correctamente!'),
            onError: () => formError('Un error ocurrió mientras se eliminaba la unidad de medida.'),
        });
    }
}
</script>

<template>
    <ConfirmationModal ref="confirm" />
    <FormDialog :isOpen="formDialog.visible" :title="formDialog.title" @close="handleDialogClose" @save="submitForm">
        <form @submit.prevent="submitForm" class="space-y-4">
                <div>
                    <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Nombre de la unidad
                    </label>
                    <input type="text" id="name" v-model="form.name"
                        placeholder="Ejemplo: Kilogramo, Litro, Pieza, Unidad, etc."
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        required>
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>
                <div>
                    <label for="slug" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Símbolo
                    </label>
                    <input type="text" id="slug" v-model="form.symbol"
                        placeholder="Ejemplo: Kg, L, Pz, U"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <InputError class="mt-2" :message="form.errors.symbol" />
                </div>
                <div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input v-model="form.status" type="checkbox" class="sr-only peer">
                        <div
                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                        </div>
                        <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">
                            {{ form.status ? 'Activo' : 'Inactivo' }}
                        </span>
                    </label>
                    <InputError class="mt-2" :message="form.errors.status" />
                </div>
            </form>
    </FormDialog>
    <AdminLayout title="Unidades de Medida">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Unidades de Medida
            </h2>
        </template>

        <section class="mx-auto max-w-screen-xl">
            <UnitTable :data="list" @table:creating="onCreate" @table:updating="onEdit"
                @table:deleting="confirmDelete" />
        </section>
    </AdminLayout>
</template>