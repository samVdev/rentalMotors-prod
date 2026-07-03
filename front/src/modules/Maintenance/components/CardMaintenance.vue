<script setup lang="ts">
import { computed } from 'vue'
import type MaintenanceInterface from '../types/MaintenanceInterface'
import { parsePrices } from '@/utils/parsePrices';

const props = defineProps<{
  row: MaintenanceInterface,
  showBtnDel: boolean
}>()

const statusColor = computed(() => {
  return {
    pending: 'bg-yellow-400',
    completed: 'bg-green-500'
  }[props.row.status?.toLowerCase()] || 'bg-gray-400'
})

const statusText = computed(() => {
  return props.row.status || 'Desconocido'
})

const formattedDate = computed(() => props.row.date || 'Sin fecha')
</script>

<template>
  <div class="relative flex bg-white border border-gray-200 rounded-2xl overflow-hidden mb-6">

    <div :class="['w-2', statusColor]" />

    <div class="flex-1 p-6">

      <div class="flex justify-between items-start mb-4">
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-400">
            Mantenimiento
          </p>
          <h2 class="text-xl font-bold text-gray-900">
            {{ row.client || 'Cliente no identificado' }}
          </h2>
        </div>

        <span class="text-xs font-semibold px-3 py-1 rounded-full bg-gray-100 text-gray-700">
          {{ statusText }}
        </span>
      </div>

      <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">

        <div>
          <p class="text-gray-400">Vehículo</p>
          <p class="font-medium text-gray-800">
            {{ row.vehicle }}
          </p>
        </div>

        <div>
          <p class="text-gray-400">Cédula</p>
          <p class="font-medium text-gray-800">
            {{ row.cedula }}
          </p>
        </div>

        <div>
          <p class="text-gray-400">Fecha</p>
          <p class="font-medium text-gray-800">
            {{ formattedDate }}
          </p>
        </div>

        <div>
          <p class="text-gray-400">Costo</p>
          <p class="font-bold text-gray-900">
            {{ parsePrices(row.cost) }}
          </p>
        </div>

      </div>

      <div class="mt-5 border-t pt-4">
        <p class="text-xs text-gray-400 mb-1">Descripción</p>
        <p class="text-sm text-gray-700 leading-relaxed">
          {{ row.descripcion || 'Sin descripción registrada' }}
        </p>
      </div>

      <div class="flex justify-between items-center mt-6">

        <div class="text-sm text-gray-500">
          📞 {{ row.phone || 'Teléfono no disponible' }}
        </div>

        <div class="flex gap-3">

          <button class="px-4 py-2 text-white"
            v-if="row.status != 'pending'"
            :class="row.status == 'checking' ? 'bg-green-500 hover:bg-green-600' : 'bg-yellow-500 hover:bg-yellow-600'"
            @click="$emit('toggle-status')" title="Cambiar Estado">
            Marcar como "{{ row.status == 'checking' ? 'Completado' : 'Por revisar' }}" &nbsp;
            <font-awesome-icon
              :icon="row.status == 'checking' ? 'fa-solid fa-circle-check' : 'fa-solid fa-circle-xmark'" />
          </button>

          <button  v-if="row.status != 'completed'" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600" @click="$emit('edit')"
            title="Editar">
            Editar &nbsp;
            <font-awesome-icon icon="fa-solid fa-pen-to-square" />
          </button>

          <button v-if="row.status != 'completed' && showBtnDel" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600" @click.stop="$emit('delete')"
            title="Eliminar">
            Eliminar &nbsp;
            <font-awesome-icon icon="fa-solid fa-trash-can" />
          </button>
        </div>

      </div>

    </div>
  </div>
</template>