<script setup>
import { ref } from 'vue';
import { toast } from 'vue3-toastify';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ConfirmationModal from '@/Components/Shared/ConfirmationModal.vue';
import FormDialog from '@/Components/Shared/FormDialog.vue';
import InputError from '@/Components/InputError.vue';
import { useForm } from '@inertiajs/vue3';
import ProductTable from '@/Components/Tables/Product/ProductTable.vue';

defineProps({
    list: Object,
    units: {
        type: Array,
        default: () => [],
    },
    brands: {
        type: Array,
        default: () => [],
    },
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
    formDialog.value.title = 'Crear nuevo producto';
};

const onEdit = (model) => {
    formDialog.value.visible = true;
    formDialog.value.title = 'Editar producto';

    form.id = model.id;
    form.name = model.name;
    form.status = Boolean(model.status);
    form.imagePreview = model.image_url;
};

const handleDialogClose = () => {
    formDialog.value.visible = false;
    formDialog.value.title = null;
    form.clearErrors();
    form.reset();
};

const form = useForm({
    id: null,
    name: "",
    status: true,
    price: null,
    image: null,
    imagePreview: null,
    brand: null,
    unit: null,
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
        form.put(route('products.update', form.id), {
            preserveScroll: true,
            onSuccess: () => formSuccess('El producto se actualizó correctamente!'),
            onError: () => formError('Se produjo un error al actualizar el producto.'),
        });
    } else {
        form.post(route('products.store'), {
            preserveScroll: true,
            onSuccess: () => formSuccess('El producto se creó correctamente!'),
            onError: () => formError('Se produjo un error al crear el producto.'),
        });
    }
};

const confirmDelete = async (id) => {
    const ok = await confirm.value.show({
        title: 'Eliminar Producto',
        message: '¿Estás seguro que quieres borrarlo? Recuerda que tu acción no se puede deshacer.',
        okButton: 'Delete',
    });

    if (ok) {
        form.delete(route('products.destroy', id), {
            preserveScroll: true,
            onSuccess: () => formSuccess('El producto se eliminó correctamente!'),
            onError: () => formError('Un error ocurrió mientras se eliminaba el producto.'),
        });
    }
}

const imageFile = ref(null);
const handleFileDrop = (e) => {
    if (imageFile.value) {
        form.image = imageFile.value.files[0];
    }
    const file = e.target.files[0];
    const reader = new FileReader();

    reader.onload = (e) => {
        const blobUrl = URL.createObjectURL(file);
        form.imagePreview = blobUrl;
    };

    reader.readAsDataURL(file);
};

const removeImage = () => {
    if (form.imagePreview) {
        URL.revokeObjectURL(form.imagePreview);
    }
    form.image = null;
    form.imagePreview = null;
    imageFile.value = null;
};
</script>

<template>
    <ConfirmationModal ref="confirm" />
    <FormDialog :isOpen="formDialog.visible" :title="formDialog.title" @close="handleDialogClose" @save="submitForm"
        size="4xl">
        <form @submit.prevent="submitForm" class="space-y-4">

            <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex items-center justify-start w-full">
                    <div v-if="form.imagePreview" class="flex">
                        <div class="relative w-48 h-48">
                            <div class="rounded-lg overflow-hidden w-full h-full">
                                <img :src="form.imagePreview" class="w-full h-full object-cover" />
                            </div>
                            <button type="button" @click="removeImage" aria-label="Remove image"
                                class="absolute top-0 right-0 bg-red-600 hover:bg-red-700 focus:outline-none text-white rounded-full h-8 w-8 flex items-center justify-center -mt-2 -mr-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" class="h-5 w-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div v-else class="flex items-center justify-center w-full">
                        <label
                            class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-bray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                </svg>
                                <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">
                                        Click to upload</span> or drag and drop</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG or Webp (MAX. 800x800px)
                                </p>
                            </div>
                            <input @change="handleFileDrop" ref="imageFile" type="file" class="hidden" />
                        </label>
                    </div>
                </div>
                <div class="w-full flex flex-col gap-4">
                    <div class="w-full">
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Nombre del producto
                        </label>
                        <input type="text" v-model="form.name"
                            placeholder="Ejemplo: Dragon Ball, TCG, Model Kits, etc."
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            required>
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>
                    <div class="w-full">
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Precio de venta
                        </label>
                        <div class="flex">
                            <span
                                class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-200 border border-r-0 border-gray-300 rounded-l-md dark:bg-gray-600 dark:text-gray-400 dark:border-gray-600 font-extrabold">
                                $
                            </span>
                            <input v-model="form.price" type="number"
                                class="rounded-none rounded-r-lg bg-gray-50 border text-gray-900 focus:ring-blue-500 focus:border-blue-500 block flex-1 min-w-0 w-full text-sm border-gray-300 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="100.00">
                        </div>
                        <InputError class="mt-2" :message="form.errors.price" />
                    </div>
                    <div class="w-full">
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Unidad de medida
                        </label>
                        <select v-model="form.unit"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            required>
                            <option :value="null" disabled>Selecciona una unidad de medida</option>
                            <option v-for="unit in units" :key="unit.id" :value="unit.id">{{ unit.name }}</option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.unit" />
                    </div>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="w-full sm:w-1/2">
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Marca
                    </label>
                    <select v-model="form.brand"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option :value="null">Selecciona una marca</option>
                        <option v-for="brand in brands" :key="brand.id" :value="brand.id">{{ brand.name }}</option>
                    </select>
                    <InputError class="mt-2" :message="form.errors.brand" />
                </div>
                <div class="w-full sm:w-1/2">
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Código de barras / SKU / Identificador único
                    </label>
                    <input type="text" v-model="form.code"
                        placeholder="Ejemplo: Dragon Ball, TCG, Model Kits, etc."
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        required>
                    <InputError class="mt-2" :message="form.errors.code" />
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-4">

                <div class="w-full sm:w-1/2">
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Mínimo en stock (alerta)
                    </label>
                    <input type="number" v-model="form.min_stock" placeholder="0"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <InputError class="mt-2" :message="form.errors.min_stock" />
                </div>
            </div>
            <div>
                <label for="notes" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Notas
                    adicionales</label>
                <textarea id="notes" rows="4"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Cualquier dato extra que desees..."></textarea>
                <InputError class="mt-2" :message="form.errors.notes" />
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
    <AdminLayout title="Productos">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Productos
            </h2>
        </template>

        <section class="mx-auto max-w-screen-xl">
            {{ list }}
            <ProductTable :data="list" @table:creating="onCreate" @table:updating="onEdit"
                @table:deleting="confirmDelete" />
        </section>
    </AdminLayout>
</template>