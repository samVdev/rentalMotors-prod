<script setup lang="ts">
import { onMounted, reactive, ref, watch } from "vue";
import Loader from "@/components/Loader.vue";
import type User from "@/modules/User/types/User";
import type { VehicleType } from "@/modules/Vehicles/types/vehicleType";
import SearchSelect from "@/components/SearchSelect.vue";
import UploadModal from "@/modules/Application/components/UploadModal.vue";
import { descripcionesPredefinidas } from "@/utils/constantes/MantenenceTypes";
import LotesService from "@/modules/Lotes/services";
import { FormatCurrency, unFormatCurrency } from "@/utils/formatCurrency";
import { alertWithToast } from "@/utils/toast";

const props = defineProps<{
  id?: string
  financiamiento?: any
  sending: boolean
  errors: any
  users: User[]
  vehiculos: VehicleType[]
}>();

const lotes = ref<Array<any>>([]);

const lote_id = ref('');
const ready = ref(false)
const mostrarTextarea = ref(false);
const form = reactive({
  ...props.financiamiento,
});

const emit = defineEmits(['submit'])

const submit = (formData: FormData) => {
  delete form.cuotas
  delete form.total
  delete form.inicial
  delete form.mora
  delete form.interes
  delete form.image
  delete form.lote_name
  delete form.pago_inicial
  delete form.priceTotal

  submitFinal(formData)
}


const submitFinal = (formData: FormData) => {

  if (form.vehiculo === null) {
    delete form.vehiculo;
  }

  for (const key in form) {
    if (form.hasOwnProperty(key) && form[key] !== undefined && form[key] !== null) {
      formData.append(key, form[key]);
    }

    if (key === 'mora') {
      formData.append(key, form[key]);
      continue;
    }
  }

  formData.delete('precio');
  formData.append('precio', unFormatCurrency(form.precio as any) as any);

  emit('submit', formData)
}


const formEvent = (e: Event) => {
  if (!props.id) {

    if (form.tipo != 'vehicle' && form.precio == 0) return alertWithToast('El precio no puede ser 0 para este tipo de financiación', 'error')
    else if (form.tipo == 'vehicle' && !form.vehiculo) return alertWithToast('Debe seleccionar un vehículo para este tipo de financiación', 'error')
    else if (form.tipo == 'mantenence' && !form.observacion) return alertWithToast('Debe seleccionar un tipo de servicio para este tipo de financiación', 'error')
    else if (form.tipo == '') return alertWithToast('Debe seleccionar un tipo de financiación', 'error')
    else if (!form.cliente) return alertWithToast('Debe seleccionar un cliente', 'error')


    ready.value = true
  }
  else {
    const formData = new FormData(e.target as HTMLFormElement);
    submitFinal(formData)
  }
}

const handleTipoChange = (event: Event) => {
  const target = event.target as HTMLSelectElement;
  const value = target.value;

  if (value === 'otros') {
    mostrarTextarea.value = true;
    form.observacion = '';
  } else {
    mostrarTextarea.value = false;
    form.observacion = descripcionesPredefinidas[value];
  }
}

const getLotes = async () => {
  const response = await LotesService.getMin()

  if (response.data.length) {
    lotes.value = response?.data.map((item: any) => {
      return {
        id: item.id,
        name: `${item.nombre} - financiaciones: (${item.count})`
      }
    })

  }
};

const onEarningsInput = (e: Event) => {
  const target = e.target as HTMLInputElement;
  form.precio = FormatCurrency(target.value) as any;
}


onMounted(() => {

  if (props.id) getLotes()
});

watch(
  () => form.vehiculo,
  (newVehiculo) => {
    if (!newVehiculo) {
      form.precio = 0
      return
    }
    const vehicle = props.vehiculos.find(v => v.id == newVehiculo)
    form.precio = vehicle ? vehicle.precio : 0
  }
)

watch(
  () => form.tipo,
  (newTipo) => {
    if (newTipo === 'tax') {
      form.precio = 0
    }
  }
)

</script>

<template>
  <main>

    <UploadModal v-if="ready" :lotes="lotes" :precio="Number(unFormatCurrency(form.precio as any))"
      @close="ready = false" :plan="form.plan" @submit="submit" />

    <form v-else @submit.prevent="formEvent" class="space-y-6">

      <div class="grid lg:grid-cols-2 gap-6">
        <div v-if="!id">
          <label class="block text-sm font-semibold text-gray-700 mb-2">Tipo de financiación</label>
          <select name="tipo" v-model="form.tipo"
            class="p-3 w-full border rounded-xl shadow-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
            <option value="" disabled>Seleccione un tipo</option>
            <option value="vehicle">Vehículo</option>
            <option value="tax">Impuesto</option>
            <option value="mantenence">Mantenimiento</option>
          </select>
        </div>

        <div v-if="form.tipo == 'vehicle' && !id">
          <label class="block text-sm font-semibold text-gray-700 mb-2">Vehículo</label>
          <SearchSelect v-model="form.vehiculo" :items="vehiculos" displayField="marca"
            placeholder="Buscar vehiculo..." />
        </div>

        <div v-if="users.length > 0 && !id">
          <label class="block text-sm font-semibold text-gray-700 mb-2">Cliente</label>
          <SearchSelect v-model="form.cliente" :items="users" displayField="name" placeholder="Buscar cliente..." />
        </div>

        <div class="grid items-center" v-else-if="users.length == 0 && !id">
          <label class="block text-sm font-semibold text-gray-700 p-3">No se encontraron clientes</label>
        </div>

        <div v-if="lotes.length > 0 && id" class="mb-5 text-center">
          <label class="text-base text-gray-700 font-medium">Lote a colocar:</label>
          <div class="w-full text-center mt-3 outline-none mx-auto">
            <SearchSelect v-model="form.lote_name" :items="lotes" displayField="name" placeholder="Buscar lote..."
              required />
          </div>
        </div>

        <div class="mb-5" v-if="form.tipo != 'vehicle' && !id">
          <label class="block text-sm font-semibold text-gray-700 mb-2">Precio</label>
          <input name="precio" type="text" :value="form.precio" @input="onEarningsInput"
            placeholder="Ingresa el precio de esta financiación"
            class="p-3 w-full border rounded-xl shadow-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all" />
        </div>

        <div v-if="form.tipo == 'mantenence'" class="md:col-span-2">
          <div class="mb-6 grid lg:grid-cols-2">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Tipo de Servicio</label>
              <select @change="handleTipoChange"
                class="p-3 w-full border border-gray-200 rounded-xl shadow-sm focus:ring-2 focus:ring-[#FF7539] focus:border-[#FF7539] transition-all bg-white">
                <option value="" disabled selected>Seleccione una opción...</option>
                <option value="cambio_aceite">🛢️ Cambio de Aceite</option>
                <option value="preventivo">🔧 Mantenimiento Preventivo</option>
                <option value="correctivo">⚠️ Mantenimiento Correctivo</option>
                <option value="revision">🔍 Revisión General</option>
                <option value="llantas">⭕ Llantas</option>
                <option value="frenos">🛑 Frenos</option>
                <option value="transmision">⚙️ Transmisión</option>
                <option value="cadena">🔗 Cadena</option>
                <option value="filtros">🧽 Filtros</option>
                <option value="otros">🔨 Otros (Especificar)</option>
              </select>
            </div>
          </div>

          <div v-if="mostrarTextarea" class="transition-all duration-300">
            <label for="observacion" class="block text-sm font-semibold text-gray-700 mb-2">Especifique la
              Descripción</label>
            <textarea id="observacion" placeholder="Escriba aquí los detalles del trabajo..." v-model="form.observacion"
              maxlength="500" required
              class="p-3 w-full border border-gray-200 rounded-xl shadow-sm focus:ring-2 focus:ring-[#FF7539] focus:border-[#FF7539] transition-all resize-none h-32" />
          </div>
        </div>

        <div v-else>
          <label class="block text-sm font-semibold text-gray-700 mb-2">Observaciones</label>
          <textarea name="observacion" v-model="form.observacion" placeholder="Comentarios adicionales"
            class="p-3 w-full border rounded-xl shadow-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all resize-none"></textarea>
        </div>

      </div>

      <div class="flex justify-end space-x-3">
        <button v-if="!sending" type="submit"
          class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-2xl font-bold shadow-md transition transform hover:scale-[1.02]">
          {{ id ? 'Editar' : 'Siguiente' }}
        </button>
        <Loader v-else class="mx-auto" />
      </div>
    </form>

  </main>
</template>