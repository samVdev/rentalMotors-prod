<script setup lang="ts">
import type { VehicleType } from "../types/vehicleType"

const props = defineProps<{
  vehicle: VehicleType,
  showBtnDel: boolean
}>()

const emit = defineEmits<{
  (e: 'edit', vehicle: VehicleType): void
  (e: 'delete', vehicle: VehicleType): void
}>()

const handleEdit = () => emit('edit', props.vehicle)
const handleDelete = () => emit('delete', props.vehicle)
</script>

<template>
  <div class="bg-white rounded-2xl shadow-md flex flex-col md:flex-row p-4 gap-4 relative">
    <div class="w-full md:w-1/3 flex items-center justify-center">
      <img :src="vehicle.image" :alt="vehicle.modelo" class="w-full h-40 object-contain" />
    </div>

    <div class="flex-1 flex flex-col justify-between">
      <div>
        <h2 class="text-lg font-semibold text-gray-900">
          {{ vehicle.marca }} {{ vehicle.modelo }}
        </h2>
        <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600 mt-2">
          <span>{{ vehicle.year }}</span>
          <span>Cilindrada: {{ vehicle.cc }}</span>
          <span>Kms: {{ vehicle.kilometraje.toLocaleString() }}</span>
          <span>Color: {{ vehicle.color }}</span>
        </div>
      </div>

      <div class="mt-4 flex items-center justify-between">
        <p class="text-xl font-bold text-indigo-600">
          $ {{ Number(vehicle.precio).toLocaleString() }}
        </p>

        <div class="flex gap-2">
          <button 
            @click="handleEdit"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
          >
          <font-awesome-icon icon="fa-solid fa-pen-to-square" />
          </button>
          <button v-if="showBtnDel"
            @click="handleDelete"
            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition"
          >
          <font-awesome-icon icon="fa-solid fa-trash" />
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
