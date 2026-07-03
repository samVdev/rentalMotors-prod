<script setup lang="ts">
import TablesHeader from "@/components/tablesHeader.vue";
import Form from "./Form.vue";
import useAccountMethod from "./useAccountMethod";

const props = defineProps<{ id?: string }>()

const {
  method,
  errors,
  sending,
  loading,
  submit,
} = useAccountMethod(props.id)

</script>

<template>
  <div>
  <TablesHeader :title="id && method ? `Editar Método: ${method.provider_name}` : 'Crear nuevo método de cuenta'" icon="wallet" :searchActive="false" :btnCreate="false"/>

    <transition name="fade" mode="out-in">
      <div v-if="!loading && method" class="panel mt-6 p-4">           
        <div class="panel">
          <Form
            class="p-5"
            @submit='submit'
            :id="props.id"            
            :sending='sending'
            :errors='errors'
            :method="method"       
          />
        </div>
      </div>
    </transition>
  </div>
</template>
