<script setup lang="ts">
import { reactive, ref } from 'vue';
import { FormatCurrency, unFormatCurrency } from "@/utils/formatCurrency";

const loading = ref(false);

const emit = defineEmits<{
    (e: 'close'): void;
    (e: 'submit', data: any): void;
}>();

const maintenanceData = reactive({
    price: '' as string | number | null,
});

const onPriceInput = (e: Event) => {
    const target = e.target as HTMLInputElement;
    maintenanceData.price = FormatCurrency(target.value);
};

const submitForm = () => {
    const dataToSend = {
        ...maintenanceData,
        price: unFormatCurrency(maintenanceData.price)
    };
    emit('submit', dataToSend);
};

const closeModal = () => {
    emit('close');
    maintenanceData.price = null;
};
</script>


<template>
    <transition name="modal-fade">
        <div
            class="fixed inset-0 z-50 flex items-center justify-center overflow-x-hidden overflow-y-auto outline-none focus:outline-none p-4">
            <div class="absolute inset-0 bg-black opacity-50" @click="closeModal"></div>
            <div class="relative w-full max-w-lg mx-auto bg-white rounded-xl shadow-2xl transform transition-all">
                <div class="flex items-center justify-between p-5 border-b border-gray-100">
                    <h3 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-check-double text-green-500"></i>
                        Finalizar Mantenimiento
                    </h3>
                    <button @click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form @submit.prevent="submitForm" class="p-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Precio del Servicio ($)
                            </label>
                            <div class="relative group">
                                <input :value="maintenanceData.price" @input="onPriceInput" type="text" required
                                    class="block w-full pl-7 pr-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all outline-none"
                                    placeholder="0,00$">
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex flex-col sm:flex-row gap-3">
                        <button type="button" @click="closeModal"
                            class="flex-1 px-4 py-2.5 text-gray-600 bg-gray-50 hover:bg-gray-100 rounded-lg font-medium transition-colors">
                            Cancelar
                        </button>
                        <button type="submit" :disabled="loading"
                            class="flex-1 px-4 py-2.5 text-white bg-blue-600 hover:bg-blue-700 disabled:bg-blue-300 rounded-lg font-bold shadow-lg shadow-blue-100 transition-all flex items-center justify-center gap-2">
                            <span v-if="loading"
                                class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full"></span>
                            {{ loading ? 'Procesando...' : 'Confirmar Finalización' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </transition>

</template>


<style>
.modal-fade-enter-active,
.modal-fade-leave-active {
    transition: opacity 0.3s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
    opacity: 0;
}

.modal-fade-enter-active .relative,
.modal-fade-leave-active .relative {
    transition: transform 0.3s ease-out;
}

.modal-fade-enter-from .relative {
    transform: scale(0.95);
}
</style>