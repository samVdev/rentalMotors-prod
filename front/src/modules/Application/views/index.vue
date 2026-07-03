<script setup lang="ts">
import Loader from "@/components/Loader.vue";
import useIndex from "../composable/useIndex";
import { computed, ref } from "vue";
import CardRecaudos from "../components/CardRecaudos.vue";
import TablesHeader from "@/components/tablesHeader.vue";
import UploadModal from "../components/UploadModal.vue";
import RequirementsModal from "@/modules/Financiacion/components/RequirementsModal.vue";
import ViewFile from "@/components/ViewFile.vue";

const {
  data,
  anex_id,
  loaded,
  route,
  router,
  docFile,
  requirements_id,
  loadScroll,
  changeStatus,
  setSearch,
  submit,
  submitRequirements,
} = useIndex();

const currentType = computed(() => route.query.type ?? "pending");

function setType(type: string) {
  router.push({
    query: { ...route.query, type }
  });
}

const modalClose = () => {
  anex_id.value = 0
  docFile.value = ''
  requirements_id.value = 0
}

</script>

<template>
  <div class="px-5">

    <TablesHeader title="Solicitudes" icon="fa-solid fa-box-archive" :searchActive="true"
      @setSearch="({ e }) => setSearch(e)" :btnCreate="false" />

    <section v-if="anex_id != 0 || docFile != '' || requirements_id != 0"
      class="fixed inset-0 flex items-center justify-center bg-black/50 backdrop-blur-sm z-50" @click.self="modalClose">
      <div class="bg-white rounded-2xl shadow-2xl w-full h-screen md:w-[80%] md:h-[90vh] p-8 animate-fade-in border overflow-auto border-gray-100">

        <UploadModal v-if="anex_id != 0" :precio="data.rows.find(e => e.id == anex_id).precio" @close="anex_id = 0"
          :plan="data.rows.find(e => e.id == anex_id).plan" @submit="submit" />

        <RequirementsModal v-else-if="requirements_id != 0" @close="requirements_id = 0" @submit="submitRequirements" />

        <ViewFile v-else-if="docFile != ''" :path="docFile" />
      </div>
    </section>

    <div class="flex gap-5 justify-between my-6">
      <button @click="setType('pending')" :class="[
        'block w-full px-5 py-2 font-bold rounded-full shadow-md transition-all duration-300',
        currentType === 'pending'
          ? 'bg-[#FF7539] text-white hover:text-[#FF7539] hover:bg-white'
          : 'text-[#FF7539] bg-white hover:bg-[#FF7539] hover:text-white'
      ]">
        Pendientes
      </button>

      <button @click="setType('accept')" :class="[
        'block w-full px-5 py-2 font-bold rounded-full shadow-md transition-all duration-300',
        currentType === 'accept'
          ? 'bg-[#FF7539] text-white hover:text-[#FF7539] hover:bg-white'
          : 'text-[#FF7539] bg-white hover:bg-[#FF7539] hover:text-white'
      ]">
        Completados
      </button>

      <button @click="setType('reject')" :class="[
        'block w-full px-5 py-2 font-bold rounded-full shadow-md transition-all duration-300',
        currentType === 'reject'
          ? 'bg-[#FF7539] text-white hover:text-[#FF7539] hover:bg-white'
          : 'text-[#FF7539] bg-white hover:bg-[#FF7539] hover:text-white'
      ]">
        Rechazados
      </button>
    </div>

    <section class="relative mx-auto my-4 overflow-auto animate-fade-in">
      <div class="fakeTable md:w-[90%] mx-auto h-[70vh] py-10" @scroll="loadScroll">
        <section v-if="data.rows.length > 0">
          <CardRecaudos v-for="row in data.rows" :key="row.id" :row="row"
            @statusChange="({ id, status }) => changeStatus(id, status)" @anexar_doc="() => anex_id = row.id"
            @actualizar_recaudos="(id) => requirements_id = id"
            @document="(prop: string) => docFile = row[prop]" />
        </section>

        <div class="fakeTable-body" v-if="data.rows.length === 0">
          <Loader v-if="loaded" />
          <p v-else>Sin data.</p>
        </div>
      </div>
    </section>
  </div>
</template>
