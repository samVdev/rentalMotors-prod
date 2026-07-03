<script setup lang="ts">
import { onMounted, reactive, ref } from "vue";
import { onBeforeRouteUpdate } from 'vue-router'
import AppPaginationB from "@/components/AppPaginationB.vue";
import tablesHeader from "@/components/tablesHeader.vue";
import useAccountMethodList from "./useAccountMethodList";
import ActionsTable from "@/components/actionsTable.vue";
import Loader from "@/components/Loader.vue";
import RouterView from "@/components/RouterView.vue";

const {
  data,
  loaded,
  route,
  router,
  setSearch,
  setSort,
  loadScroll,
  getAccountMethods,
  deleteRow,
  toggleRow
} = useAccountMethodList()

onBeforeRouteUpdate(async (to, from) => {
  if (to.query !== from.query) {
    // @ts-ignore   
    await getAccountMethods(new URLSearchParams(to.query).toString());
  }
});

onMounted(() => {
  // @ts-ignore
  getAccountMethods(new URLSearchParams(route.query).toString());
});
</script>

<template>
  <div>
    <tablesHeader title="Cuentas" icon="wallet" :searchActive="true" :btnCreate="true"
      @setSearch="({ e }) => setSearch(e)" @create="router.push('/account-methods/form')" />
      

    <RouterView :conditionForView="$route.path.includes('/account-methods/form')" />

    <section class="relative mx-auto mb-4 overflow-auto animate-fade-in panel">
      <div class="fakeTable md:w-[95%] mx-auto h-[70vh] py-10" @scroll="loadScroll">
        
        <article class="fakeTable-head grid-cols-5">
          <a to="#" class="cursor-pointer" @click.prevent="setSort('provider_name')">
            Proveedor
            <font-awesome-icon icon="sort" class="ml-2" />
          </a>
          <a to="#" class="cursor-pointer" @click.prevent="setSort('identifier')">
            Identificador
            <font-awesome-icon icon="sort" class="ml-2" />
          </a>
          <a to="#" class="cursor-pointer" @click.prevent="setSort('type')">
            Tipo
            <font-awesome-icon icon="sort" class="ml-2" />
          </a>
          <a to="#" class="cursor-pointer" @click.prevent="setSort('is_active')">
            Activo
            <font-awesome-icon icon="sort" class="ml-2" />
          </a>
          <p>Acción</p>
        </article>


        <section v-if="data.rows.length > 0">
          <div v-for="row in data.rows" :key="row.id" class="grid-cols-5 fakeTable-body">
            <p>{{ row.provider_name }}</p>
            <p>{{ row.identifier }}</p>
            <p>{{ row.type }}</p>
            <p class="text-center flex justify-center items-center">
              <button @click="toggleRow(row)" :title="row.is_active ? 'Desactivar cuenta' : 'Activar cuenta'"
                class="relative inline-flex items-center h-6 rounded-full w-11 transition-colors focus:outline-none"
                :class="row.is_active ? 'bg-green-500' : 'bg-gray-300'">
                <span :class="row.is_active ? 'translate-x-6' : 'translate-x-1'"
                  class="inline-block w-4 h-4 transform bg-white rounded-full transition-transform duration-200 ease-in-out shadow"></span>
              </button>
            </p>
            <p>
              <ActionsTable 
              :deleteBtn="true" @remove="deleteRow(row.id)" 
              :editBtn="true" @edit="router.push({ path: '/account-methods/form/' + row.id })"
              />
            </p>
          </div>
        </section>
        <div class="FadeTR " v-if="data.rows.length === 0">
          <Loader v-if="loaded" />
          <p v-else class="bg-white fakeTable-body mt-[-10px]">Cuentas no encontradas.</p>
        </div>
      </div>
    </section>
  </div>
</template>
