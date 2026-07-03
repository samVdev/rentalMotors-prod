<script setup lang="ts">
defineProps<{
    mante: any;
}>();

defineEmits<{
    (e: 'mantenimientSend', id: string | number): void;
}>();
</script>

<template>
    <div class="ThisDiv bg-gradient-to-r from-red-50 to-red-100 border-l-4 border-red-500 rounded-xl p-6 mb-6 shadow">
        <h3 class="text-lg font-semibold text-red-700 mb-2">Mantenimiento pendiente</h3>

        <div class="flex justify-between items-center py-2 border-b">
            <p class="text-gray-600 font-bold">Fecha</p>
            <span class="font-bold text-red-600">{{ mante.fecha }}</span>
        </div>

        <div class="flex mt-5 items-center gap-2 mb-4">
            <div class="w-2 h-8 bg-red-500 rounded-full"></div>
            <h3 class="text-xl font-bold text-red-600">Descripcion</h3>
        </div>

        <p class="text-gray-700 font-bold animate-pulse-gentle"> {{ mante.descripcion }}</p>

        <hr class="border-b mt-5">

        <div class="mt-6">
            <button @click="$emit('mantenimientSend', mante.id)"
                class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 rounded-lg shadow-md transition duration-200">
                Mantenimiento realizado
            </button>
        </div>
    </div>
</template>

<style scoped>
.ThisDiv {
    animation: swing-top 1s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

@keyframes pulse-gentle {
    0%,
    100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

.animate-pulse-gentle {
    animation: pulse-gentle 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
