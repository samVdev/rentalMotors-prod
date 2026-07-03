<script setup lang="ts">
const props = defineProps({
  id: { type: Number, required: true },
  client: { type: String, required: true },
  codigo: { type: String, required: true },
  type: { type: String, required: true },
  installments: { type: Number, required: true },
  startDate: { type: String, required: true },
  responsable: { type: String, required: true },
  lote: { type: String, required: true },
  showBtnDel: { type: Boolean, required: true },
})
</script>

<template>
  <div
    class="w-full bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 border border-gray-200 dark:border-gray-700 hover:shadow-xl transition-all">
    <div class="relative mb-5 p-4 rounded-xl bg-gradient-to-r from-blue-500 to-blue-400 text-white shadow-inner">
      <h2 class="text-2xl font-bold">{{ codigo }}</h2>

      <router-link class="absolute top-5 right-5" title="Editar"
        :to="{ name: 'financing-form', params: { id: props.id } }">
        <font-awesome-icon icon="fa-regular fa-pen-to-square" />
      </router-link>
    </div>

    <div class="space-y-3 text-gray-700 dark:text-gray-300">
      <div class="flex justify-between items-center">
        <span class="font-semibold">Cliente:</span>
        <span class="text-gray-900 dark:text-gray-100">{{ client }}</span>
      </div>
      <div class="flex justify-between items-center">
        <span class="font-semibold">Número de cuotas:</span>
        <span class="text-gray-900 dark:text-gray-100">{{ installments }}</span>
      </div>
      <div class="flex justify-between items-center">
        <span class="font-semibold">Fecha de inicio:</span>
        <span class="text-gray-900 dark:text-gray-100">{{ startDate }}</span>
      </div>
      <div class="flex justify-between items-center">
        <span class="font-semibold">Financiado por:</span>
        <span class="text-gray-900 dark:text-gray-100">{{ responsable }}</span>
      </div>
      <div class="flex justify-between items-center">
        <span class="font-semibold">Lote:</span>
        <span class="text-gray-900 dark:text-gray-100">{{ lote }}</span>
      </div>
    </div>

    <div class="mt-6 flex justify-end gap-3">
      <button @click="$emit('details')"
        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-all">
        Ver detalles
      </button>
      <button v-if="showBtnDel" @click="$emit('delete')"
        class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg text-sm font-medium transition-all">
        Eliminar
      </button>
    </div>
  </div>
</template>
