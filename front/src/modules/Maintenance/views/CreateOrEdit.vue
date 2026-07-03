<script setup lang="ts">
import Loader from "@/components/Loader.vue";
import FormIndex from "../components/form/index.vue";
import useForm from "../composable/useForm";

const props = defineProps<{ id?: string }>()

const {
  data,
  users,
  errors,
  loading,
  sending,
  submit,
} = useForm(props.id)

</script>

<template>
  <div class="overflow-auto h-full">
    <h1 class="font-bold text-2xl text-gray-700 flex gap-4 items-center justify-normal w-full md:w-auto">
      {{ props.id ? `Editar` : 'Nuevo' }} mantenimiento
    </h1>
    <FormIndex v-if="!loading" class="p-5 rounded" @submit='submit' :users="users" :id="props.id" :data='data' :sending='sending'
      :errors='errors' />

      <Loader v-else/>
  </div>
</template>
