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
    <tablesHeader title="Usuarios" icon="users" :searchActive="true" @setSearch="({ e }) => setSearch(e)"
      :btnCreate="true" @create="router.push('/users/create')" />

    <RouterView :conditionForView="$route.path.includes('/create') || $route.path.includes('/edit')"/>

    <section className="relative mx-auto my-4 overflow-auto animate-fade-in">

      <div class="fakeTable md:w-[90%] mx-auto h-[70vh]" @scroll="loadScroll">
        <article class="fakeTable-head grid-cols-5">
          <a to="#" class="cursor-pointer" @click.prevent="setSort('name')">
            Nombre
            <font-awesome-icon icon="sort" class="ml-2" />
          </a>
          <a to="#" class="cursor-pointer" @click.prevent="setSort('username')">
            Usuario
            <font-awesome-icon icon="sort" class="ml-2" />
          </a>
          <a to="#" class="cursor-pointer" @click.prevent="setSort('phone')">
            Celular
            <font-awesome-icon icon="sort" class="ml-2" />
          </a>
          <a to="#" class="cursor-pointer" @click.prevent="setSort('rol')">
            Rol
            <font-awesome-icon icon="sort" class="ml-2" />
          </a>
          <p>Acción</p>
        </article>

        <section v-if="data.rows.length > 0">
          <div v-for="row in data.rows" :key="row.uuid" class="grid-cols-5 fakeTable-body">
            <p>{{ row.nombre }}</p>
            <p>{{ row.usuario }}</p>
            <p>{{ row.phone }}</p>
            <p>{{ row.rol }}</p>
            <p>
              <ActionsTable :deleteBtn="true" :editBtn="true" @edit="router.push({ path: '/users/edit/' + row.uuid })"
                @remove="deleteRow(row.uuid)" />
            </p>
          </div>
        </section>

        <div class="FadeTR" v-if="data.rows.length === 0">
          <Loader v-if="loaded" />
          <p v-else>Usuarios no encontrados.</p>
        </div>
      </div>

    </section>

  </div>
</template>
