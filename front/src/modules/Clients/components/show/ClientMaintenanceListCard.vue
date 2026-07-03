<!-- src/modules/Clients/components/show/ClientMaintenanceListCard.vue -->
<script setup lang="ts">
import type MaintenanceInterface from '@/modules/Maintenance/types/MaintenanceInterface';
import { parsePrices } from '@/utils/parsePrices';

const props = defineProps<{ row: MaintenanceInterface, showBtnDel: boolean }>();
defineEmits(['edit', 'delete', 'toggle-status']);
</script>

<template>
  <div class="w-full relative flex flex-col md:flex-row items-center bg-white border border-gray-200 rounded-xl pl-5 pr-4 py-3 mb-3 shadow-[0_2px_8px_rgba(0,0,0,0.04)] hover:shadow-md transition-shadow gap-4 overflow-hidden">
     <!-- Status Color Bar -->
     <div :class="[
         'absolute left-0 top-0 bottom-0 w-[0.4rem]',
         row.status === 'completed' ? 'bg-green-500' :
         row.status === 'checking' ? 'bg-blue-500' : 'bg-yellow-400'
     ]"></div>

     <div class="flex items-center justify-center w-10 h-10 rounded-[0.8rem] bg-gray-50 border border-gray-100 text-gray-500 shrink-0">
       <font-awesome-icon icon="fa-solid fa-wrench" class="text-lg" />
     </div>
     
     <div class="flex-1 min-w-[140px]">
         <p class="text-[0.65rem] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Vehículo</p>
         <h3 class="text-sm font-bold text-gray-800 truncate">{{ row.vehicle || 'N/A' }}</h3>
     </div>
     
     <div class="flex-1 min-w-[120px] hidden md:block">
         <p class="text-xs font-semibold text-gray-700 truncate"><font-awesome-icon icon="fa-solid fa-calendar" class="mr-2 w-3 text-gray-400"/>{{ row.date }}</p>
         <p class="text-xs text-gray-600 truncate mt-1 font-bold"><font-awesome-icon icon="fa-solid fa-coins" class="mr-2 w-3 text-gray-400"/>{{ row.cost ? parsePrices(row.cost) : '---' }}</p>
     </div>

     <div class="flex-1 min-w-[160px] hidden lg:block">
         <p class="text-[0.65rem] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Descripción</p>
         <p class="text-xs text-gray-500 truncate" :title="row.descripcion">{{ row.descripcion || 'Sin descripción' }}</p>
     </div>

     <div class="flex items-center justify-end min-w-[160px] gap-2">
         <span :class="[
             'px-2.5 py-0.5 rounded-full text-[0.65rem] font-bold uppercase tracking-wide mr-2',
             row.status === 'completed' ? 'bg-green-100 text-green-700' :
             row.status === 'checking' ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-700'
         ]">
             {{ row.status === 'completed' ? 'Listo' : row.status === 'checking' ? 'Por Revisar' : 'Pendiente' }}
         </span>

         <!-- Action Buttons -->
         <button v-if="row.status !== 'pending'" class="w-8 h-8 rounded-lg bg-green-50 text-green-600 hover:bg-green-100 border border-green-100 flex items-center justify-center transition" @click="$emit('toggle-status')" title="Cambiar Estado">
            <font-awesome-icon :icon="row.status === 'checking' ? 'fa-solid fa-check' : 'fa-solid fa-rotate-left'" />
         </button>

         <button v-if="row.status !== 'completed'" class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 border border-blue-100 flex items-center justify-center transition" @click="$emit('edit')" title="Editar">
            <font-awesome-icon icon="fa-solid fa-pen" class="text-xs" />
         </button>

         <button v-if="row.status !== 'completed' && showBtnDel" class="w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 border border-red-100 flex items-center justify-center transition" @click.stop="$emit('delete')" title="Eliminar">
            <font-awesome-icon icon="fa-solid fa-trash" class="text-xs"/>
         </button>
     </div>
  </div>
</template>
