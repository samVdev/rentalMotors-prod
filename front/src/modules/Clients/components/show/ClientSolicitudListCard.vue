<!-- src/modules/Clients/components/show/ClientSolicitudListCard.vue -->
<script setup lang="ts">
import type { applyInterface } from "@/modules/Application/types/applyType";

defineProps<{ row: applyInterface }>();
</script>

<template>
  <div class="w-full flex flex-col md:flex-row items-center bg-white border border-gray-200 rounded-xl p-4 mb-3 shadow-[0_2px_8px_rgba(0,0,0,0.04)] hover:shadow-md transition-shadow gap-4">
     <div class="flex items-center justify-center w-12 h-12 rounded-[0.8rem] bg-[#FF7539]/10 text-[#FF7539] shrink-0">
       <font-awesome-icon icon="fa-solid fa-file-invoice" class="text-xl" />
     </div>
     
     <div class="flex-1 min-w-0">
         <p class="text-[0.65rem] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Solicitud / Código</p>
         <h3 class="text-sm font-bold text-gray-800 truncate">{{ row.codigo || 'S/C' }}</h3>
     </div>
     
     <div class="flex-1 min-w-0">
         <p class="text-xs text-gray-600 truncate font-semibold">{{ row.vehicle_brand }} {{ row.vehicle_model }}</p>
         <p class="text-[0.65rem] text-gray-500 truncate mt-0.5 uppercase tracking-wide">{{ row.financiacion_type }} / {{ row.plan }}</p>
     </div>

     <div class="flex-1 min-w-0 hidden md:block">
         <p class="text-xs text-gray-600 truncate"><font-awesome-icon icon="fa-solid fa-id-card" class="mr-2 w-3 text-gray-400"/>{{ row.cedula }}</p>
         <p class="text-xs text-gray-600 truncate mt-1"><font-awesome-icon icon="fa-solid fa-phone" class="mr-2 w-3 text-gray-400"/>{{ row.phone }}</p>
     </div>

     <div class="w-32 flex flex-col items-end shrink-0">
         <p class="text-[0.65rem] text-gray-400 font-semibold uppercase tracking-widest mb-1">{{ row.date }}</p>
         <span :class="[
             'px-2.5 py-0.5 rounded-full text-[0.65rem] font-bold uppercase tracking-wide',
             !row.estatus ? 'bg-gray-100 text-gray-600' : 'bg-green-100 text-green-700'
         ]">
             {{ !row.estatus ? 'Inactiva' : 'Activa' }}
         </span>
     </div>
  </div>
</template>
