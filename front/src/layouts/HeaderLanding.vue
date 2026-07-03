<script setup lang="ts">
import { ref } from 'vue';
import { menuLanding } from '@/utils/constantes/menuLanding';

const isMenuOpen = ref(false);

const toggleMenu = () => {
    isMenuOpen.value = !isMenuOpen.value;
};
</script>

<template>
    <header class="fixed bg-white w-[90%] md:w-[80%] top-2 left-[5%]  md:left-[10%] rounded-3xl border border-gray-200 gap-3 z-[1000] flex items-center justify-between h-[8vh] px-4 md:px-10 lg:px-20 2xl:h-[9vh]">
        <a href="/" class="text-[#FF7539] hover:text-[#ff8a58] transition-all block duration-200 font-bold text-2xl py-2">
            <img src="/logo_2.png" alt="Logo" class="w-36 h-36 object-contain">
        </a>

        <nav class="hidden md:flex items-center justify-center w-[80%] gap-12">
            <a v-for="item in menuLanding" :key="item.name" :href="`/${item.to}`" class="text-[#FF7539] hover:text-[#ff8a58] font-semibold transition-all block duration-200 text-lg py-2">
                {{ item.name }}
            </a>
        </nav>

            <router-link to="/login" class="hidden md:grid bg-[#FF7539] hover:bg-[#ff8a58] transition-all text-white font-bold w-full md:w-[4%] text-center h-[60%] place-items-center rounded-full 2xl:w-[3%] 2xl:h-[41%]">
                <font-awesome-icon icon="fa-solid fa-circle-user" />
            </router-link>

        <div class="md:hidden flex items-center">
            <button @click="toggleMenu" class="text-[#FF7539] focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                </svg>
            </button>
        </div>
    </header>

    <!-- Menú móvil desplegable -->
    <nav v-if="isMenuOpen" class="fixed top-0 left-0 w-full h-full bg-white z-[1000] flex flex-col items-center justify-center">
        <button @click="toggleMenu" class="absolute top-4 right-4 text-[#FF7539]">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        <div class="flex flex-col items-center w-full">
            <a v-for="item in menuLanding" @click="toggleMenu" :key="item.name" :href="item.to" class="text-[#FF7539] hover:text-[#ff8a58] transition-all block duration-200 font-normal text-xl py-2">
                {{ item.name }}
            </a>
            <router-link to="/login" class="bg-[#FF7539] hover:bg-[#ff8a58] transition-all text-white font-bold w-[40%] text-center mt-4 h-[40px] grid place-items-center rounded-3xl">
                <p>Iniciar Sesión</p>
            </router-link>
        </div>
    </nav>
</template>