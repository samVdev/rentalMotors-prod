<script setup lang="ts">
import { reactive} from "vue"
import Loader from "@/components/Loader.vue";

const props = defineProps<{
  data: any
  sending: boolean
  errors: any
}>()

const emit = defineEmits<{
  (e: 'submit', user: any): void
}>()


const form: any = reactive(props.data)

const submit = async () => {
  emit('submit', {
    name: form.name,
    email: form.email,
    phone: form.phone,
    password: form.password,
    role_id: form.role_id,
    cedula: form.cedula,
    dateN: form.dateN,
    dir: form.dir,
    usuario: form.usuario
  })
}

</script>

<template>
  <form @submit.prevent="submit" class="p-6 space-y-6">

    <div class="gap-6">
      <div>
        <label for="nombre" class="block text-sm font-semibold text-gray-700 mb-2">Nombre del lote</label>
        <input type="text" id="nombre" required v-model="form.name" placeholder="Nombre del lote"
          class="p-3 w-full border border-gray-200 rounded-xl shadow-sm focus:ring-2 focus:ring-[#FF7539] focus:border-[#FF7539] transition-all" />
      </div>
    </div>

      <button v-if="!sending" type="submit"
        class="w-[80%] mx-[10%] bg-[#FF7539] hover:bg-[#D54A5C] text-white px-6 py-3 rounded-2xl font-bold shadow-md transition transform hover:scale-[1.02]">
        Guardar
      </button>

      <Loader v-else class="mx-auto" />
  </form>
</template>

<style>
#password {
  @apply p-3 w-full border border-gray-200 shadow-sm focus:ring-2 focus:ring-[#FF7539] focus:border-[#FF7539] transition-all
}
</style>