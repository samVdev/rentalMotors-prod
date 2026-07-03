<script setup lang="ts">
import Loader from "@/components/Loader.vue";
import useIndex from "../composable/useIndex";
import { computed } from "vue";
import TablesHeader from "@/components/tablesHeader.vue";
import PaymentContainer from "../components/PaymentContainer.vue";
import ResumeMoney from "../components/ResumeMoney.vue";

const {
  data,
  loaded,
  route,
  router,
  loadScroll,
  reload,
  setSearch,
} = useIndex();

const currentType = computed(() => route.query.type ?? "pending");

function setType(type: string) {
  router.push({
    query: { ...route.query, type }
  });
}
</script>

<template>
  <div class="px-5">

    <TablesHeader title="Pagos" icon="fa-solid fa-comments-dollar" :searchActive="true"
      @setSearch="({ e }) => setSearch(e)" :btnCreate="false" >
      
      <div class="w-full mx-auto my-10 md:w-[90%]">
        <ResumeMoney/>
      </div>
    </TablesHeader>

    <div class="flex gap-5 justify-between my-6">
      <button @click="setType('pending')" :class="[
        'block w-full px-5 py-2 font-bold rounded-full shadow-md transition-all duration-300',
        currentType === 'pending'
          ? 'bg-[#FF7539] text-white hover:text-[#FF7539] hover:bg-white'
          : 'text-[#FF7539] bg-white hover:bg-[#FF7539] hover:text-white'
      ]">
        Pendientes
      </button>

      <button @click="setType('approved')" :class="[
        'block w-full px-5 py-2 font-bold rounded-full shadow-md transition-all duration-300',
        currentType === 'approved'
          ? 'bg-[#FF7539] text-white hover:text-[#FF7539] hover:bg-white'
          : 'text-[#FF7539] bg-white hover:bg-[#FF7539] hover:text-white'
      ]">
        Completados
      </button>

      <button @click="setType('rejected')" :class="[
        'block w-full px-5 py-2 font-bold rounded-full shadow-md transition-all duration-300',
        currentType === 'rejected'
          ? 'bg-[#FF7539] text-white hover:text-[#FF7539] hover:bg-white'
          : 'text-[#FF7539] bg-white hover:bg-[#FF7539] hover:text-white'
      ]">
        Rechazados
      </button>
    </div>

    <section class="relative mx-auto my-4 overflow-auto animate-fade-in">
      <div class="fakeTable md:w-[90%] mx-auto h-[70vh] py-10" @scroll="loadScroll">
        <section class="w-full mx-auto md:w-[90%] 2xl:w-full">
          <PaymentContainer :rows="data.rows" @statusChanged="({status}) => reload(status)"/>
        </section>

        <div class="fakeTable-body" v-if="data.rows.length === 0">
          <Loader v-if="loaded" />
          <p v-else>Sin data.</p>
        </div>
      </div>
    </section>
  </div>
</template>
