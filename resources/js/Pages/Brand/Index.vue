<script setup>
import { ref } from 'vue';
import { toast } from 'vue3-toastify';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ConfirmationModal from '@/Components/Shared/ConfirmationModal.vue';
import FormDialog from '@/Components/Shared/FormDialog.vue';
import InputError from '@/Components/InputError.vue';
import { useForm } from '@inertiajs/vue3';
import BrandTable from '@/Components/Tables/Brand/BrandTable.vue';

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
    formDialog.value.title = 'Crear nueva marca';
};

const onEdit = (model) => {
    formDialog.value.visible = true;
    formDialog.value.title = 'Editar marca';

    form.id = model.id;
    form.name = model.name;
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
    image: null,
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
        form.put(route('brands.update', form.id), {
            preserveScroll: true,
            onSuccess: () => formSuccess('La marca se actualizó correctamente!'),
            onError: () => formError('Se produjo un error al actualizar la marca.'),
        });
    } else {
        form.post(route('brands.store'), {
            preserveScroll: true,
            onSuccess: () => formSuccess('La marca se creó correctamente!'),
            onError: () => formError('Se produjo un error al crear la marca.'),
        });
    }
};

const confirmDelete = async (id) => {
    const ok = await confirm.value.show({
        title: 'Eliminar Categoría',
        message: '¿Estás seguro que quieres borrarlo? Recuerda que tu acción no se puede deshacer.',
        okButton: 'Delete',
    });

    if (ok) {
        form.delete(route('brands.destroy', id), {
            preserveScroll: true,
            onSuccess: () => formSuccess('La marca se eliminó correctamente!'),
            onError: () => formError('Un error ocurrió mientras se eliminaba la marca.'),
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
                    Nombre de la marca
                </label>
                <input type="text" id="name" v-model="form.name" placeholder="Ejemplo: Dragon Ball, TCG, Model Kits, etc."
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    required>
                <InputError class="mt-2" :message="form.errors.name" />
            </div>
        </form>
    </FormDialog>
    <AdminLayout title="Categorías">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Categorías
            </h2>
        </template>

        <section class="mx-auto max-w-screen-xl">
            <BrandTable :data="list" @table:creating="onCreate" @table:updating="onEdit"
                @table:deleting="confirmDelete" />
        </section>
    </AdminLayout>
</template>