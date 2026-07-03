<script setup lang="ts">
import { ref, reactive} from "vue"
import type Role from "../../types/Role"
import type { Error } from "@/types/Error"
import type { Menu } from "@/types/Menu"

const props = defineProps<{
  id?: string
  role: Role  
  sending: boolean
  loading: boolean
  errors: Error | undefined
  menus: Menu[]
}>()

const emit = defineEmits<{
  (e: 'submit', role: Role, roleId?: string): void
}>()

const form: Role = reactive(props.role)

const allSelected = ref(false)

const submit = async () => {
  emit('submit', form, props.id)  
}

const selectAll = () => {
  if (!allSelected.value) {        
    let temp:number[] = [];
    props.menus.forEach(menu => {
      if (menu.path !== '#')
        temp.push(menu.id);
    });
    form.menu_ids=[];
    form.menu_ids=temp;
    allSelected.value=true;                     
  } else {
    form.menu_ids=[];        
    allSelected.value=false;
  }      
}
</script>

<template>
  <form @submit.prevent="submit" class="p-4 w-full mx-auto md:w-[80%]">    
    <div class="grid lg:grid-cols-2 gap-4">          
      <label class="block">
        <span>Nombre del rol</span>
        <input v-model="form.name" type="text" class="mt-5" />
      </label>          

      <label class="block">
        <span>Descripción del rol</span>
        <input v-model="form.description" type="text" class="mt-5"  />
      </label>
    </div>
 
    <br/>

    <div class="">
      <table class="">
        <thead>
          <tr class=""> 
            <th class="">Opciones del Menú</th>                             
            <th><input type="checkbox"  v-model="allSelected" @click="selectAll" title="Seleccionar todos"></th>
          </tr>
        </thead>
        <tbody>              
          <tr
	          v-for="menu in menus"
	          :key="menu.id"
	          class="focus-within:bg-gray-400"
	        > 
            <td>
	            {{ menu.title}}
	          </td> 
            <td>              
              <div v-if="menu.path !== '#'" class="flex items-center space-x-1">                                
                <input type="checkbox" v-model="form.menu_ids" :value="menu.id">
              </div>
            </td>               
          </tr>                
        </tbody>            
      </table>      
    </div>

    <div class="mt-4 px-2 border-gray-100 flex justify-end space-x-2">
      <button :isDisabled='sending' class="bg-[#FF7539] hover:bg-[#D54A5C] transition-all text-white font-bold w-full text-center mt-4 h-[40px] grid place-items-center rounded-3xl">
                <p>{{sending ? 'Guardando...' : 'Guardar'}}</p>
      </button>
    </div>
  </form>  
</template>
