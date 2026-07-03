<script lang="ts" setup>
import type { FinancingInterface } from '@/modules/Financiacion/types/FinancingInterface';

const props = defineProps<{
    fin: FinancingInterface
}>()

</script>

<template>
<div class="bg-white rounded-xl shadow p-4 my-4 border border-gray-100">
  <div class="flex justify-between items-center">
    <h3 class="text-lg font-semibold text-gray-800">{{ fin.monto }}</h3>
  </div>

  <p class="text-xs text-gray-500 mt-2">Cliente: {{ fin.cliente }}</p>

  <div class="mt-3">
    <div class="w-full bg-gray-200 rounded-full h-2">
      <div class="bg-black h-2 rounded-full" :style="{ width: fin.progreso + '%' }"></div>
    </div>
    <div class="flex justify-between text-xs text-gray-500 mt-1">
      <span>{{ fin.progreso }}% completado</span>
      <span>{{ fin.actual }}/{{ fin.total }}</span>
    </div>
  </div>

  <div class="flex justify-between items-center text-xs text-gray-500 mt-2">
    <span>Inicio: {{ fin.fecha_inicio }}</span>
    <span>Restan: {{ fin.total - fin.actual }} cuotas</span>
  </div>
</div>

</template>