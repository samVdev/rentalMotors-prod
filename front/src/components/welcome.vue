<template>
        <div id="Inicio" class="px-10 pt-[10vh]">
            <section id="bgBanner"
                class="relative bg-black px-6 pt-[10vh]  md:min-h-[90vh] text-center rounded-3xl overflow-hidden grid place-items-center md:block">
                <div class="absolute inset-0 bg-black/40"></div>

                <div class="md:mt-[0vh] md:mb-0 2xl:mt-[10vh]">
                    <h1 class="relative text-3xl text-[#FF7539] md:text-4xl font-extrabold" data-aos="fade-down" >CUMPLE
                        TU SUEÑO DE TENER TU</h1>
                    <h2 class="relative text-[8vw] text-[#ffffff]  md:text-[6vw] font-extrabold" data-aos="fade-up">
                        PROPIO VEHICULO
                    </h2>
                </div>

                <img src="/logo.png" data-aos="zoom-in" alt="carro"
                    class="w-full object-contain relative bottom-0 left-0 z-[100] pointer-events-none h-[40vh] md:h-[40vh]" />

                <slot></slot>
            </section>
        </div>
</template>

<style scoped>
#bgBanner {
    background-image: url('/bg.jpg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
}
</style>