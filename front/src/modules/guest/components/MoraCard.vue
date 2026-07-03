<script setup lang="ts">
import { parsePrices } from '@/utils/parsePrices';

defineProps<{
    mora: {
        id: number;
        financing_id: number;
        codigo: string;
        total: number;
        reconnection_fee: number;
        mora_amount: number;
        base_amount: number;
        percentage: number;
        index: number;
        status: string;
        has_pending_payment: boolean;
        descripcion: string;
    };
}>();

defineEmits<{
    (e: 'payMora', payload: { id: number, financing_id: number, total: number }): void;
}>();
</script>

<template>
    <div
        class="my-6 bg-gradient-to-r from-red-50 to-red-100 border border-red-300 rounded-xl p-6 shadow-lg overflow-hidden transition-all hover:shadow-xl relative">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-red-200 rounded-full opacity-30 transform rotate-12"></div>

        <h2 class="text-4xl font-black text-red-600 text-center mb-1">{{ mora.codigo }}</h2>
        <p class="text-center text-[10px] uppercase font-black text-red-400 tracking-widest mb-4">Mora Independiente</p>

        <div class="flex items-center gap-2 mb-4 bg-white/50 py-2 px-3 rounded-lg border border-red-200">
            <div class="w-2 h-8 bg-red-500 rounded-full"></div>
            <h3 class="text-lg font-bold text-red-600">Cupón de Mora #{{ mora.index }}</h3>
        </div>

        <div class="flex justify-between items-center py-2 border-b border-red-100">
            <p class="text-gray-600 font-medium text-sm">Monto Cuota Base</p>
            <span class="font-bold text-gray-800 text-sm">{{ parsePrices(mora.base_amount) }}</span>
        </div>

        <div class="flex justify-between items-center py-2 border-b border-red-100">
            <p class="text-gray-600 font-medium text-sm">Recargo Mora ({{ mora.percentage }}%)</p>
            <span class="font-black text-red-600 text-sm">+ {{ parsePrices(mora.mora_amount) }}</span>
        </div>

        <div class="flex justify-between items-center py-2 border-b border-red-100">
            <p class="text-gray-600 font-medium text-sm">Gastos Administrativos</p>
            <span class="font-bold text-gray-800 text-sm">+ {{ parsePrices(mora.reconnection_fee) }}</span>
        </div>

        <div class="flex justify-between items-center py-3 mt-2 border-t-2 border-dashed border-red-200">
            <p class="text-lg font-black text-gray-800">Total a pagar</p>
            <span class="text-2xl font-black text-red-600">
                {{ parsePrices(mora.total) }}
            </span>
        </div>

        <div class="bg-white/40 p-3 rounded-lg mb-6 border border-red-100">
            <p class="text-red-700 text-xs font-bold text-center leading-snug">
                <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                {{ mora.descripcion }}
            </p>
        </div>

        <div class="mt-4">
            <button v-if="!mora.has_pending_payment"
                @click="$emit('payMora', { id: mora.id, financing_id: mora.financing_id, total: mora.total })"
                class="w-full bg-red-600 hover:bg-red-700 text-white font-black py-4 rounded-xl shadow-lg transition-all duration-300 transform active:scale-95 flex items-center justify-center gap-2">
                <font-awesome-icon icon="fa-solid fa-receipt" />
                PAGAR MORA AHORA
            </button>
            <div v-else
                class="flex flex-col items-center justify-center py-4 bg-white/60 rounded-xl border border-red-200 animate-pulse">
                <font-awesome-icon icon="fa-solid fa-clock-rotate-left" class="text-red-500 text-xl mb-2" />
                <p class="text-center text-sm font-black text-red-600 uppercase tracking-tight">
                    Mora pendiente por aceptar
                </p>
                <span class="text-[10px] text-red-400 font-bold mt-1 tracking-widest uppercase">En revisión</span>
            </div>
        </div>
    </div>
</template>