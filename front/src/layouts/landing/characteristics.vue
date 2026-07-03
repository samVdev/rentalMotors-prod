<script setup lang="ts">
import CardBenefits from '@/components/cardBenefits.vue';
import MiniTitle from '@/components/miniTitle.vue';

</script>

<template>
    <section id="caracts" class="bg-[#F7F8F9] min-h-[50vh] w-full py-10">
        <MiniTitle text="Funcionalidades principales" class="text-center mx-auto"/>
        
        <div class="text-center my-2 mt-4 px-2">
            <h2 class="font-extrabold w-[100%] md:w-[60%] text-[#333C4A] mx-auto text-2xl md:font-bold md:text-4xl">Todo lo que se necesita para una gestión eficiente</h2>
            <p class="font-semibold mt-4 text-md text-[#333C4A] md:text-lg">Nuestro sistema ofrece todas las herramientas para simplificar la administración del condominio.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 w-[90%] mx-auto mt-10 px-4 2xl:w-[80%]">
            <CardBenefits title="Directorio de Residentes" data-aos="flip-up" subtitle="Accede fácilmente a la información de contacto y gestiona permisos de acceso." icon="users"/>
            <CardBenefits title="Recibos" data-aos="flip-up" subtitle="Visualización de los recibos a pagar al condominio Yutaje" icon="ticket"/>
            <CardBenefits title="Pagos" data-aos="flip-up" subtitle="Registra los pagos de mantenimiento y mantén las finanzas al día." icon="wallet"/>
            <CardBenefits title="Encuestas" data-aos="flip-up" subtitle="Participación en encuestas para toma de decisiones en el condominio Yutaje." icon="clipboard-list"/>
  
        </div>

    </section>
</template>