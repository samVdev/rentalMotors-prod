<script setup lang="ts">
import "vue3-carousel/dist/carousel.css";
import { Carousel, Slide } from "vue3-carousel";
import CardItem from "./CardItem.vue";
import useCarrouselVehicles from "../composable/useCarrouselVehicles";
import { onMounted, watch, ref } from "vue";
import NotRecords from "@/components/notRecords.vue";
import type { vehicleMinType } from "../types/vehicleMinType";
import VehicleDetailModal from "./VehicleDetailModal.vue";

const props = defineProps<{
  bntAdd?: boolean;
  type: "bikes" | "cars";
}>();

const {
  config,
  carousel,
  vehicles,
  showArrows,
  search,
  getVehicles,
  goPrev,
  goNext,
  searchVehicles,
} = useCarrouselVehicles();

const url = import.meta.env.VITE_APP_API_URL;

const selectedVehicleId = ref<string | null>(null)

const openModal = (vehicle: vehicleMinType) => {
  selectedVehicleId.value = vehicle.id
}

const closeModal = () => {
  selectedVehicleId.value = null
}

onMounted(() => {
  getVehicles(props.type);
});

watch(() => props.type, (newType) => getVehicles(newType));
</script>

<template>
  <section class="flex flex-col gap-6 items-center w-full">

    <div class="relative w-full max-w-lg mx-auto mb-8">
      <div class="backdrop-blur-md bg-white/60 dark:bg-gray-800/40 border border-gray-200 dark:border-gray-700
               rounded-3xl shadow-lg flex items-center gap-3 px-4 py-2 transition-all
               focus-within:ring-2 focus-within:ring-orange-400">
        <i class="fa-solid fa-magnifying-glass text-gray-400 group-focus-within:text-orange-500 transition-colors"></i>
        <input v-model="search" type="text" placeholder="Buscar vehículo o marca..." @keyup.enter="searchVehicles"
          class="flex-1 bg-transparent text-gray-700 dark:text-gray-100 placeholder-gray-400
         focus:outline-none text-base no-border" />

        <button @click="searchVehicles" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-1.5 rounded-full text-sm
                 font-medium transition-all shadow-sm hover:shadow-md">
          <font-awesome-icon icon="fa-solid fa-magnifying-glass" />
        </button>
      </div>
    </div>

    <section class="relative w-full max-w-6xl" v-if="vehicles.length > 0">

      <Carousel ref="carousel" v-bind="config">
        <Slide v-for="(card, i) in vehicles" :key="i" class="flex justify-center px-3">
          <CardItem :image="`${url}/${card.image}`" :title="card.title" :description="card.description" :price="card.price" :bntAdd="bntAdd"
            @add="$emit('select', { element: card })" @details="openModal(card)" />
        </Slide>
      </Carousel>

      <button v-if="showArrows" @click="goPrev" class="absolute left-0 top-1/2 -translate-y-1/2 z-[20]
               bg-white dark:bg-gray-800/90 border border-gray-200 dark:border-gray-700
               hover:bg-orange-500 hover:text-white
               text-gray-600 rounded-full w-12 h-12 flex items-center justify-center
               shadow-lg transition-all duration-300 hover:scale-105">
        <i class="fa-solid fa-chevron-left text-lg"></i>
      </button>

      <button v-if="showArrows" @click="goNext" class="absolute right-0 top-1/2 -translate-y-1/2 z-[20]
               bg-white dark:bg-gray-800/90 border border-gray-200 dark:border-gray-700
               hover:bg-orange-500 hover:text-white
               text-gray-600 rounded-full w-12 h-12 flex items-center justify-center
               shadow-lg transition-all duration-300 hover:scale-105">
        <i class="fa-solid fa-chevron-right text-lg"></i>
      </button>
    </section>

    <NotRecords v-else icon="fa-solid fa-car" text="Sin vehículos para financiar" class="scale-[0.9] py-10" />

    <VehicleDetailModal v-if="selectedVehicleId" :vehicle-id="selectedVehicleId" @close="closeModal" />
  </section>
</template>

<style scoped>
section {
  transition: all 0.3s ease;
}

input::placeholder {
  transition: color 0.3s;
}

input:focus::placeholder {
  color: transparent;
}

.no-border {
  border: none !important;
  box-shadow: none !important;
}
</style>
