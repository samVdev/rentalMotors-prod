<script setup lang="ts">
import { onMounted, reactive, ref, watch } from "vue"
import type User from "../types/User"
import type Role from "../types/Role"
import Loader from "@/components/Loader.vue";
import InputPassword from "@/components/inputPassword.vue";
import MiniTitle from "@/components/miniTitle.vue";
import LotesService from "@/modules/Lotes/services";

const props = defineProps<{
  uuid?: string
  user: User
  sending: boolean
  errors: any
  roles: Role[]
}>()

const emit = defineEmits<{
  (e: 'submit', user: any, userId?: string): void
}>()

const lotes = ref<Array<any>>([]);

const form = reactive({
  ...props.user,
  password: props.user.password || '',
  lotes: props.user.lotes || []
})

const activeInputPassword = ref(true)

const submit = async () => {
  emit('submit', { ...form }, props.uuid)
}

onMounted(() => {
  if (props.uuid) activeInputPassword.value = false
  getLotes()
})

const getLotes = async () => {
  try {
    const response = await LotesService.getMin()
    lotes.value = response.data
    form.lotes = form.lotes.filter((loteId: number) =>
      lotes.value.some((lote: any) => lote.id === loteId)
    );
  } catch (error) {
    console.error("Error cargando lotes", error)
  }
};

const toggleLote = (loteId: number) => {
  const index = form.lotes.indexOf(loteId);
  if (index > -1) {
    form.lotes.splice(index, 1);
  } else {
    form.lotes.push(loteId);
  }
}

const toggleAllLotes = () => {
  if (form.lotes.length === lotes.value.length) {
    form.lotes = [];
  } else {
    form.lotes = lotes.value.map((lote: any) => lote.id);
  }
}

</script>

<template>
  <form @submit.prevent="submit" class="p-6 space-y-6 h-full">

    <MiniTitle class="lg:col-span-2" text="Datos personales" />

    <div class="grid lg:grid-cols-2 gap-6">
      <div>
        <label for="nombre" class="block text-sm font-semibold text-gray-700 mb-2">Nombre Completo</label>
        <input type="text" id="nombre" required v-model="form.name" placeholder="Nombre Completo"
          class="p-3 w-full border border-gray-200 rounded-xl shadow-sm focus:ring-2 focus:ring-[#FF7539] focus:border-[#FF7539] transition-all" />
      </div>

      <div>
        <label for="nombre" class="block text-sm font-semibold text-gray-700 mb-2">Cédula</label>
        <input type="text" id="cedula" required v-model="form.cedula" placeholder="Cédula"
          class="p-3 w-full border border-gray-200 rounded-xl shadow-sm focus:ring-2 focus:ring-[#FF7539] focus:border-[#FF7539] transition-all" />
      </div>

      <div>
        <label for="celular" class="block text-sm font-semibold text-gray-700 mb-2">Celular</label>
        <input type="tel" id="celular" required v-model="form.phone" maxlength="15" placeholder="Ej: 0412000000"
          class="p-3 w-full border border-gray-200 rounded-xl shadow-sm focus:ring-2 focus:ring-[#FF7539] focus:border-[#FF7539] transition-all" />
      </div>

      <div>
        <label for="correo" class="block text-sm font-semibold text-gray-700 mb-2">Usuario</label>
        <input type="text" id="usuario" required v-model="form.usuario" placeholder="Usuario"
          class="p-3 w-full border border-gray-200 rounded-xl shadow-sm focus:ring-2 focus:ring-[#FF7539] focus:border-[#FF7539] transition-all" />
      </div>

      <div>
        <label for="correo" class="block text-sm font-semibold text-gray-700 mb-2">Fecha de Nacimiento</label>
        <input type="date" id="dateN" required v-model="form.dateN"
          class="p-3 w-full border border-gray-200 rounded-xl shadow-sm focus:ring-2 focus:ring-[#FF7539] focus:border-[#FF7539] transition-all" />
      </div>


      <div>
        <label for="correo" class="block text-sm font-semibold text-gray-700 mb-2">Correo Electrónico</label>
        <input type="email" id="correo" required v-model="form.email" placeholder="Correo Electrónico"
          class="p-3 w-full border border-gray-200 rounded-xl shadow-sm focus:ring-2 focus:ring-[#FF7539] focus:border-[#FF7539] transition-all" />
      </div>

      <MiniTitle class="mt-5 lg:col-span-2" text="Datos del Usuario" />

      <div>
        <label for="rol" class="block text-sm font-semibold text-gray-700 mb-2">Rol</label>
        <select id="rol" required v-model="form.role_id"
          class="p-3 w-full border border-gray-200 rounded-xl shadow-sm capitalize focus:ring-2 focus:ring-[#FF7539] focus:border-[#FF7539] transition">
          <option value="" disabled>Selecciona un Rol</option>
          <option v-for="rol in roles" :key="rol.id" :value="rol.id">{{ rol.name }}</option>
        </select>
      </div>

      <div>
        <label for="contraseña" class="block text-sm font-semibold text-gray-700 mb-2">Contraseña</label>
        <div v-if="activeInputPassword">
          <InputPassword v-model="form.password" name="password" placeholder="Ingresa la contraseña" class="mb-3" />

          <button v-if="uuid" type="button" @click="activeInputPassword = false"
            class="w-full md:w-auto bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-xl font-medium transition">
            Quitar contraseña
          </button>
        </div>

        <div v-else>
          <button type="button" @click="activeInputPassword = true"
            class="w-full md:w-auto bg-[#FF7539] hover:bg-[#D54A5C] text-white px-4 py-2 rounded-xl font-semibold transition">
            Cambiar contraseña
          </button>
        </div>
      </div>

    </div>

    <div>
      <label for="correo" class="block text-sm font-semibold text-gray-700 mb-2">Dirección (opcional)</label>
      <textarea type="date" id="dir" placeholder="Dirección (opcional)" v-model="form.dir" maxlength="500"
        class="p-3 w-full border border-gray-200 rounded-xl shadow-sm focus:ring-2 focus:ring-[#FF7539] focus:border-[#FF7539] transition-all resize-none" />
    </div>


    <div class="lg:col-span-2">
      <div class="flex justify-between items-center mb-3">
        <label class="block text-sm font-semibold text-gray-700">Asignar Lotes</label>
        <button v-if="lotes.length > 0" type="button" @click="toggleAllLotes"
          class="text-xs font-bold text-[#FF7539] hover:text-[#D54A5C] transition-colors flex items-center gap-1">
          <svg v-if="form.lotes.length === lotes.length" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
          </svg>
          <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
          </svg>
          {{ form.lotes.length === lotes.length ? 'Deseleccionar Todos' : 'Seleccionar Todos' }}
        </button>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
        <div v-for="lote in lotes" :key="lote.id" @click="toggleLote(lote.id)" :class="[
          'cursor-pointer p-3 rounded-xl border-2 transition-all flex justify-between items-center',
          form.lotes.includes(lote.id)
            ? 'border-[#FF7539] bg-orange-50'
            : 'border-gray-100 bg-white hover:border-gray-300'
        ]">
          <div>
            <p class="font-bold text-sm" :class="form.lotes.includes(lote.id) ? 'text-[#FF7539]' : 'text-gray-700'">
              {{ lote.nombre }}
            </p>
            <p class="text-xs text-gray-500">({{ lote.count }}) Financiaciones</p>
          </div>

          <div v-if="form.lotes.includes(lote.id)" class="text-[#FF7539]">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                clip-rule="evenodd" />
            </svg>
          </div>
        </div>
      </div>

      <p v-if="lotes.length === 0" class="text-sm text-gray-400 italic">No hay lotes disponibles...</p>
    </div>


    <div class="flex justify-end space-x-3">
      <button v-if="!sending" type="submit"
        class="w-full md:w-auto bg-[#FF7539] hover:bg-[#D54A5C] text-white px-6 py-3 rounded-2xl font-bold shadow-md transition transform hover:scale-[1.02]">
        Guardar
      </button>

      <Loader v-else class="mx-auto" />
    </div>
  </form>
</template>

<style>
#password {
  @apply p-3 w-full border border-gray-200 shadow-sm focus:ring-2 focus:ring-[#FF7539] focus:border-[#FF7539] transition-all
}
</style>