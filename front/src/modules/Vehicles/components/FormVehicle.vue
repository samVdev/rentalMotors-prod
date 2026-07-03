<script setup lang="ts">
import { onMounted, reactive } from "vue"
import Loader from "@/components/Loader.vue"
import UploadImg from "@/components/uploadImg.vue";

import type { VehicleType } from "../types/vehicleType";
import type User from "@/modules/User/types/User";
import { FormatCurrency, unFormatCurrency } from "@/utils/formatCurrency";

const props = defineProps<{
  id?: string
  vehicle: VehicleType
  sending: boolean
  errors: any,
  users: User[]
}>()

const emit = defineEmits(['submit'])

const form: VehicleType = reactive({
  ...props.vehicle,
  imageFile: '',
})

const url = import.meta.env.VITE_APP_API_URL


const onPriceInput = (e: Event) => {
    const target = e.target as HTMLInputElement;
    form.precio = FormatCurrency(target.value);
};


onMounted(() => {
  if (props.id) {
    form.marca = props.vehicle.marca
    form.modelo = props.vehicle.modelo
    form.imageFile = `${url}/${props.vehicle.image}`
    form.year = props.vehicle.year
    form.cc = props.vehicle.cc
    form.color = props.vehicle.color
    form.precio = FormatCurrency(props.vehicle.precio)
    form.kilometraje = props.vehicle.kilometraje
    form.type = props.vehicle.type
    form.show = props.vehicle.show
  }
})


const submit = (e: Event) => {
  e.preventDefault()
  const formData = new FormData(e.target as HTMLFormElement)

  const imageInput = (e.target as HTMLFormElement).querySelector<HTMLInputElement>('input[name="file"]')
  if (imageInput && imageInput.files && imageInput.files.length > 0) {
    formData.append("file", imageInput.files[0])
  } else {
    formData.delete("file")
  }

  formData.set("precio", unFormatCurrency(form.precio))

  emit('submit', formData)
}

</script>

<template>
  <form @submit.prevent="submit" class=" space-y-6 h-full">

    <div class="grid lg:grid-cols-2 gap-6">
      <div>
        <label for="brand" class="block text-sm font-semibold text-gray-700 mb-2">Marca</label>
        <input type="text" name="marca" required v-model="form.marca" placeholder="Ej: Toyota"
          class="p-3 w-full border border-gray-200 rounded-xl shadow-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all" />
      </div>

      <div>
        <label for="model" class="block text-sm font-semibold text-gray-700 mb-2">Modelo</label>
        <input type="text" name="modelo" required v-model="form.modelo" placeholder="Ej: Corolla"
          class="p-3 w-full border border-gray-200 rounded-xl shadow-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all" />
      </div>

      <div>
        <label for="year" class="block text-sm font-semibold text-gray-700 mb-2">Año</label>
        <input type="number" name="year" required v-model="form.year" placeholder="2020"
          class="p-3 w-full border border-gray-200 rounded-xl shadow-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all" />
      </div>

      <div>
        <label for="cc" class="block text-sm font-semibold text-gray-700 mb-2">Cilindrada</label>
        <input type="text" name="cc" required v-model="form.cc" placeholder="Ej: 200"
          class="p-3 w-full border border-gray-200 rounded-xl shadow-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all" />
      </div>

      <div>
        <label for="color" class="block text-sm font-semibold text-gray-700 mb-2">Color</label>
        <input type="text" name="color" required v-model="form.color" placeholder="Ej: Rojo"
          class="p-3 w-full border border-gray-200 rounded-xl shadow-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all" />
      </div>

      <div>
        <label for="price" class="block text-sm font-semibold text-gray-700 mb-2">Precio</label>
        <input type="text" name="precio" required :value="form.precio" @input="onPriceInput" step="0.01" placeholder="Ej: 12000"
          class="p-3 w-full border border-gray-200 rounded-xl shadow-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all" />
      </div>

      <div>
        <label for="mileage" class="block text-sm font-semibold text-gray-700 mb-2">Kilometraje</label>
        <input type="number" name="kilometraje" required v-model="form.kilometraje" placeholder="Ej: 45000"
          class="p-3 w-full border border-gray-200 rounded-xl shadow-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all" />
      </div>

      <div>
        <label for="image" class="block text-sm font-semibold text-gray-700 mb-2">Imagen</label>
        <UploadImg :img="form.imageFile" @setImg="({ imgFile, imgURL }) => {
          form.imageFile = imgURL
          form.image = imgFile
        }" />
      </div>

      <div class="flex items-center space-x-3">
        <input type="checkbox" name="show" v-model="form.show"
          class="w-5 h-5 text-orange-500 border-gray-300 rounded focus:ring-orange-500">
        <label for="show" class="text-sm font-semibold text-gray-700">Mostrar en catálogo</label>
      </div>
    </div>

    <div class="flex justify-end space-x-3">
      <button v-if="!sending" type="submit"
        class="w-full md:w-auto bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-2xl font-bold shadow-md transition transform hover:scale-[1.02]">
        Guardar
      </button>

      <Loader v-else class="mx-auto" />
    </div>
  </form>
</template>
