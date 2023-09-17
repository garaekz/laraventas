<script setup>
import { computed } from 'vue'
import { XMarkIcon } from '@heroicons/vue/24/outline'
import {
    TransitionRoot,
    TransitionChild,
    Dialog,
    DialogPanel,
    DialogTitle,
} from '@headlessui/vue'

const props = defineProps({
    isOpen: Boolean,
    title: String,
    size: {
        type: String,
        default: '2xl',
    },
    okButton: {
        type: String,
        default: 'Guardar',
    },
    cancelButton: {
        type: String,
        default: 'Cancelar',
    },
    showCancelButton: {
        type: Boolean,
        default: true,
    },
})

const emit = defineEmits(['close', 'save'])

const sizeClass = computed(() => {
    return {
        'sm': 'sm:max-w-sm',
        'md': 'sm:max-w-md',
        'lg': 'sm:max-w-lg',
        'xl': 'sm:max-w-xl',
        '2xl': 'sm:max-w-2xl',
        '3xl': 'sm:max-w-3xl',
        '4xl': 'sm:max-w-4xl',
    }[props.size];
});
</script>
<template>
    <TransitionRoot appear :show="isOpen" as="template">
        <Dialog as="div" class="relative z-50">
            <TransitionChild as="template" enter="duration-300 ease-out" enter-from="opacity-0" enter-to="opacity-100"
                leave="duration-200 ease-in" leave-from="opacity-100" leave-to="opacity-0">
                <div class="fixed inset-0 bg-black bg-opacity-25" />
            </TransitionChild>

            <div class="fixed inset-0 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center">
                    <TransitionChild as="template" enter="duration-300 ease-out" enter-from="opacity-0 scale-95"
                        enter-to="opacity-100 scale-100" leave="duration-200 ease-in" leave-from="opacity-100 scale-100"
                        leave-to="opacity-0 scale-95">
                        <DialogPanel
                            :class="sizeClass"
                            class="w-full transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 p-6 text-left align-middle shadow-xl transition-all">
                            <DialogTitle as="h3" class="text-lg font-medium leading-6 text-gray-500 dark:text-gray-300">
                                <h4 id="drawer-label"
                                    class="mb-1.5 leading-none text-xl font-semibold text-gray-900 dark:text-white">
                                    {{ title }}
                                </h4>
                                <button @click="emit('close')" type="button"
                                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 absolute top-2.5 right-2.5 inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white dark:bg-gray-700">
                                    <XMarkIcon class="w-5 h-5" />
                                    <span class="sr-only">Cerrar menú</span>
                                </button>
                            </DialogTitle>
                            <div class="mt-6">
                                <p class="text text-gray-500 dark:text-gray-300">
                                    <slot />
                                </p>
                            </div>

                            <div class="mt-4 flex justify-end gap-x-3">
                                <button v-if="showCancelButton" type="button" @click="emit('close')"
                                    class="inline-flex justify-center rounded-md border border-transparent bg-gray-100 px-4 py-2 text-sm font-medium text-gray-900 hover:bg-gray-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-500 focus-visible:ring-offset-2 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                                    {{ cancelButton }}
                                </button>
                                <button type="button" @click="emit('save')"
                                    class="inline-flex justify-center rounded-md border border-transparent bg-green-500 px-4 py-2 text-sm font-bold text-white hover:bg-green-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2 dark:bg-green-700 dark:hover:bg-green-800">
                                    {{ okButton }}
                                </button>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>
  