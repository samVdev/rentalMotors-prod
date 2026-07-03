<script setup lang="ts">
import Loader from "@/components/Loader.vue";
import useIndex from "../composables/useIndex";
import TablesHeader from "@/components/tablesHeader.vue";
import RouterView from "@/components/RouterView.vue";
import FinancingCard from "../components/FinancingCard.vue";
import Details from "../components/Details.vue";
import useShowFinancing from "../composables/useShowFinancing";
import ResumeFinancing from "../components/ResumeFinancing.vue";
import { useAuthStore } from "@/modules/Auth/stores";

const {
  data,
  loaded,
  router,
  loadScroll,
  setSearch,
  deleteItem,
} = useIndex();

const {
  financingToShow,
  getShowData,
  emptyFinancingDetails
} = useShowFinancing()


const store = useAuthStore()


</script>

<template>
  <div class="px-5">
    <TablesHeader title="Financiaciones" icon="fa-solid fa-coins" :searchActive="true"
      @setSearch="({ e }) => setSearch(e)" :btnCreate="true" @create="router.push('/financing/form')" >
      <div class="w-full mx-auto mt-10 md:w-[90%]">
        <ResumeFinancing/>
      </div>
    </TablesHeader>

    <Details v-if="financingToShow.id" :financing="financingToShow"
      @close="financingToShow = emptyFinancingDetails()" />

    <RouterView :conditionForView="$route.path.includes('/financing/form')" />

    <section class="relative mx-auto mb-4 overflow-auto animate-fade-in">
      <div class="fakeTable md:w-[90%] mx-auto h-[70vh] py-10" @scroll="loadScroll">
        <section class="grid grid-cols-1 gap-5 md:grid-cols-3" v-if="data.rows.length > 0">
          <FinancingCard v-for="row in data.rows" :showBtnDel="store.authUser?.role_id <= 2" :key="row.id + row.cliente" :id="row.id" :lote="row.lote" :client="row.cliente" :responsable="row.responsable" :codigo="row.codigo"
            :type="row.tipo" :installments="row.cuotas" :startDate="row.fecha_inicio" @details="getShowData(row.id)"
            @delete="deleteItem(row.id)" />
        </section>

        <div class="fakeTable-body" v-if="data.rows.length === 0">
          <Loader v-if="loaded" />
          <p v-else>Sin data.</p>
        </div>
      </div>
    </section>
  </div>
</template>
