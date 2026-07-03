<script setup lang="ts">
import { parsePrices } from '@/utils/parsePrices';

defineProps<{
    pay: any;
}>();

defineEmits<{
    (e: 'pay', payload: { id: string | number, total: number }): void;
}>();
</script>

<template>
    <div class="my-6 bg-gradient-to-r from-orange-50 to-orange-100 border border-orange-300 rounded-xl p-6 shadow-lg">
        <h2 class="text-6xl font-semibold text-orange-600 text-center">{{ pay.codigo }}</h2>

        <div class="flex items-center gap-2 mb-4">
            <div class="w-2 h-8 bg-orange-500 rounded-full"></div>
            <h3 class="text-xl font-semibold text-orange-600">Próximo pago</h3>
        </div>

        <div class="flex justify-between items-center py-2 border-b">
            <p class="text-gray-600 font-medium">Fecha de financiación</p>
            <span class="font-bold text-gray-800">{{ pay.fecha_financiacion }}</span>
        </div>

        <div class="flex justify-between items-center py-2 border-b">
            <p class="text-gray-600 font-medium">Cuotas</p>
            <span class="font-bold text-gray-800">{{ pay.cuota_actual }}/{{ pay.total_cuotas }}</span>
        </div>

        <div class="flex justify-between items-center py-2 border-b">
            <p class="text-gray-600 font-medium">Plan</p>
            <span class="font-bold text-gray-800">{{ pay.plan }}</span>
        </div>

        <div class="flex justify-between items-center py-2 border-b">
            <p class="text-gray-600 font-medium">Tipo</p>
            <span class="font-bold text-gray-800">{{ pay.type }}</span>
        </div>

        <div class="flex justify-between items-center py-2 border-b">
            <p class="text-gray-600 font-medium">Fecha del pago</p>
            <span class="font-bold text-gray-800">{{ pay.fecha }}</span>
        </div>

        <div class="flex justify-between items-center py-2 border-b">
            <p class="text-gray-600 font-medium">Monto de la cuota</p>
            <span class="font-bold text-orange-600">
                {{ parsePrices(pay.monto_cuota) }}
            </span>
        </div>

        <div class="flex justify-between items-center py-2 border-b">
            <p class="text-gray-600 font-medium">Financiación total</p>
            <span class="font-bold text-gray-800">{{ parsePrices(pay.total_deuda) }}</span>
        </div>

        <div class="flex justify-between items-center py-2 border-b">
            <p class="text-gray-600 font-medium">Total pagado</p>
            <span class="font-bold text-gray-800">{{ parsePrices(pay.total_pagado) }}</span>
        </div>

        <div class="flex justify-between items-center py-2 border-b">
            <p class="text-gray-600 font-medium">Saldo pendiente</p>
            <span class="font-bold text-gray-800">{{ parsePrices(pay.saldo_pendiente) }}</span>
        </div>


        <div class="flex justify-between items-center py-3 mt-2">
            <p class="text-lg font-semibold text-gray-800">Total a pagar</p>
            <span class="text-xl font-extrabold text-orange-600">
                {{ parsePrices(pay.total) }}
            </span>
        </div>

        <p class="text-gray-700 font-bold animate-pulse-gentle"> {{ pay.descripcion }}</p>

        <div class="mt-6" v-if="pay.pending == false">
            <button @click="$emit('pay', { id: pay.financing_id, total: pay.total })"
                class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 rounded-lg shadow-md transition duration-200">
                Pagar ahora
            </button>
        </div>

        <p v-else class="text-center mt-4 text-md font-semibold text-gray-700">
            Pago pendiente de aprobación
        </p>
    </div>
</template>

<style scoped>
@keyframes pulse-gentle {

    0%,
    100% {
        opacity: 1;
    }

    50% {
        opacity: 0.5;
    }
}

.animate-pulse-gentle {
    animation: pulse-gentle 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
