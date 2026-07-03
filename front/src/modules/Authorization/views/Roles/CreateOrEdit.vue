<script setup lang="ts">
import TablesHeader from "@/components/tablesHeader.vue";
import Form from "./Form.vue";
import useRole from "./useRole";

const props = defineProps<{ id?: string }>()

const {
  role,
  errors,
  sending,
  loading,
  menus,
  submit,
} = useRole(props.id)
</script>

<template>
  <div>
  <TablesHeader :title="id && role ? `Editar el rol ${role.name}` : 'Crear nuevo rol'" icon="hand-holding-droplet" :searchActive="false" :btnCreate="false"/>

    <transition name="fade" mode="out-in">
      <div v-if="!loading && role" class="panel mt-6 p-4">           
        <div class="panel mt-6">
          <Form
            class="p-5"
            @submit='submit'
            :id="props.id"            
            :sending='sending'
            :loading='false'
            :errors='errors'
            :menus="menus"
            :role="role"       
          />
        </div>
      </div>
    </transition>
  </div>
</template>
