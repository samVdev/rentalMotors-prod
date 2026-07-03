<script setup lang="ts">
import useIndex from "../composables/useIndex";
import tablesHeader from "@/components/tablesHeader.vue"
import ActionsTable from "@/components/actionsTable.vue";
import Loader from "@/components/Loader.vue";
import RouterView from "@/components/RouterView.vue";

const {
  data,
  router,
  loaded,
  deleteRow,
  setSearch,
  setSort,
  loadScroll
} = useIndex()

</script>

<template>
  <div>
    <tablesHeader title="Lotes" icon="warehouse" :searchActive="true" @setSearch="({ e }) => setSearch(e)"
      :btnCreate="true" @create="router.push('/lotes/create')" />

    <RouterView :conditionForView="$route.path.includes('/create')"/>

    <section className="relative mx-auto my-4 overflow-auto animate-fade-in">

      <div class="fakeTable md:w-[90%] mx-auto h-[70vh]" @scroll="loadScroll">
        <article class="fakeTable-head grid-cols-3">
          <a to="#" class="cursor-pointer" @click.prevent="setSort('name')">
            Nombre
            <font-awesome-icon icon="sort" class="ml-2" />
          </a>
          <a to="#" class="cursor-pointer" @click.prevent="setSort('count')">
            Cantidad de financiados
            <font-awesome-icon icon="sort" class="ml-2" />
          </a>
          <p>Acción</p>
        </article>

        <section v-if="data.rows.length > 0">
          <div v-for="row in data.rows" :key="row.id" class="grid-cols-3 fakeTable-body">
            <p>{{ row.nombre }}</p>
            <p>{{ row.financings_count }}</p>
            <p>
              <ActionsTable :deleteBtn="true" @remove="deleteRow(row.id)" />
            </p>
          </div>
        </section>

        <div class="FadeTR" v-if="data.rows.length === 0">
          <Loader v-if="loaded" />
          <p v-else>Lotes no encontrados.</p>
        </div>
      </div>

    </section>

  </div>
</template>
