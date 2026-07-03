<script setup lang="ts">
const props = defineProps({
  id: { type: Number, required: true },
  codigo: { type: String, required: true },
  type: { type: String, required: true },
  installments: { type: Number, required: true },
  startDate: { type: String, required: true },
  responsable: { type: String, required: true },
  lote: { type: String, required: true },
})
</script>

<template>
  <div class="relative overflow-hidden w-full bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-100 dark:border-gray-700 transition-all hover:-translate-y-1 hover:shadow-xl group">
    <!-- Decorative background shape -->
    <div class="absolute -top-10 -right-10 w-32 h-32 bg-[#ff814b] rounded-full opacity-10 group-hover:scale-150 transition-transform duration-500"></div>
    
    <div class="p-6 relative z-10">
      <div class="flex justify-between items-start mb-6 border-b border-gray-100 dark:border-gray-700 pb-4">
        <div>
          <p class="text-xs uppercase tracking-wider text-gray-400 font-bold mb-1">Código de Financiación</p>
          <h3 class="text-2xl font-black text-gray-800 dark:text-white">{{ codigo }}</h3>
        </div>
        <div class="bg-[#ff814b]/10 text-[#ff814b] px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-wide">
          {{ type }}
        </div>
      </div>

      <div class="grid grid-cols-2 gap-y-5 gap-x-4 mb-6 text-sm">
        <div class="flex space-x-3 items-center">
          <div class="w-8 h-8 rounded-full bg-orange-50 dark:bg-gray-700 flex items-center justify-center text-[#ff814b]">
            <font-awesome-icon icon="fa-regular fa-calendar" />
          </div>
          <div class="flex flex-col">
            <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">Inicio</span>
            <span class="font-semibold text-gray-800 dark:text-gray-200">{{ startDate }}</span>
          </div>
        </div>

        <div class="flex space-x-3 items-center">
          <div class="w-8 h-8 rounded-full bg-orange-50 dark:bg-gray-700 flex items-center justify-center text-[#ff814b]">
            <font-awesome-icon icon="fa-solid fa-layer-group" />
          </div>
          <div class="flex flex-col">
            <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">Cuotas</span>
            <span class="font-semibold text-gray-800 dark:text-gray-200">{{ installments }}</span>
          </div>
        </div>

        <div class="flex space-x-3 items-center">
          <div class="w-8 h-8 rounded-full bg-orange-50 dark:bg-gray-700 flex items-center justify-center text-[#ff814b]">
            <font-awesome-icon icon="fa-solid fa-user-tie" />
          </div>
          <div class="flex flex-col">
            <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">Responsable</span>
            <span class="font-semibold text-gray-800 dark:text-gray-200">{{ responsable }}</span>
          </div>
        </div>

        <div class="flex space-x-3 items-center">
          <div class="w-8 h-8 rounded-full bg-orange-50 dark:bg-gray-700 flex items-center justify-center text-[#ff814b]">
            <font-awesome-icon icon="fa-solid fa-map-location-dot" />
          </div>
          <div class="flex flex-col">
            <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">Lote</span>
            <span class="font-semibold text-gray-800 dark:text-gray-200">{{ lote }}</span>
          </div>
        </div>
      </div>

      <button @click="$emit('details')" class="w-full flex items-center justify-center gap-2 bg-gray-50 hover:bg-[#ff814b] shadow-sm text-[#ff814b] hover:text-white dark:bg-gray-700 dark:hover:bg-[#ff814b] font-semibold py-3 px-4 rounded-xl transition-all duration-300">
        <span>Ver Detalles Completos</span>
        <font-awesome-icon icon="fa-solid fa-arrow-right" class="text-sm" />
      </button>
    </div>
  </div>
</template>
