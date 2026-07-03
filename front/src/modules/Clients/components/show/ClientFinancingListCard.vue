<!-- src/modules/Clients/components/show/ClientFinancingListCard.vue -->
<script setup lang="ts">
defineProps<{
    id: number,
    lote: string,
    responsable: string,
    codigo: string,
    type: string,
    installments: number,
    startDate: string
}>()
</script>

<template>
  <div class="w-full flex flex-col md:flex-row items-center bg-white border border-gray-200 rounded-xl p-4 mb-3 shadow-[0_2px_8px_rgba(0,0,0,0.04)] hover:shadow-md transition-shadow gap-4 cursor-pointer" @click="$emit('details', id)">
     <div class="flex items-center justify-center w-12 h-12 rounded-[0.8rem] bg-blue-50 text-blue-500 shrink-0">
       <font-awesome-icon icon="fa-solid fa-handshake" class="text-xl" />
     </div>
     
     <div class="flex-1 min-w-0">
         <p class="text-[0.65rem] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Financiación</p>
         <h3 class="text-sm font-bold text-gray-800 truncate">{{ codigo || 'S/C' }}</h3>
     </div>
     
     <div class="flex-1 min-w-0">
         <p class="text-xs text-gray-600 truncate font-semibold"><font-awesome-icon icon="fa-solid fa-user-tie" class="mr-2 w-3 text-gray-400"/>{{ responsable }}</p>
         <p class="text-[0.65rem] text-gray-500 truncate mt-1 uppercase"><font-awesome-icon icon="fa-solid fa-layer-group" class="mr-2 w-3 text-gray-400"/>{{ installments }} Cuotas</p>
     </div>

     <div class="flex-1 min-w-0 hidden md:block">
         <p class="text-xs font-semibold text-gray-700 truncate">Lote: {{ lote }}</p>
         <p class="text-[0.65rem] text-gray-500 truncate mt-1 uppercase">{{ type }}</p>
     </div>

     <div class="w-32 flex flex-col items-end shrink-0">
         <p class="text-[0.65rem] text-gray-400 font-semibold mb-1 tracking-widest">{{ startDate }}</p>
         <button class="text-[0.7rem] font-bold text-blue-600 hover:text-blue-800 uppercase tracking-widest" @click.stop="$emit('details', id)">Detalles &rarr;</button>
     </div>
  </div>
</template>
