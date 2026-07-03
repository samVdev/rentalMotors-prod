<script setup lang="ts">
import ClientInfo from '../components/show/ClientInfo.vue';
import HeaderShow from '../components/show/headerShow.vue';
import SolicitudesShow from '../components/show/solicitudesShow.vue';
import FinanciamientosShow from '../components/show/financiamientosShow.vue';
import PagosShow from '../components/show/pagosShow.vue';
import MantenimientoShow from '../components/show/mantenimientoShow.vue';
import imageBg from '../../../../public/bg.svg'

</script>

<template>
    <main
        class="w-full relative bg-gray-50 dark:bg-gray-900 md:grid md:grid-cols-4 md:h-screen overflow-hidden font-sans">
        <img :src="imageBg"
            class="absolute inset-0 w-full h-full object-cover opacity-5 mix-blend-overlay pointer-events-none" />

        <button @click="$router.push('/clients')" title="Volver a clientes"
            class="absolute top-6 right-8 w-12 h-12 flex items-center justify-center rounded-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-500 hover:text-[#ff814b] shadow-sm z-[100]">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path>
            </svg>
        </button>

        <ClientInfo class="shadow-xl relative" />

        <section class="relative col-span-3 flex flex-col h-full z-[10] overflow-hidden">
            <HeaderShow />
            <div
                class="flex-1 w-full px-4 md:px-8 pt-[12vh] md:pt-[9vh] pb-6 overflow-y-auto custom-scrollbar relative">
                <div :key="$route.fullPath" class="w-full h-full">
                    <SolicitudesShow v-if="$route.params.type == 's'" />
                    <FinanciamientosShow v-else-if="$route.params.type == 'f'" />
                    <PagosShow v-else-if="$route.params.type == 'p'" />
                    <MantenimientoShow v-else-if="$route.params.type == 'm'" />
                </div>
            </div>
        </section>
    </main>
</template>