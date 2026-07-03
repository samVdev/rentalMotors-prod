<script setup lang="ts">
import { ref, onMounted } from 'vue';
import Http from '@/utils/Http';
import { FormatCurrency } from "@/utils/formatCurrency";

const props = defineProps<{
    vehicleId: string
}>();

const emit = defineEmits<{
    (e: 'close'): void
}>();

const url = import.meta.env.VITE_APP_API_URL;
const loading = ref(true);
const vehicleData = ref<any>(null);

const fetchVehicle = async () => {
    try {
        const res = await Http.get(`api/guest/vehicle/${props.vehicleId}`);
        vehicleData.value = res.data;
    } catch (error) {
        console.error("Error al cargar detalles del vehículo", error);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchVehicle();
});
</script>

<template>
    <transition name="modal-fade" appear>
        <div class="fixed inset-0 z-[1000] flex items-center justify-center overflow-hidden bg-black/60 p-4"
            @click.self="$emit('close')">
            <div
                class="relative w-full max-w-3xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden flex flex-col max-h-[90vh]">

                <button @click="$emit('close')"
                    class="absolute top-4 right-4 z-10 bg-white/70 hover:bg-white text-gray-800 rounded-full w-8 h-8 flex items-center justify-center transition-all shadow-sm">
                    <i class="fas fa-times"></i>
                </button>

                <div v-if="loading"
                    class="w-full h-64 sm:h-80 flex items-center justify-center bg-gray-50 flex-shrink-0">
                    <div class="animate-spin h-8 w-8 border-4 border-orange-500 border-t-transparent rounded-full">
                    </div>
                </div>
                <template v-else-if="vehicleData">
                    <div class="w-full h-64 sm:h-[26rem] relative bg-gray-100 flex-shrink-0">
                        <img :src="`${url}/${vehicleData.image}`" :alt="vehicleData.marca"
                            class="w-full h-full object-cover">
                    </div>

                    <div class="p-6 sm:p-8 overflow-y-auto">
                        <div class="flex justify-between items-start mb-2">
                            <h2 class="text-3xl font-bold text-gray-800">{{ vehicleData.marca }} {{ vehicleData.modelo
                                }}</h2>
                            <span class="text-2xl font-black text-orange-500">${{ FormatCurrency(vehicleData.precio)
                                }}</span>
                        </div>
                        <div class="w-16 h-1 bg-orange-500 rounded-full mb-6"></div>

                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                                <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-1">Año</p>
                                <p class="font-bold text-gray-800">{{ vehicleData.year || 'N/A' }}</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                                <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-1">Cilindrada
                                </p>
                                <p class="font-bold text-gray-800">{{ vehicleData.cc ? vehicleData.cc + ' cc' : 'N/A' }}
                                </p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                                <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-1">Kilometraje
                                </p>
                                <p class="font-bold text-gray-800">{{ vehicleData.kilometraje ?
                                    FormatCurrency(vehicleData.kilometraje) + ' km' : '0 km' }}</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                                <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-1">Color</p>
                                <p class="font-bold text-gray-800">{{ vehicleData.color || 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="mt-8 pt-6 border-t border-gray-100 flex justify-end">
                            <button @click="$emit('close')"
                                class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-colors">
                                Cerrar
                            </button>
                        </div>
                    </div>
                </template>
                <!-- error view -->
                <template v-else>
                    <div class="p-8 text-center text-gray-500">
                        No se pudo cargar la información del vehículo.
                    </div>
                </template>
            </div>
        </div>
    </transition>
</template>

<style scoped>
.modal-fade-enter-active,
.modal-fade-leave-active {
    transition: opacity 0.3s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
    opacity: 0;
}
</style>
