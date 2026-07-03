<script setup lang="ts">
import ActionsTable from "@/components/actionsTable.vue";
import useIndex from "../composables/useIndex";
import Loader from "@/components/Loader.vue";

const {
  data,
  loaded,
  setSort,
  loadScroll
} = useIndex()

</script>

<template>
  <div class="relative overflow-auto md:h-[70%] md:px-4">
    <div class="fakeTable mx-auto h-full" @scroll="loadScroll">
      <article class="fakeTable-head h-[15%] grid-cols-4">
        <a to="#" class="cursor-pointer" @click.prevent="setSort('name')">
          Nombre
          <font-awesome-icon icon="sort" class="ml-2" />
        </a>
        <a to="#" class="cursor-pointer" @click.prevent="setSort('email')">
          Correo
          <font-awesome-icon icon="sort" class="ml-2" />
        </a>
        <a to="#" class="cursor-pointer" @click.prevent="setSort('phone')">
          Celular
          <font-awesome-icon icon="sort" class="ml-2" />
        </a>
        <p>Acción</p>
      </article>

      <section v-if="data.rows.length > 0">
        <div v-for="row in data.rows" :key="row.uuid" class="grid-cols-4 fakeTable-body">
          <p>{{ row.nombre }}</p>
          <p>{{ row.email }}</p>
          <p>{{ row.phone }}</p>
          <p>
            <ActionsTable :recibesBtn="true" @recives="() => $emit('recibesPending', {uuid: row.uuid})" />
          </p>
        </div>
      </section>

      <div class="FadeTR" v-if="data.rows.length === 0">
        <Loader v-if="loaded" />
        <p v-else>Usuarios no encontrados.</p>
      </div>
    </div>

  </div>
</template>
