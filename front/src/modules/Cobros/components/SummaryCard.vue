<script setup lang="ts">
defineProps<{
    card: {
        title: string;
        paid: number;
        total: number;
        pending: number;
    },
    active?: boolean
}>();

defineEmits(['click']);
</script>

<template>
    <div @click="$emit('click')" :class="active ? 'ring-2 ring-[#FF7539] shadow-lg scale-105' : 'hover:shadow-xl'"
        class="relative bg-white group overflow-hidden rounded-3xl border border-gray-100 shadow-sm transition-all duration-300 p-6 cursor-pointer">
        <div class="relative z-10">
            <span class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1 block">{{ card.title }}</span>
            <div class="flex items-baseline gap-2 mb-4">
                <h3 class="text-4xl font-black text-gray-900">{{ card.paid }}</h3>
                <span class="text-gray-400 font-medium">/ {{ card.total }}</span>
            </div>

            <div class="pt-4 border-t border-gray-50 flex items-center justify-between">
                <div class="flex flex-col">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">Total Clientes</span>
                    <span class="text-sm font-bold text-gray-600">{{ card.total }}</span>
                </div>

                <div v-if="card.pending > 0" class="flex flex-col items-end">
                    <span
                        class="text-[10px] font-bold text-red-500 uppercase tracking-tighter animate-pulse">Pendientes</span>
                    <span class="text-sm font-black text-red-600 bg-red-50 px-3 py-0.5 rounded-full">{{ card.pending
                        }}</span>
                </div>
                <div v-else class="flex flex-col items-end">
                    <span class="text-[10px] font-bold text-green-500 uppercase tracking-tighter">Al día</span>
                    <font-awesome-icon icon="fa-solid fa-circle-check" class="text-green-500" />
                </div>
            </div>
        </div>
    </div>
</template>
