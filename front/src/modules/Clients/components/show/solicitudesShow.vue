<script setup lang="ts">
import ClientSolicitudListCard from './ClientSolicitudListCard.vue'
import useSoliShow from '../../composables/useSoliShow';

const {
  data,
  loaded,
  loadScroll,
} = useSoliShow();

</script>

<template>
    <section class="relative mx-auto my-2 overflow-auto">
      <div class="md:w-[90%] mx-auto h-[60vh] py-4" @scroll="loadScroll">
        <section class="flex flex-col" v-if="data.rows.length > 0">
          <ClientSolicitudListCard v-for="row in data.rows" :key="row.id" :row="row"/>
        </section>

        <div class="flex flex-col items-center justify-center h-full" v-if="data.rows.length === 0">
          <Loader v-if="loaded" />
          <p v-else class="text-gray-500 font-medium">Sin datos.</p>
        </div>
      </div>
    </section>
</template>