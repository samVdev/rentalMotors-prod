<script lang="ts" setup>
import { ref, defineEmits, onMounted } from 'vue'
import * as MenuService from "@/modules/Authorization/services/MenuService"
import { alertWithToast } from '@/utils/toast';
import type { Menu } from '@/types/Menu';

const emit = defineEmits(['closeModal', 'saved']);

const form = ref({
  title: '',
  path: 'dashboard',
  icon: 'fas fa-home',
});

const props = defineProps<{
  menuToEdit: Menu
}>()

const closeModal = () => {
  emit('closeModal');
};

const store = async () => {
  try {
    const response = await MenuService.insertMenu(form.value);
    return response.data.message
  } catch (error) {
    throw error
  }
};

const edit = async () => {
  try {
    const response = await MenuService.updateMenu(props.menuToEdit.id, form.value)
    return response.data.message
  } catch (error) {
    throw error
  }
}

const submit = async () => {
  try {
    const message = props.menuToEdit.id ? await edit() : await store()
    alertWithToast(message, 'success')
    emit('saved');
  } catch (error) {
    const message = error.response.data.message || 'Error inesperado';
    alertWithToast(message, 'error')
  }
}

onMounted(() => {
  if (props.menuToEdit.id) {
    form.value = props.menuToEdit
  }
})

</script>

<template>
  <form @submit.prevent="submit" class="bg-white p-5 relative w-full md:w-[30%]">
    <label class="absolute top-3 right-5 cursor-pointer text-2xl" @click="closeModal()">x</label>
    <h5 class="font-semibold tracking-tight text-xl">{{ menuToEdit.id ? 'Editar' : 'Crear' }} Menu <strong>({{
      form.title }})</strong></h5>
    <div class="">
      <label class="peer-disabled:cursor-not-allowed peer-disabled:opacity-70 text-sm block my-4 font-medium"
        for="name">Nombre de la ruta</label>
      <input
        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm transition-all-200"
        id="name" name="name" v-model="form.title" placeholder="Ingresa el nombre de la ruta">
    </div>

    <div class="">
      <label class="peer-disabled:cursor-not-allowed peer-disabled:opacity-70 text-sm block my-4 font-medium"
        for="name">URL de la ruta</label>
      <input
        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm transition-all-200"
        id="name" name="name" v-model="form.path" placeholder="Ingresa el url de la ruta">
    </div>

    <div class="">
      <label class="peer-disabled:cursor-not-allowed peer-disabled:opacity-70 text-sm block my-4 font-medium"
        for="name">Icono de la ruta</label>
      <input
        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm transition-all-200"
        id="name" name="name" v-model="form.icon" placeholder="Ingresa el icono de la ruta">
    </div>

    <button type="submit"
      class="inline-flex justify-center mt-5 w-full rounded-md border border-transparent px-4 py-2 bg-[#253041 ] text-base leading-6 font-medium text-white shadow-sm hover:bg-[#a7404e] transition ease-in-out duration-150 sm:text-sm sm:leading-5">
      Actualizar
    </button>
  </form>
</template>
