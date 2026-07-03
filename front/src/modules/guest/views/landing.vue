<script setup lang="ts">
import HeaderLanding from '@/layouts/HeaderLanding.vue';
import Requeriments from '../components/Requeriments.vue';
import Benefits from '@/layouts/landing/benefits.vue';
import CarrouselInicial from '../components/CarrouselInicial.vue';
import VehiclesCarrousel from '../components/vehiclesCarrousel.vue';

</script>

<template>
    <main class="w-full bg-white">
        <HeaderLanding />

        <section class="relative">
            <article class="absolute inset-0 z-10 px-4 sm:px-8 flex flex-col justify-center text-center">
                <img src="/logo.png" class="z-[100] w-[70vh] md:mt-36 md:mb-16 mx-auto" alt="logo">
                <div class="absolute inset-0 bg-black/60"></div>


                <a to="/requisitos" data-aos="fade-left" href="#requeriments"
                    class="px-10 md:w-[20%] mx-auto mb-[10vh] bg-[#FF7539] hover:bg-white hover:text-[#FF7539] transition-all text-white font-bold py-3 sm:py-4 rounded-3xl text-lg sm:text-xl flex items-center justify-center">
                    Ver requisitos
                    <font-awesome-icon icon="fa-solid fa-arrow-down-long" class="ml-2" />
                </a>

            </article>

            <CarrouselInicial />
        </section>

        <div class="bg-gray-100">

            <div id="benefits" class="bg-[#ff7539] px-0 pt-20 pb-40 mt-10 md:pt-20 text-center text-white">
                <h2 class="text-3xl md:text-5xl font-extrabold text-center ">
                    ¿Por qué financiar con <span class="text-[#000000]">Nuestra Empresa</span>?
                </h2>
                <p class="font-semibold mt-4 text-xl md:text-3xl">Algunos de nuestros beneficios</p>
            </div>

            <Benefits />

            <router-link to="/apply" data-aos="fade-up"
                class="my-16 mx-auto md:w-[30%] bg-[#160e0a] hover:text-[#FF7539] hover:bg-white transition-all text-white font-bold w-full text-center h-[60px] grid place-items-center rounded-3xl">
                <p>Aplicar</p>
            </router-link>

            <section id="Motos" class="py-32 bg-white">
                <div class="max-w-6xl mx-auto px-6 text-center">

                    <h2 class="text-3xl md:text-5xl font-extrabold text-center mb-16">
                        Motos <span class="text-[#FF7539]">Disponibles</span>
                    </h2>

                    <VehiclesCarrousel type="bikes"/>
                </div>
            </section>

            <section id="Carros" class="py-20 bg-gray-100">
                <div class="max-w-6xl mx-auto px-6 text-center">

                    <h2 class="text-3xl md:text-5xl font-extrabold text-center mb-16">
                        Carros <span class="text-[#FF7539]">Disponibles</span>
                    </h2>
                    <VehiclesCarrousel type="cars"/>
                </div>
            </section>
            <Requeriments />
        </div>
        <footer class="bg-gray-900 text-gray-400 text-center py-6">
            <p>© 2025 Rental Motors. Todos los derechos reservados.</p>
        </footer>
    </main>
</template>


<style>
@font-face {
    font-family: "Boldonse";
    src: url("/fonts/ArchivoBlack-Regular.ttf");
}

.font-regular {
    font-family: "Boldonse", system-ui, sans-serif;
    font-weight: 400;
    font-style: normal;
}
</style>