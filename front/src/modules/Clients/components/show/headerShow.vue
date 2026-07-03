<script setup lang="ts">

const links = [
    {
        name: 'solicitudes',
        to: 's',
    },
    {
        name: 'financiamientos',
        to: 'f',
    },
    {
        name: 'pagos',
        to: 'p',
    },
    {
        name: 'Mantenimiento',
        to: 'm',
    }
]
</script>

<template>
    <header class="absolute top-6 left-1/2 transform -translate-x-1/2 w-[95%] md:w-[85%] max-w-5xl z-[60]">
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 shadow-sm rounded-[2rem] p-2 mx-auto">
            <nav class="flex items-center justify-between w-full h-full gap-2 relative">
                <router-link v-for="item in links" :key="item.name" :to="`/clients/show/${item.to}/${$route.params.id}`"
                    class="relative flex-1 text-center py-2.5 px-2 md:px-4 rounded-[1.5rem] font-bold text-sm md:text-base capitalize group overflow-hidden"
                    :class="$route.params.type === item.to ? 'text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800'">
                    <!-- Active Background Pill -->
                    <div v-if="$route.params.type === item.to" class="absolute inset-0 bg-[#FF7539] rounded-[1.5rem] z-0 pointer-events-none"></div>
                    
                    <span class="relative z-10 tracking-wide">{{ item.name }}</span>
                </router-link>
            </nav>
        </div>
    </header>
</template>