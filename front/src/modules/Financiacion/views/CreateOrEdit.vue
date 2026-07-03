<script setup lang="ts">
import Loader from "@/components/Loader.vue";
import useCreateOrEdit from "../composables/useCreateOrEdit";
import { onMounted } from "vue";
import FormFinancing from "../components/FormFinancing.vue";

const props = defineProps<{ id?: string }>()

const {
  users,
  vehicles,
  financiamiento,
  errors,
  sending,
  loading,
  submit,
  getUserMin,
  getMinVehicle
} = useCreateOrEdit(props.id)

onMounted(() => {

  if (!props.id) {
    getUserMin()
    getMinVehicle()
  }
})

</script>

<template>
  <div class="overflow-auto" :class="props.id ? 'h-[50%]' : 'h-full'">
    <h1 class="font-bold text-2xl text-gray-700 flex gap-4 items-center justify-normal w-full md:w-auto">{{props.id ? 'Editar' : 'Nueva'}} financiación</h1>
    <FormFinancing v-if="!loading" class="p-5 rounded" @submit='submit' :id="props.id" :financiamiento='financiamiento'
    :sending='sending' :errors='errors' :users="users" :vehiculos="vehicles" />
    <Loader v-else />
  </div>
</template>
