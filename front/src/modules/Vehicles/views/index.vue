<script setup lang="ts">
import Loader from "@/components/Loader.vue";
import useIndex from "../composables/useIndex";
import CardVehicles from "../components/CardVehicles.vue";
import TablesHeader from "@/components/tablesHeader.vue";
import RouterView from "@/components/RouterView.vue";
import { useAuthStore } from '@/modules/Auth/stores';

const {
  data,
  loaded,
  router,
  loadScroll,
  setSearch,
  deleteVehicle
} = useIndex('1');

const store = useAuthStore()
</script>

<template>
  <div class="px-5">
    <TablesHeader title="Motos" icon="fa-solid fa-motorcycle" 
    :searchActive="true" @setSearch="({ e }) => setSearch(e)"
    :btnCreate="true" @create="router.push('/vehicles/bikes/create')" />

    <RouterView :conditionForView="$route.path.includes('/vehicles/bikes/create') || $route.path.includes('/vehicles/bikes/edit')"/>

    <section class="relative mx-auto mb-4 overflow-auto animate-fade-in">
      <div class="fakeTable md:w-[90%] mx-auto h-[70vh] py-10" @scroll="loadScroll">
        <section class="grid grid-cols-1 gap-5 md:grid-cols-2" v-if="data.rows.length > 0">
            <CardVehicles v-for="row in data.rows" :key="row.id" :showBtnDel="store.authUser?.role_id <= 2" :vehicle="row" 
            @delete="() => deleteVehicle(row.id)"
            @edit="() => $router.push(`/vehicles/bikes/edit/${row.id}`)"/>
        </section>

        <div class="fakeTable-body" v-if="data.rows.length === 0">
          <Loader v-if="loaded" />
          <p v-else>Sin data.</p>
        </div>
      </div>
    </section>
  </div>
</template>
