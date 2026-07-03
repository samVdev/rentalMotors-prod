<script setup lang="ts">
import useIndex from "../composables/useIndex";
import tablesHeader from "@/components/tablesHeader.vue"
import ActionsTable from "@/components/actionsTable.vue";
import Loader from "@/components/Loader.vue";
import RouterView from "@/components/RouterView.vue";
import formatDate from "@/utils/formatDate";

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
    <tablesHeader title="Clientes" icon="fa-solid fa-user-group" :searchActive="true" @setSearch="({ e }) => setSearch(e)"
      :btnCreate="false" @create="router.push('/clients/create')" />

    <RouterView :conditionForView="$route.path.includes('/create') || $route.path.includes('/edit')"/>

    <section className="relative mx-auto my-4 overflow-auto animate-fade-in">

      <div class="fakeTable md:w-[90%] mx-auto h-[70vh]" @scroll="loadScroll">
        <article class="fakeTable-head grid-cols-6">
          <a to="#" class="cursor-pointer" @click.prevent="setSort('name')">
            Nombre
            <font-awesome-icon icon="sort" class="ml-2" />
          </a>
          <a to="#" class="cursor-pointer" @click.prevent="setSort('usuario')">
            Usuario
            <font-awesome-icon icon="sort" class="ml-2" />
          </a>
          <a to="#" class="cursor-pointer" @click.prevent="setSort('cedula')">
            Cédula
            <font-awesome-icon icon="sort" class="ml-2" />
          </a>
          <a to="#" class="cursor-pointer" @click.prevent="setSort('phone')">
            Celular
            <font-awesome-icon icon="sort" class="ml-2" />
          </a>
          <a to="#" class="cursor-pointer" @click.prevent="setSort('dateN')">
            Fecha de Nacimiento
            <font-awesome-icon icon="sort" class="ml-2" />
          </a>
          <p>Acción</p>
        </article>

        <section v-if="data.rows.length > 0">
          <div v-for="row in data.rows" :key="row.uuid" class="grid-cols-6 fakeTable-body">
            <p>{{ row.nombre }}</p>
            <p>{{ row.usuario }}</p>
            <p>{{ row.cedula }}</p>
            <p>{{ row.phone }}</p>
            <p>{{ row.dateN ? formatDate(row.dateN) : 'Sin fecha' }}</p>
            <p>
              <ActionsTable 
              :showBtn="true" @show="router.push({ path: '/clients/show/s/' + row.id })"
              :deleteBtn="true" @remove="deleteRow(row.uuid)" 
              :editBtn="true" @edit="router.push({ path: '/clients/edit/' + row.id })"
              />
            </p>
          </div>
        </section>

        <div class="FadeTR" v-if="data.rows.length === 0">
          <Loader v-if="loaded" />
          <p v-else>Clientes no encontrados.</p>
        </div>
      </div>

    </section>

  </div>
</template>
