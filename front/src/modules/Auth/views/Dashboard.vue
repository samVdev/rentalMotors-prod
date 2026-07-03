<script setup lang="ts">
import CardDash from '@/modules/Auth/components/CardDash.vue';
import useDashboard from '../composables/useDashboard';
import { onMounted } from 'vue';
import FinanCard from '@/components/FinanCard.vue';
import PaymentContainer from '@/modules/Payments/components/PaymentContainer.vue';
import NoInfo from '@/components/noInfo.vue';
import { parsePrices } from '@/utils/parsePrices';
import ResumeMoney from '@/modules/Payments/components/ResumeMoney.vue';
import MiniTitle from '@/components/miniTitle.vue';
import ResumeFinancing from '@/modules/Financiacion/components/ResumeFinancing.vue';

const {
  financings,
  financingsFinishing,
  payments,
  countedData,
  getCountedData,
  getPayments,
  getFinancings
} = useDashboard()

onMounted(() => {
  getCountedData()
  loadFinancingWhitPayments()
})

const loadFinancingWhitPayments = () => {
  getPayments()
  getFinancings()
}

</script>

<template>

  <main id="dashboard" class="mt-[-4vh] pb-10 md:pb-0 relative w-[95%] mx-auto md:w-[90%]">

    <MiniTitle class="my-5" text="General" />
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
      <CardDash icon="fa-solid fa-users" color="bg-orange-100 text-orange-600" :value="countedData.clients"
        label="Total Cliente" @click="() => $router.push('/clients')" />
      <CardDash icon="fa-solid fa-motorcycle" color="bg-blue-100 text-blue-600" :value="countedData.bikes"
        label="Total Motos Financiadas" @click="() => $router.push('/vehicles/bikes')" />
      <CardDash icon="fa-solid fa-wallet" color="bg-purple-100 text-purple-600" :value="countedData.activesPayment"
        label="Financiaciones Activas" @click="() => $router.push('/financing')" />
      <CardDash icon="fa-solid fa-triangle-exclamation" color="bg-pink-100 text-pink-600"
        :value="countedData.manteniment" label="Mantenimientos Pendientes"
        @click="() => $router.push('/maintenance')" />
    </div>

    <hr class="w-[90%] mx-auto my-5">
    <ResumeFinancing />
    <hr class="w-[90%] mx-auto my-5">
    <ResumeMoney />

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 py-6">

      <section class="bg-white rounded-2xl shadow-md p-6 flex flex-col h-[70vh] md:h-[65vh]">
        <h2 class="text-lg font-semibold flex items-center gap-2 text-gray-700 border-b pb-4">
          <span class="text-green-500 text-xl">$</span> Pagos Recientes
        </h2>

        <div class="flex-1 overflow-y-auto mt-4 pr-1 space-y-4 scroll-smooth" v-if="payments.length > 0"
          style="scrollbar-width: thin; scrollbar-color: #ccc transparent;">
          <PaymentContainer :rows="payments" @statusChanged="loadFinancingWhitPayments" />
        </div>

        <NoInfo title="No se encontraron pagos pendientes" icon="fa-solid fa-comments-dollar" v-else />

        <button @click="$router.push('/payments')"
          class="w-full md:w-[50%] mx-auto bg-[#FF7539] hover:bg-[#D54A5C] text-white px-6 py-3 rounded-2xl font-bold shadow-md transition transform hover:scale-[1.02] mt-4">
          Ver más
        </button>
      </section>

      <section class="bg-white rounded-2xl shadow-md p-6 flex flex-col h-[70vh] md:h-[65vh]">
        <h2 class="text-lg font-semibold flex items-center gap-2 text-gray-700 border-b pb-4">
          <span class="text-purple-500 text-xl">▢</span> Financiaciones Activas
        </h2>

        <div class="flex-1 overflow-y-auto mt-4 pr-1 space-y-4 scroll-smooth" v-if="financings.length > 0"
          style="scrollbar-width: thin; scrollbar-color: #ccc transparent;">
          <FinanCard v-for="(fin, i) in financings" :key="i" :fin="fin" />
        </div>

        <NoInfo title="No se encontraron financiaciones activas" icon="fa-solid fa-coins" v-else />


        <button @click="$router.push('/financing')"
          class="w-full md:w-[50%] mx-auto bg-[#FF7539] hover:bg-[#D54A5C] text-white px-6 py-3 rounded-2xl font-bold shadow-md transition transform hover:scale-[1.02] mt-4">
          Ver más
        </button>
      </section>

      <section class="bg-white rounded-2xl shadow-md p-6 flex flex-col h-[70vh] md:h-[65vh]">
        <h2 class="text-lg font-semibold flex items-center gap-2 text-gray-700 border-b pb-4">
          <span class="text-purple-500 text-xl">></span> Financiaciones por salir
        </h2>

        <div class="flex-1 overflow-y-auto mt-4 pr-1 space-y-4 scroll-smooth" v-if="financingsFinishing.length > 0"
          style="scrollbar-width: thin; scrollbar-color: #ccc transparent;">
          <FinanCard v-for="(fin, i) in financingsFinishing" :key="i" :fin="fin" />
        </div>

        <NoInfo title="No se encontraron financiaciones por salir" icon="fa-solid fa-coins" v-else />

        <button @click="$router.push('/financing')"
          class="w-full md:w-[50%] mx-auto bg-[#FF7539] hover:bg-[#D54A5C] text-white px-6 py-3 rounded-2xl font-bold shadow-md transition transform hover:scale-[1.02] mt-4">
          Ver más
        </button>
      </section>

    </div>

  </main>

</template>
