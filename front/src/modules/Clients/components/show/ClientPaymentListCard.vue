<!-- src/modules/Clients/components/show/ClientPaymentListCard.vue -->
<script setup lang="ts">
import { parsePrices } from '@/utils/parsePrices';
import type { PagoInterface } from '@/modules/Payments/types/PagoType';

defineProps<{ pago: PagoInterface }>()
defineEmits(['statusChange', 'show', 'document'])
</script>

<template>
    <div
        class="w-full flex flex-col md:flex-row items-center bg-white border border-gray-200 rounded-xl p-4 mb-3 shadow-[0_2px_8px_rgba(0,0,0,0.04)] hover:shadow-md transition-shadow gap-4">
        <div class="flex items-center justify-center w-12 h-12 rounded-[0.8rem] bg-green-50 text-green-500 shrink-0">
            <font-awesome-icon icon="fa-solid fa-money-bill-wave" class="text-xl" />
        </div>

        <div class="flex-1 min-w-[120px]">
            <p class="text-[0.65rem] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Referencia / Cód.</p>
            <h3 class="text-sm font-bold text-gray-800 truncate">{{ pago.codigo || 'S/C' }}</h3>
        </div>

        <div class="flex-1 min-w-[120px]">
            <p class="text-[0.65rem] text-gray-500 uppercase tracking-widest font-bold mb-0.5">Cuota: <span
                    class="text-gray-900">{{ pago.cuota == 'Cuota #0' ? 'Inicial' : pago.cuota }}</span></p>
            <p class="text-xs text-gray-600 truncate mt-1">Lote: {{ pago.lote }}</p>
            <p v-if="pago.cuenta_destino" class="text-[0.65rem] text-gray-500 truncate mt-0.5">Banco: {{
                pago.cuenta_destino }}</p>
        </div>

        <div class="flex-1 min-w-[100px] flex flex-col items-start md:items-center">
            <p class="text-xs text-gray-400 mb-0.5"><font-awesome-icon icon="fa-solid fa-calendar" class="mr-1" />{{
                pago.fecha }}</p>
            <p class="text-lg font-black text-gray-900 flex items-center gap-1">
                {{ pago.monto ? parsePrices(typeof pago.monto === 'string' ? parseFloat(pago.monto) : pago.monto) :
                    '-' }}
            </p>
            <p v-if="pago.mora && pago.mora > 0"
                class="text-[0.65rem] text-red-500 font-bold mt-0.5 uppercase tracking-wider">+{{ pago.mora }}% mora</p>
        </div>

        <div class="flex items-center justify-end min-w-[140px] gap-2">
            <span :class="[
                'px-2.5 py-0.5 rounded-full text-[0.65rem] font-bold uppercase mr-2 tracking-wide',
                pago.status === 'pending' ? 'bg-yellow-100 text-yellow-700' :
                    pago.status === 'approved' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'
            ]">
                {{ pago.status === 'pending' ? 'Pendiente' : pago.status === 'approved' ? 'Aprobado' : 'Rechazado' }}
            </span>

            <!-- Action Buttons -->
            <button @click="$emit('show')" title="Ver financiación"
                class="w-8 h-8 rounded-lg border border-gray-200 bg-gray-50 text-gray-600 hover:bg-gray-100 flex items-center justify-center transition">
                <font-awesome-icon icon="fa-solid fa-file-invoice-dollar" class="text-xs" />
            </button>

            <button v-if="pago.file" @click="$emit('document', pago.file)" title="Ver captura"
                class="w-8 h-8 rounded-lg border border-blue-100 bg-blue-50 text-blue-600 hover:bg-blue-100 flex items-center justify-center transition">
                <font-awesome-icon icon="fa-regular fa-image" class="text-xs" />
            </button>

            <template v-if="pago.status === 'pending' || pago.status === 'rejected'">
                <button @click="$emit('statusChange', { id: pago.id, status: true })" title="Aceptar pago"
                    class="w-8 h-8 rounded-lg border border-green-100 bg-green-50 text-green-600 hover:bg-green-100 flex items-center justify-center transition">
                    <font-awesome-icon icon="fa-solid fa-check" class="text-xs" />
                </button>
            </template>

            <template v-if="pago.status === 'pending' || pago.status === 'approved'">
                <button @click="$emit('statusChange', { id: pago.id, status: false })" title="Rechazar pago"
                    class="w-8 h-8 rounded-lg border border-red-100 bg-red-50 text-red-600 hover:bg-red-100 flex items-center justify-center transition">
                    <font-awesome-icon icon="fa-solid fa-xmark" class="text-xs" />
                </button>
            </template>
        </div>
    </div>
</template>
