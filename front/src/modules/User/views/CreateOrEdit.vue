<script setup lang="ts">
import FormCreateOrEdit from "../components/FormCreateOrEdit.vue";
import useCreateOrEdit from "../composables/useCreateOrEdit";

const props = defineProps<{ uuid?: string }>()

const {
  user,
  errors,
  roles,
  sending,
  loading,
  submit    
} = useCreateOrEdit(props.uuid)

</script>

<template>
      <div class="overflow-auto h-full">       
        <h1 class="font-bold text-2xl text-gray-700 flex gap-4 items-center justify-normal w-full md:w-auto">{{ $route.path.includes('/edit') ? `Editar` : 'Nuevo'}} usuario</h1>
          <FormCreateOrEdit
            v-if="!loading"
            class="p-5 rounded"
            @submit='submit'
            :uuid="props.uuid"
            :user='user'
            :sending='sending'
            :errors='errors'
            :roles="roles"            
          />
      </div>
</template>
