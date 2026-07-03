<script setup lang="ts">
import Loader from "@/components/Loader.vue";
import useIndex from "../composable/useIndex";
import { computed } from "vue";
import TablesHeader from "@/components/tablesHeader.vue";
import CardMaintenance from "../components/CardMaintenance.vue";
import RouterView from "@/components/RouterView.vue";
import CompletedMantenece from "../components/CompletedMantenece.vue";
import { useAuthStore } from "@/modules/Auth/stores";

const {
  data,
  loaded,
  route,
  router,
  mantenenceToAccept,
  acceptMantenence,
  loadScroll,
  deleteMantenience,
  setSearch,
  changeStatus
} = useIndex();

const currentType = computed(() => route.query.status ?? "pending");
const store = useAuthStore()

function setType(status: string) {
  router.push({
    query: { ...route.query, status: status }
  });
}

</script>

<template>
  <div class="px-5">

    <TablesHeader title="Mantenimientos" icon="fa-solid fa-hammer" :searchActive="true"
      @setSearch="({ e }) => setSearch(e)" :btnCreate="true" @create="router.push('/maintenance/form')" />

    <RouterView :conditionForView="$route.path.includes('/form')" />

    <CompletedMantenece v-if="mantenenceToAccept != '0'" @close="mantenenceToAccept = '0'" @submit="acceptMantenence"/>

    <div class="flex gap-5 justify-between my-6">

      <button @click="setType('pending')" :class="[
        'block w-full px-5 py-2 font-bold rounded-full shadow-md transition-all duration-300',
        currentType === 'pending'
          ? 'bg-[#FF7539] text-white hover:text-[#FF7539] hover:bg-white'
          : 'text-[#FF7539] bg-white hover:bg-[#FF7539] hover:text-white'
      ]">
        Pendientes
      </button>

      <button @click="setType('checking')" :class="[
        'block w-full px-5 py-2 font-bold rounded-full shadow-md transition-all duration-300',
        currentType === 'checking'
          ? 'bg-[#FF7539] text-white hover:text-[#FF7539] hover:bg-white'
          : 'text-[#FF7539] bg-white hover:bg-[#FF7539] hover:text-white'
      ]">
        Por revisar
      </button>

      <button @click="setType('completed')" :class="[
        'block w-full px-5 py-2 font-bold rounded-full shadow-md transition-all duration-300',
        currentType === 'completed'
          ? 'bg-[#FF7539] text-white hover:text-[#FF7539] hover:bg-white'
          : 'text-[#FF7539] bg-white hover:bg-[#FF7539] hover:text-white'
      ]">
        Completados
      </button>
    </div>

    <section class="relative mx-auto my-4 overflow-auto animate-fade-in">
      <div class="fakeTable md:w-[90%] mx-auto h-[70vh] py-10" @scroll="loadScroll">
        <section v-if="data.rows.length > 0">
          <CardMaintenance v-for="row in data.rows" :key="row.id" :row="row" :showBtnDel="store.authUser?.role_id <= 2" 
            @edit="$router.push({ name: 'maintenance-form', params: { id: row.id, cedula: row.cedula } })"
            @delete="() => deleteMantenience(row.id)" @toggle-status="changeStatus(row.id, row.status)" />
        </section>

        <div class="fakeTable-body" v-if="data.rows.length === 0">
          <Loader v-if="loaded" />
          <p v-else>Sin data.</p>
        </div>
      </div>
    </section>
  </div>
</template>
