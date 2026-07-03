<script lang="ts" setup>
 // @ts-nocheck 
import { ref, onMounted } from 'vue';
import * as MenuService from "@/modules/Authorization/services/MenuService";
import tablesHeader from "@/components/tablesHeader.vue";
import ActionsTable from '@/components/actionsTable.vue';
import { questionSweet } from '@/utils/question';
import Loader from '@/components/Loader.vue';
import FormMenus from "../Menus/FormMenus.vue"

const isOpenModal = ref(false);
const isOpen = ref(false);
const menu = ref({});
const menus = ref([]);
const loaded = ref(true);

const openModal = () => {
  isOpenModal.value = true;
};

const closeModal = () => {
  menu.value = {};
  isOpenModal.value = false;
}

const edit = (data: any) => {
  menu.value = data;
  openModal();
};

const get = async () => {
  loaded.value = true
  menus.value = [];
try {
  const response = await MenuService.getMenus();
  menus.value = response.data.data;
} catch (error) {
  
} finally {
  loaded.value = false
}
};

const saved = async () => {
  closeModal()
  await get();
};

const remove = async (id: number | undefined) => {
  if (id === undefined) return;
  const confirm = await questionSweet('Atención', `¿Estás seguro de que quieres eliminar el registro?`, 'question');
  if (!confirm) return;
  await MenuService.deleteMenu(id);
  await get();
};

onMounted(async () => {
  await get();
});
</script>

<template>
  <div>
    <tablesHeader icon="list" title="Menús" :searchActive="false" :btnCreate="true" @create="openModal()" />

    <div class="overflow-hidden mt-6 mx-auto md:w-[90%]">
      <table class="table-animation">
        <thead>
          <tr class="">
            <th class="">Menu</th>
            <th class="">URL</th>
            <th class="">Icon</th>
            <th class="">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="menu in menus" :key="menu.id">
            <td class="">{{ menu.title }}</td>
            <td class="">{{ menu.path }}</td>
            <td class="">
              <font-awesome-icon :icon="menu.icon" />
            </td>
            <td class="">
              <ActionsTable :deleteBtn="true" :editBtn="true" @edit="edit(menu)" @remove="remove(menu.id)" />
            </td>
          </tr>
          
          <tr v-if="menus.length == 0">
            <td class="" colspan="8" >
                <Loader v-if="loaded"/>
                <p v-else>Menus no encontrados.</p>
              </td>
          </tr>
        </tbody>
      </table>

      <div class="fixed z-[1000] inset-0 grid place-items-center transition-all bg-black/80" v-if="isOpenModal">
        <FormMenus @closeModal="closeModal" @saved="saved" :menuToEdit="menu"/>
      </div>
    </div>
  </div>
</template>
