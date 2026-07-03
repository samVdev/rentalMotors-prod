<script setup lang="ts">
import { onMounted, reactive, ref, watch } from "vue"
import Loader from "@/components/Loader.vue";
import MiniTitle from "@/components/miniTitle.vue";
import UploadImg from "@/components/uploadImg.vue";
import ViewFile from "@/components/ViewFile.vue";
import { FormatCurrency, unFormatCurrency } from "@/utils/formatCurrency";
import type User from "../types/User"

const props = defineProps<{
  uuid?: string
  user: User
  sending: boolean
  errors: any
}>()

const emit = defineEmits(['submit'])

const form: User = reactive(props.user)

const activeInputPassword = ref(true)
const activeUpdloadPhoto = ref(true)
const userHasSelectedNewPhoto = ref(false)

const onEarningsInput = (e: Event) => {
  const target = e.target as HTMLInputElement;
  form.earnings = FormatCurrency(target.value) as any;
}

/*const submit = async () => {
  emit('submit', {
    name: form.name,
    usuario: form.usuario,
    email: form.email,
    phone: form.phone,
    cedula: form.cedula,
    dateN: form.dateN,
    dir: form.dir,
    earnings: form.earnings
  }, props.uuid)
}*/


const submit = (e: Event) => {
  e.preventDefault()
  const formData = new FormData(e.target as HTMLFormElement)

  let rawEarnings = formData.get("earnings") as string;
  if (rawEarnings) {
    rawEarnings = rawEarnings.replace(/\./g, '').replace(/,/g, '.');
    formData.set("earnings", rawEarnings);
  }

  if (form.image) {
    formData.append("file", form.image)
  } else {
    formData.delete("file")
  }

  emit('submit', formData, props.uuid)
}

onMounted(() => {
  if (props.uuid) activeInputPassword.value = false
  if (form.imageApp) activeUpdloadPhoto.value = false
  if (form.earnings) form.earnings = unFormatCurrency(form.earnings) as any
})

watch(() => props.user, (newVal) => {
  if (newVal.imageApp && !form.imageFile) {
    activeUpdloadPhoto.value = false
  }
}, { deep: true })

</script>

<template>
  <form @submit.prevent="submit" class="space-y-6 ">

    <MiniTitle class="lg:col-span-2" text="Datos personales" />

    <div class="grid lg:grid-cols-2 gap-6">
      <div>
        <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Nombre Completo</label>
        <input type="text" name="name" id="name" required v-model="form.name" placeholder="Nombre Completo"
          class="p-3 w-full border border-gray-200 rounded-xl shadow-sm focus:ring-2 focus:ring-[#FF7539] focus:border-[#FF7539] transition-all" />
      </div>

      <div v-if="props.uuid">
        <label for="usuario" class="block text-sm font-semibold text-gray-700 mb-2">Usuario</label>
        <input type="text" name="usuario" id="usuario" required v-model="form.usuario" placeholder="Usuario"
          maxlength="15"
          class="p-3 w-full border border-gray-200 rounded-xl shadow-sm focus:ring-2 focus:ring-[#FF7539] focus:border-[#FF7539] transition-all" />
      </div>

      <div>
        <label for="cedula" class="block text-sm font-semibold text-gray-700 mb-2">Cédula o PPT</label>
        <input type="text" name="cedula" id="cedula" required v-model="form.cedula" placeholder="Cédula" maxlength="15"
          class="p-3 w-full border border-gray-200 rounded-xl shadow-sm focus:ring-2 focus:ring-[#FF7539] focus:border-[#FF7539] transition-all" />
      </div>

      <div>
        <label for="celular" class="block text-sm font-semibold text-gray-700 mb-2">Celular</label>
        <input type="tel" name="phone" id="celular" required v-model="form.phone" maxlength="15"
          placeholder="Ej: 0412000000"
          class="p-3 w-full border border-gray-200 rounded-xl shadow-sm focus:ring-2 focus:ring-[#FF7539] focus:border-[#FF7539] transition-all" />
      </div>

      <div>
        <label for="correo" class="block text-sm font-semibold text-gray-700 mb-2">Correo Electrónico</label>
        <input type="email" name="email" id="correo" v-model="form.email" placeholder="Correo Electrónico"
          class="p-3 w-full border border-gray-200 rounded-xl shadow-sm focus:ring-2 focus:ring-[#FF7539] focus:border-[#FF7539] transition-all" />
      </div>


      <div>
        <label for="dateN" class="block text-sm font-semibold text-gray-700 mb-2">Fecha de Nacimiento</label>
        <input type="date" name="dateN" id="dateN" required v-model="form.dateN"
          class="p-3 w-full border border-gray-200 rounded-xl shadow-sm focus:ring-2 focus:ring-[#FF7539] focus:border-[#FF7539] transition-all" />
      </div>

      <div>
        <label for="earnings" class="block text-sm font-semibold text-gray-700 mb-2">Ingresos Mensuales</label>
        <input type="text" name="earnings" id="earnings" :value="form.earnings" @input="onEarningsInput"
          placeholder="Ingresos Mensuales"
          class="p-3 w-full border border-gray-200 rounded-xl shadow-sm focus:ring-2 focus:ring-[#FF7539] focus:border-[#FF7539] transition-all" />
      </div>

      <div>
        <label for="file" class="block text-sm font-semibold text-gray-700 mb-2">Foto del cliente</label>

        <UploadImg v-if="activeUpdloadPhoto || !form.imageApp" :img="form.imageFile" @setImg="({ imgFile, imgURL }) => {
          form.imageFile = imgURL
          form.image = imgFile
          activeUpdloadPhoto = true
        }" />

        <div
          class="w-32 h-32 overflow-hidden mx-auto md:w-52 md:h-52 flex items-center justify-center border-2 border-dashed rounded-lg"
          v-else>
          <ViewFile :path="form.imageApp" />
        </div>

        <button v-if="props.uuid && form.imageApp" type="button" @click="activeUpdloadPhoto = !activeUpdloadPhoto"
          class="w-full md:w-auto block mx-auto bg-[#FF7539] hover:bg-[#D54A5C] text-white px-6 py-3 rounded-2xl font-bold shadow-md my-4 transition transform hover:scale-[1.02]">
          <span v-if="activeUpdloadPhoto">
            Ver foto actual del servidor
          </span>
          <span v-else>
            {{ form.imageFile ? 'Ver foto nueva seleccionada' : 'Cambiar foto' }}
          </span>
        </button>
      </div>

    </div>

    <div>
      <label for="dir" class="block text-sm font-semibold text-gray-700 mb-2">Dirección (opcional)</label>
      <textarea type="date" name="dir" id="dir" placeholder="Dirección (opcional)" v-model="form.dir" maxlength="500"
        class="p-3 w-full border border-gray-200 rounded-xl shadow-sm focus:ring-2 focus:ring-[#FF7539] focus:border-[#FF7539] transition-all resize-none" />
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