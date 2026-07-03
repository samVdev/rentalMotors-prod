<script setup lang="ts">
import Loader from "@/components/Loader.vue";
import FormVehicle from "../components/FormVehicle.vue";
import useCreateOrEdit from "../composables/useCreateOrEdit";
import { onMounted } from "vue";

const props = defineProps<{ id?: string }>()

const {
  users,
  vehicle,
  errors,
  sending,
  loading,
  submit,
  getUserMin    
} = useCreateOrEdit(props.id)

onMounted(() => getUserMin())

</script>

<template>
      <div class="overflow-auto h-full">       
        <h1 class="font-bold text-2xl text-gray-700 flex gap-4 items-center justify-normal w-full md:w-auto">{{ $route.path.includes('/edit') ? `Editar` : 'Nuevo'}} vehiculo</h1>
          <FormVehicle
            v-if="!loading"
            class="p-5 rounded"
            @submit='submit'
            :id="props.id"
            :vehicle='vehicle'
            :sending='sending'
            :errors='errors'
            :users="users"
          />
          <Loader v-else/>
      </div>
</template>
