<script setup lang="ts">
import { parsePrices } from '@/utils/parsePrices';
import type { PagoInterface } from '../types/PagoType'

const props = defineProps<{
  pago: PagoInterface
}>()
</script>

<template>
  <div
    class="w-full bg-white border border-gray-200 rounded-2xl shadow-sm hover:shadow-md transition-all duration-200 p-6 mb-2">

    <div class="flex justify-between items-start">
      <div class="space-y-1">
        <div class="flex items-center gap-3">
          <font-awesome-icon icon="fa-solid fa-user" class="text-gray-500" />
          <h3 class="text-lg font-semibold text-gray-900">{{ pago.cliente }}</h3>

          <span v-if="pago.is_mora"
            class="bg-red-100 text-red-600 px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-tighter shadow-sm border border-red-200">
            Mora
          </span>
        </div>

        <div class="flex text-sm text-gray-600 gap-5 ml-6 py-3">
          <p class="flex items-center gap-2">
            <font-awesome-icon icon="fa-solid fa-id-card" class="text-gray-400" />
            <span class="font-medium">Cédula:</span> {{ pago.cedula }}
          </p>
          <p class="flex items-center gap-2">
            <font-awesome-icon icon="fa-solid fa-barcode" class="text-gray-400" />
            <span class="font-medium">Código:</span>
            <span class="font-mono text-gray-700">{{ pago.codigo }}</span>
          </p>
          <p class="flex items-center gap-2">
            <font-awesome-icon icon="fa-solid fa-layer-group" class="text-gray-400" />
            <span class="font-medium">Cuota:</span> {{ pago.cuota }}
          </p>
          <p class="flex items-center gap-2">
            <font-awesome-icon icon="fa-solid fa-layer-group" class="text-gray-400" />
            <span class="font-medium">POS:</span> {{ pago.lote }}
          </p>
          <p v-if="pago.cuenta_destino" class="flex items-center gap-2">
            <font-awesome-icon icon="fa-solid fa-building-columns" class="text-gray-400" />
            <span class="font-medium">Destino:</span> {{ pago.cuenta_destino }}
          </p>
        </div>
      </div>

      <span :class="[
        'px-3 py-1.5 rounded-full text-xs font-semibold uppercase flex items-center gap-1 shadow-sm',
        pago.status === 'pending' && 'bg-yellow-100 text-yellow-800',
        pago.status === 'approved' && 'bg-green-100 text-green-800',
        pago.status === 'rejected' && 'bg-red-100 text-red-800',
      ]">
        <font-awesome-icon :icon="pago.status === 'pending'
          ? 'fa-solid fa-hourglass-half'
          : pago.status === 'approved'
            ? 'fa-solid fa-circle-check'
            : 'fa-solid fa-circle-xmark'" />
        {{ pago.status }}
      </span>
    </div>

    <div class="flex flex-wrap justify-between items-center border-t border-gray-100 pt-4 gap-4">
      <div class="space-y-1">
        <p class="text-3xl font-bold text-gray-900 flex items-center gap-2">
          {{ pago.monto ? parsePrices(typeof pago.monto == 'string' ? parseFloat(pago.monto) : pago.monto) : '-' }}

          <template v-if="pago.is_mora">
            <span class="text-red-500 text-sm font-black bg-red-50 px-2 py-1 rounded border border-red-100">
              +{{ pago.mora_percentage }}%
              <small class="text-[10px] text-red-400 ml-1 font-bold">Cupón #{{ pago.mora_index }}</small>
            </span>
          </template>
          <template v-else-if="pago.mora && pago.mora > 0">
            <small id="colorGreen" class="text-xl">(+ {{ pago.mora }}%)</small>
          </template>
        </p>
        <p class="text-sm text-gray-500 flex items-center gap-2">
          <font-awesome-icon icon="fa-regular fa-calendar" />
          {{ pago.fecha }}
        </p>
      </div>

      <div class="flex flex-wrap gap-2">
        <button @click="$emit('show')" title="Ver financiación"
          class="flex items-center gap-2 border border-gray-300 text-gray-700 px-3 py-1.5 rounded-lg hover:bg-gray-100 transition">
          <font-awesome-icon icon="fa-solid fa-file-invoice-dollar" />
        </button>

        <button v-if="pago.file" @click="$emit('document', pago.file)" target="_blank" title="Ver captura de pago"
          class="flex items-center gap-2 border border-gray-300 text-gray-700 px-3 py-1.5 rounded-lg hover:bg-gray-100 transition">
          <font-awesome-icon icon="fa-regular fa-image" />
        </button>

        <button v-if="pago.status === 'pending' || pago.status === 'rejected'"
          @click="$emit('statusChange', { id: pago.id, status: true })" title="Aceptar pago"
          class="flex items-center gap-2 bg-green-600 text-white px-3 py-1.5 rounded-lg hover:bg-green-700 transition">
          <font-awesome-icon icon="fa-solid fa-check" />
        </button>

        <button v-if="pago.status === 'pending' || pago.status === 'approved'"
          @click="$emit('statusChange', { id: pago.id, status: false })" title="Rechazar pago"
          class="flex items-center gap-2 bg-red-600 text-white px-3 py-1.5 rounded-lg hover:bg-red-700 transition">
          <font-awesome-icon icon="fa-solid fa-xmark" />
        </button>
      </div>
    </div>
  </div>
</template>


<style scoped>
#colorGreen {
  color: rgb(5, 170, 5);
}
</style>