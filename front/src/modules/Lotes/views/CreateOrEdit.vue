<script setup lang="ts">
import FormCreateOrEdit from "../components/FormCreateOrEdit.vue";
import useCreateOrEdit from "../composables/useCreateOrEdit";

const {
  data,
  errors,
  sending,
  submit    
} = useCreateOrEdit()

</script>

<template>
      <div class="overflow-auto h-[full]">       
        <h1 class="font-bold text-2xl text-gray-700 flex gap-4 items-center justify-normal w-full md:w-auto">Nuevo lote</h1>
          <FormCreateOrEdit
            class="p-5 rounded"
            @submit='submit'
            :data='data'
            :sending='sending'
            :errors='errors'
          />
      </div>
</template>
