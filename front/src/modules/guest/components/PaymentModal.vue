<script setup lang="ts">
import { ref, onMounted } from "vue";
import { alerta } from "@/utils/alert";
import UploadField from "@/modules/guest/components/UploadField.vue";
import GuestService from '../services'
import { alertWithToast } from "@/utils/toast";
import { parsePrices } from '@/utils/parsePrices';

const props = defineProps<{
    id: any,
    total?: number,
    moraId?: number | null
}>();

const emit = defineEmits<{
    (e: "close"): void;
    (e: "reload"): void;
}>();

const currentStep = ref(1);
const paymentType = ref<'manual' | 'bold' | null>(null);
const selectedMethodId = ref<number | null>(null);
const totalInput = ref(props.total ? props.total : '');
const imgFile = ref<File | null>(null);
const imgURL = ref<string | null>(null);

const accountMethods = ref<any[]>([]);
const isLoadingMethods = ref(true);
const isSubmitting = ref(false);

onMounted(async () => {
    try {
        const response = await GuestService.getActiveAccountMethods();
        accountMethods.value = response.data;
    } catch (err) {
        console.error("Error fetching account methods", err)
    } finally {
        isLoadingMethods.value = false;
    }
});

function handleSetImg(payload: { imgFile: File | null; imgURL: string | null }) {
    imgFile.value = payload.imgFile;
    imgURL.value = payload.imgURL;
}

const nextStep = () => {
    if (currentStep.value === 1) {
        if (!paymentType.value) {
            alerta("Info", "Por favor selecciona un método de pago.", "info");
            return;
        }
        if (paymentType.value === 'bold') {
            window.open("https://bold.co/", "_blank");
            return;
        }
        currentStep.value++;
    }
};

const prevStep = () => {
    if (currentStep.value > 1) {
        currentStep.value--;
    }
};

async function confirmUpload() {
    if (!selectedMethodId.value) {
        alerta("Info", "Por favor selecciona una cuenta de destino.", "info");
        return;
    }

    if (!totalInput.value) {
        alerta("Info", "Por favor ingrese el total del pago realizado.", "info");
        return;
    }

    if (!imgFile.value) {
        alerta("Info", "Por favor anexa la imagen (comprobante) del pago.", "info");
        return;
    }

    isSubmitting.value = true;
    try {
        const form = new FormData();
        form.append('account_method_id', selectedMethodId.value!.toString());
        form.append('total', totalInput.value.toString());
        form.append('upload-comprobante', imgFile.value);

        if (props.moraId) {
            form.append('mora_id', props.moraId.toString());
        }

        const response = await GuestService.storePayService(props.id, form)
        alertWithToast(
            response.data.message || 'Pago registrado con éxito.',
            'success',
        )
        emit('close');
        emit('reload');
    } catch (error: any) {
        alertWithToast(
            error?.response?.data?.message || error?.message || 'Ocurrió un error al registrar el pago.',
            'error',
        )
    } finally {
        isSubmitting.value = false;
    }
}
</script>

<template>
    <div class="fixed inset-0 flex items-center justify-center bg-gray-900/60 z-50 overflow-y-auto py-10 transition-all"
        @click.self="emit('close')">
        <div class="bg-white rounded-xl shadow-xl w-[95%] mx-auto flex flex-col overflow-hidden transition-all duration-300 ease-in-out"
            :class="currentStep === 2 ? '2xl:w-[1300px] xl:w-[1240px] lg:w-[1100px]' : 'md:w-[750px]'">

            <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-xl font-bold text-gray-800">Registrar Pago</h2>
                <button @click="emit('close')" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <font-awesome-icon icon="times" class="text-xl" />
                </button>
            </div>

            <div class="px-6 py-6 flex-grow relative overflow-y-auto max-h-[75vh]">

                <div v-if="currentStep === 1">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">1. Selecciona el tipo de pago</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="cursor-pointer border rounded-lg p-5 transition-all text-center flex flex-col items-center gap-3"
                            :class="paymentType === 'bold' ? 'border-[#FF7539] bg-orange-50' : 'border-gray-200 hover:border-orange-200 hover:bg-gray-50'"
                            @click="paymentType = 'bold'">
                            <div class="text-3xl text-gray-700 mt-2">
                                <font-awesome-icon icon="credit-card" />
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-gray-800">Pago en línea (Bold)</h4>
                                <p class="text-gray-500 text-sm mt-1">Tarjeta de crédito o débito, PSE.</p>
                            </div>
                        </div>

                        <div class="cursor-pointer border rounded-lg p-5 transition-all text-center flex flex-col items-center gap-3"
                            :class="paymentType === 'manual' ? 'border-[#FF7539] bg-orange-50' : 'border-gray-200 hover:border-orange-200 hover:bg-gray-50'"
                            @click="paymentType = 'manual'">
                            <div class="text-3xl text-gray-700 mt-2">
                                <font-awesome-icon icon="money-bill-transfer" />
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-gray-800">Transferencia / Depósito</h4>
                                <p class="text-gray-500 text-sm mt-1">Consignación bancaria, billetera virtual.</p>
                            </div>
                        </div>
                    </div>

                    <div v-if="props.total"
                        class="mt-8 bg-gray-50 rounded-lg p-4 border border-gray-200 flex justify-between items-center">
                        <span class="text-gray-600 font-medium">Monto adeudado sugerido:</span>
                        <span class="text-xl font-bold text-gray-800">{{ parsePrices(props.total) }}</span>
                    </div>
                </div>

                <div v-else-if="currentStep === 2">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                        <div class="flex flex-col h-full">
                            <h3 class="text-lg font-semibold text-gray-700 mb-3 border-b pb-2">1. Datos para
                                transferencia</h3>

                            <div v-if="isLoadingMethods" class="flex justify-center py-6">
                                <font-awesome-icon icon="spinner" spin class="text-2xl text-gray-400" />
                            </div>
                            <div v-else-if="accountMethods.length === 0" class="text-center py-6 text-gray-500 text-sm">
                                No hay cuentas configuradas en el sistema.
                            </div>
                            <div v-else
                                class="grid grid-cols-1 gap-3 flex-grow content-start pr-2 overflow-y-auto max-h-[500px]">
                                <div v-for="method in accountMethods" :key="method.id"
                                    @click="selectedMethodId = method.id"
                                    class="cursor-pointer border rounded-lg p-4 transition-all"
                                    :class="selectedMethodId === method.id ? 'border-[#FF7539] bg-orange-50 shadow-sm' : 'border-gray-200 hover:border-orange-200 bg-white'">

                                    <div class="flex items-center justify-between mb-3 border-b border-orange-100 pb-2">
                                        <div class="flex items-center gap-2">
                                            <span class="text-lg text-gray-400">
                                                <font-awesome-icon
                                                    :icon="method.type === 'bank' ? 'building-columns' : (method.type === 'wallet' ? 'mobile-screen' : 'coins')" />
                                            </span>
                                            <span class="font-bold text-gray-800 text-base">{{ method.provider_name
                                            }}</span>
                                        </div>
                                        <font-awesome-icon v-if="selectedMethodId === method.id" icon="check-circle"
                                            class="text-[#FF7539] text-xl" />
                                    </div>

                                    <div class="space-y-2 text-sm">
                                        <div
                                            class="flex justify-between items-center text-gray-700 bg-white px-2 py-1.5 rounded-md border border-gray-100">
                                            <span class="font-semibold">{{ method.type === 'wallet' ? 'Número' : "Nro. Cuenta" }}:</span>
                                            <span class="font-mono text-base font-bold text-gray-900 select-all">{{
                                                method.identifier }}</span>
                                        </div>

                                        <div class="grid grid-cols-2 gap-2 text-gray-600 px-1">
                                            <div v-if="method.holder_name" class="flex flex-col">
                                                <span
                                                    class="text-[10px] uppercase font-bold text-gray-400">Titular</span>
                                                <span class="truncate" :title="method.holder_name">{{ method.holder_name
                                                }}</span>
                                            </div>

                                            <div v-if="method.holder_dni" class="flex flex-col">
                                                <span
                                                    class="text-[10px] uppercase font-bold text-gray-400">Identificación</span>
                                                <span>{{ method.holder_dni }}</span>
                                            </div>

                                            <div v-if="method.network_or_type" class="flex flex-col">
                                                <span class="text-[10px] uppercase font-bold text-gray-400">Tipo /
                                                    Red</span>
                                                <span>{{ method.network_or_type }}</span>
                                            </div>
                                        </div>

                                        <div v-if="method.notes"
                                            class="mt-2 bg-yellow-50 text-yellow-800 p-2 rounded text-xs border border-yellow-100">
                                            <i class="fa-solid fa-circle-info mr-1"></i> {{ method.notes }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            class="flex flex-col h-full border-t md:border-t-0 md:border-l border-gray-100 md:pl-8 pt-6 md:pt-0">
                            <h3 class="text-lg font-semibold text-gray-700 mb-3 border-b pb-2">2. Confirmar depósito
                            </h3>

                            <div v-if="props.total"
                                class="mt-2 mb-6 bg-orange-50 text-orange-800 px-4 py-3 rounded-md border border-orange-200 text-sm text-center">
                                Monto a transferir: <strong class="text-lg block">{{ parsePrices(props.total)
                                }}</strong>
                            </div>

                            <div class="flex flex-col gap-6 items-stretch flex-grow content-start">

                                <div class="flex flex-col">
                                    <label class="text-sm font-semibold text-gray-600 mb-2">Comprobante o
                                        captura</label>
                                    <UploadField id="upload-comprobante" label="" @setImg="handleSetImg"
                                        class="w-full text-sm" />
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">

                <button v-if="currentStep === 1" type="button" @click="emit('close')"
                    class="px-5 py-2.5 rounded-md text-gray-600 font-semibold hover:bg-gray-200 transition-colors">
                    Cancelar
                </button>
                <button v-else type="button" @click="prevStep"
                    class="px-5 py-2.5 rounded-md text-gray-600 font-semibold bg-white border border-gray-300 hover:bg-gray-100 transition-colors">
                    Atrás
                </button>

                <button v-if="currentStep === 1 && paymentType === 'bold'" type="button" @click="nextStep"
                    class="px-6 py-2.5 rounded-md bg-[#1B1B1B] text-white font-semibold hover:bg-[#333333] transition-colors flex items-center gap-2">
                    <font-awesome-icon icon="credit-card" />
                    Pagar en Bold
                </button>

                <button v-else-if="currentStep === 1 && paymentType !== 'bold'" type="button" @click="nextStep"
                    class="px-6 py-2.5 rounded-md bg-[#FF7539] text-white font-semibold hover:bg-[#E65C20] transition-colors flex items-center gap-2"
                    :class="{ 'opacity-50 cursor-not-allowed': !paymentType }">
                    Continuar
                </button>

                <button v-else-if="currentStep === 2" type="button" @click="confirmUpload" :disabled="isSubmitting"
                    class="px-6 py-2.5 rounded-md text-white font-semibold flex items-center gap-2 shadow-sm transition-colors"
                    :class="isSubmitting ? 'bg-[#FF7539] opacity-70 cursor-not-allowed' : 'bg-[#FF7539] hover:bg-[#E65C20]'">
                    <font-awesome-icon v-if="isSubmitting" icon="spinner" class="fa-spin" />
                    <font-awesome-icon v-else icon="check" />
                    {{ isSubmitting ? 'Registrando...' : 'Registrar Depósito' }}
                </button>
            </div>

        </div>
    </div>
</template>

<style scoped>
input[type=number]::-webkit-inner-spin-button,
input[type=number]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

input[type=number] {
    -moz-appearance: textfield;
}
</style>
