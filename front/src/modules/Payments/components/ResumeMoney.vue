<script setup lang="ts">
import MiniTitle from '@/components/miniTitle.vue';
import CardDash from '@/modules/Auth/components/CardDash.vue';
import { onMounted, ref } from 'vue';
import PaymentsService from '../services'
import { parsePrices } from '@/utils/parsePrices';

const data = ref({
  daily: 0,
  week: 0,
  quince: 0,
  month: 0,
})


const getResume = async () => {
  const response = await PaymentsService.resume()
  data.value = response.data
}

onMounted(() => getResume())

</script>

<template>
  <section class="w-full mx-auto">
    <MiniTitle class="mt-5 lg:col-span-2" text="Ingresos"/>
    
    <div class="w-full mx-auto grid gap-6 my-5 sm:grid-cols-2 lg:grid-cols-4">
      <CardDash icon="fa-solid fa-money-bill-transfer" color="bg-orange-100 text-orange-600" :value="`${parsePrices(data.daily)}`"
        label="Ingresos hoy" class="!cursor-default"/>
      <CardDash icon="fa-solid fa-money-bill-1-wave" color="bg-blue-100 text-blue-600" :value="`${parsePrices(data.week)}`"
        label="Ingresos semanal" class="!cursor-default"/>
      <CardDash icon="fa-solid fa-dollar-sign" color="bg-green-100 text-green-600" :value="`${parsePrices(data.quince)}`"
        label="Ingresos ultimos 15 dias" class="!cursor-default"/>
      <CardDash icon="fa-solid fa-money-bill-trend-up" color="bg-red-100 text-red-600" :value="`${parsePrices(data.month)}`"
        label="Ingresos del mes" class="!cursor-default"/>
    </div>
  </section>
</template>