<script lang="ts" setup>
import { ref } from 'vue';

const search = ref('');

const emit = defineEmits(
    ["search"]
)

const handleSearchSubmit = () => {
  emit('search', {
    search: search.value
  });
};
</script>

<template>
    <nav class="flex justify-between items-center px-0 md:px-20">
        <!-- Prevenir submit y emitir solo cuando se presiona el botón de búsqueda -->
        <form class="relative w-auto md:w-[50%]" @submit.prevent="handleSearchSubmit">
            <input 
                type="search"
                v-model="search"
                class="block w-full py-2 px-4 text-sm text-gray-900 border border-gray-300 rounded-lg outline-none bg-gray-50 focus:ring-bg-[#FF7539] focus:border-[#FF7539]"
                placeholder="Buscar encuestas"/>
            <button 
                type="submit" 
                class="text-white h-[80%] absolute end-2.5 bottom-1 bg-[#FF7539] hover:bg-[#E65729] focus:ring-4 focus:outline-none font-medium rounded-full text-sm px-2 py-2 dark:hover:bg-[#FF7539]">
                <svg class="w-4 h-full text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                </svg>
            </button>
        </form>
        <router-link to="/surveys/form"
            class="bg-[#FF7539] text-white font-semibold py-2 px-4 rounded-md hover:bg-[#E65729] transition duration-200">
            Nueva Encuesta
        </router-link>
    </nav>
</template>
