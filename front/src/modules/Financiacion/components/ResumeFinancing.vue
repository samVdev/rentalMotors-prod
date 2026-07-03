<script setup lang="ts">
import MiniTitle from '@/components/miniTitle.vue';
import CardDash from '@/modules/Auth/components/CardDash.vue';
import { onMounted, ref } from 'vue';
import FinancingService from '../services'
import { parsePrices } from '@/utils/parsePrices';

const data = ref({
  financing: 0,
  pending: 0,
  total: 0,
  total_mora: 0,
  capital: 0
})


const getResume = async () => {
  const response = await FinancingService.resume()
  data.value = response.data
}

onMounted(() => getResume())

</script>

<template>
  <section class="w-full mx-auto">
    <MiniTitle class="mt-5 lg:col-span-2" text="General del financiamiento" />
    <div class="grid gap-6 my-5 sm:grid-cols-2 lg:grid-cols-3">
      <CardDash icon="fa-solid fa-coins" color="bg-blue-100 text-blue-600" :value="`${parsePrices(data.financing)}`"
        label="Total financiado" class="!cursor-default" />
      <CardDash icon="fa-regular fa-calendar-check" color="bg-green-100 text-green-600"
        :value="`${parsePrices(data.total)}`" label="Total pagado" class="!cursor-default" />
      <CardDash icon="fa-solid fa-circle-exclamation" color="bg-red-100 text-red-600"
        :value="`${parsePrices(data.pending)}`" label="Total pendiente" class="!cursor-default" />
      <CardDash icon="fa-solid fa-sack-dollar" color="bg-orange-100 text-orange-600"
        :value="`${parsePrices(data.capital)}`" label="Inversión de capital" class="!cursor-default" />
      <CardDash icon="fa-regular fa-clock" color="bg-orange-100 text-orange-600"
        :value="`${parsePrices(data.total_mora)}`" label="Ingreso por mora" class="!cursor-default" />

    </div>
  </section>
</template>