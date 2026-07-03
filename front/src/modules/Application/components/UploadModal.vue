<script setup lang="ts">
import { onMounted, ref } from "vue";
import { alerta } from "@/utils/alert";
import UploadField from "@/modules/guest/components/UploadField.vue";
import { parsePrices } from "@/utils/parsePrices";
import Loader from "@/components/Loader.vue";
import LotesService from "@/modules/Lotes/services";
import SearchSelect from "@/components/SearchSelect.vue";
import { FormatCurrency, unFormatCurrency } from "@/utils/formatCurrency";
import ServiceService from "../services/services";

const props = defineProps<{
  plan: string,
  precio: number,
}>();

const emit = defineEmits<{
  (e: 'close'): void,
  (e: "submit", form: FormData): void;
}>();

const loading = ref(false)

const lote_id = ref('');
const code = ref('');
const planed = ref(props.plan || '');
const lotes = ref<Array<any>>([]);
const allServices = ref<Array<any>>([]);
const selectedServices = ref<Array<{ id: number, name: string, price: string }>>([]);

const total = ref(null);
const meses = ref(null);
const mora = ref(null);
const interes = ref(null);

const interesTotal = ref(null);
const finalPrice = ref(null);
const totalServicesPrice = ref(0);

const imgFile = ref<File | null>(null);

function handleSetImg(payload: { imgFile: File | null; imgURL: string | null }) {
  imgFile.value = payload.imgFile;
}

const toggleService = (service: any) => {
  const index = selectedServices.value.findIndex(s => s.id === service.id);
  if (index !== -1) {
    selectedServices.value.splice(index, 1);
  } else {
    selectedServices.value.push({
      id: service.id,
      name: service.name,
      price: '0'
    });
  }
  calcInteresTotal();
};

const onServicePriceInput = (serviceId: number, e: Event) => {
  const target = e.target as HTMLInputElement;
  const index = selectedServices.value.findIndex(s => s.id === serviceId);
  if (index !== -1) {
    selectedServices.value[index].price = FormatCurrency(target.value) as any;
    calcInteresTotal();
  }
};

async function confirmUpload(e: Event) {
  e.preventDefault();
  loading.value = true

  if (!imgFile.value) {
    alerta("Info", "Por favor selecciona una imagen antes de continuar.", "info");
    return loading.value = false
  }

  if (!total.value) {
    alerta("Info", "Por favor ingrese el total del pago realizado.", "info");
    return loading.value = false
  }

  const form = new FormData(e.target as HTMLFormElement);
  form.append('lote_id', lote_id.value);

  selectedServices.value.forEach((service, index) => {
    form.append(`services[${index}][id]`, service.id.toString());
    form.append(`services[${index}][price]`, unFormatCurrency(service.price).toString());
  });

  emit('submit', form);

  setTimeout(() => {
    loading.value = false
  }, 1000);
}


const calcPorcent = () => {
  const c = Number(meses.value)

  if (c >= 12 && c <= 18) {
    interes.value = 1.58
  } else if (c >= 24 && c <= 26) {
    interes.value = 1.70
  } else if (c >= 36 && c <= 46) {
    interes.value = 2.40
  } else if (c >= 46) {
    interes.value = 2.80
  } else {
    interes.value = 1
  }

  calcInteresTotal()
}

const calcInteresTotal = () => {
  const noPoints = unFormatCurrency(total.value as any)
  const inicial = Number(noPoints) || 0
  const precio = Number(props.precio) || 0
  const porcentaje = Number(interes.value) || 0

  const restante = precio - inicial
  totalServicesPrice.value = selectedServices.value.reduce((acc, s) => acc + Number(unFormatCurrency(s.price)), 0);

  if (restante <= 0) {
    interesTotal.value = 0
    finalPrice.value = totalServicesPrice.value
    return
  }

  interesTotal.value = restante * porcentaje
  finalPrice.value = (Number(interesTotal.value) || 0) + restante + totalServicesPrice.value
}

const onEarningsInput = (e: Event) => {
  const target = e.target as HTMLInputElement;
  total.value = FormatCurrency(target.value) as any;
  calcInteresTotal()
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

const getServices = async () => {
  try {
    const response = await ServiceService.getAll();
    allServices.value = response.data;
  } catch (error) {
    console.error("Error fetching services", error);
  }
};

onMounted(() => {
  getLotes();
  getServices();
});

</script>

<template>
  <form @submit.prevent="confirmUpload">
    <h2 class="text-2xl font-semibold text-gray-800 text-center">
      Subir comprobante
    </h2>

    <h4 v-if="plan" class="mb-8 mt-4 mx-auto text-center">Plan de financiamiento: <strong>{{ plan }}</strong></h4>

    <div class="flex flex-col lg:flex-row gap-8">
      <!-- Columna Izquierda: Formulario Principal -->
      <div class="lg:w-2/3">
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
          <div class="flex flex-col items-center gap-4">
            <UploadField id="upload-comprobante" label="Seleccionar imagen" @setImg="handleSetImg" />
          </div>

          <div class="flex flex-col gap-6 text-center">
            <div class="border-t pt-5">
              <label class="text-base text-gray-700 font-medium">Cantidad de Meses:</label>
              <input type="text" name="meses" @input="calcPorcent" v-model="meses" placeholder="0"
                class="w-full text-3xl text-center font-bold text-[#FF7539] mt-3 outline-none mx-auto" required />
            </div>

            <div class="border-t pt-5">
              <label class="text-base text-gray-700 font-medium">Total del pago realizado:</label>
              <input type="text" name="inicial" :value="total" @input="onEarningsInput" placeholder="0"
                class="w-full text-3xl text-center font-bold text-[#FF7539] mt-3 outline-none mx-auto" required />
            </div>
          </div>

          <div class="flex flex-col gap-6 text-center">
            <div class="border-t pt-5">
              <label class="text-base text-gray-700 font-medium">Interes (%):</label>
              <input type="text" name="interes" v-model="interes" @input="calcInteresTotal" placeholder="0"
                class="w-full text-3xl text-center font-bold text-[#FF7539] mt-3 outline-none mx-auto" required />
            </div>
          </div>
        </div>

        <article class="mt-6 bg-gray-50 border border-gray-100 rounded-xl p-4 shadow-sm">
          <h3 class="text-base font-semibold text-gray-700 mb-3 text-center uppercase tracking-wider">Resumen</h3>
          <div class="grid grid-cols-2 gap-y-2 text-sm text-gray-600">
            <span>Precio:</span><span class="text-right font-bold">{{ parsePrices(precio) }}</span>
            <span>Inicial:</span><span class="text-right font-bold">{{ parsePrices(Number(unFormatCurrency(total)))
            }}</span>
            <span>Interés:</span><span class="text-right font-bold">{{ parsePrices(Number(interesTotal)) }}</span>
            <span>Costo neto Financiación sin servicios:</span><span class="text-right font-bold">{{
              parsePrices(Number(finalPrice) - Number(totalServicesPrice)) }}</span>
            <span class="font-bold text-gray-800">Costo con tramites y servicios:</span><span
              class="text-right font-bold text-[#FF7539]">{{
                parsePrices(Number(finalPrice)) }}</span>
          </div>
        </article>


        <div class="grid md:grid-cols-3 gap-4 mt-6">
          <div class="border-t pt-4 text-center">
            <label class="text-xs text-gray-500 font-bold uppercase">Lote:</label>
            <SearchSelect v-if="lotes.length > 0" v-model="lote_id" :items="lotes" displayField="name"
              placeholder="Lote..." required />
            <p v-else>No se encontraron POS</p>
          </div>

          <div class="border-t pt-4 text-center">
            <label class="text-xs text-gray-500 font-bold uppercase">Código:</label>
            <input type="text" name="code" v-model="code" placeholder="Código"
              class="w-full uppercase text-lg text-center font-bold text-[#FF7539] mt-1 outline-none" required />
          </div>

          <div class="border-t pt-4 text-center">
            <label class="text-xs text-gray-500 font-bold uppercase">Plan:</label>
            <select name="plan" v-model="planed" required
              class="p-2 w-full border rounded-lg shadow-sm focus:ring-1 focus:ring-orange-500 outline-none text-sm">
              <option value="" disabled>Seleccione</option>
              <option value="Diario">Diario</option>
              <option value="Semanal">Semanal</option>
              <option value="Quincenal">Quincenal</option>
              <option value="Mensual">Mensual</option>
            </select>
          </div>
        </div>
      </div>

      <div class="lg:w-1/3 lg:border-l lg:pl-6">
        <h3 class="text-sm font-bold text-gray-500 mb-4 uppercase tracking-widest flex items-center gap-2">
          <font-awesome-icon icon="fa-solid fa-plus-circle" />
          Servicios Disponibles
        </h3>
        <div class="flex flex-col gap-3 max-h-[550px] overflow-y-auto pr-2">
          <div v-for="service in allServices" :key="service.id"
            class="group p-3 rounded-xl border transition-all duration-300 relative" :class="selectedServices.some(s => s.id === service.id)
              ? 'bg-orange-50/50 border-orange-200'
              : 'bg-white border-gray-200 hover:border-orange-400 hover:shadow-sm'">

            <div @click="toggleService(service)" class="flex items-center justify-between cursor-pointer">
              <span class="text-xs font-bold text-gray-700 uppercase">{{ service.name }}</span>
              <font-awesome-icon icon="fa-solid fa-check-circle" class="text-green-500"
                v-if="selectedServices.some(s => s.id === service.id)" />
              <font-awesome-icon icon="fa-solid fa-plus"
                class="text-gray-300 group-hover:text-orange-400 transition-colors" v-else />
            </div>

            <!-- Input de Precio Inline -->
            <div v-if="selectedServices.some(s => s.id === service.id)"
              class="mt-3 pt-3 border-t border-orange-100 animate-fade-in">
              <div class="flex items-center gap-2">
                <span class="text-xs font-semibold text-orange-800">Precio:</span>
                <div class="relative flex-1">
                  <span class="absolute left-2 top-1/2 -translate-y-1/2 text-gray-400 text-xs">$</span>
                  <input type="text" :value="selectedServices.find(s => s.id === service.id)?.price"
                    @input="(e) => onServicePriceInput(service.id, e)" placeholder="0"
                    class="w-full pl-5 pr-2 py-1.5 text-right text-sm border-0 bg-white rounded-lg focus:ring-1 focus:ring-orange-500 outline-none font-bold text-orange-600 shadow-inner" />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>


    <div class="mt-10 flex justify-end gap-4">
      <button type="button"
        class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium transition"
        @click="emit('close')">
        Cancelar
      </button>

      <button v-if="!loading"
        class="px-5 py-2.5 rounded-lg bg-[#FF7539] text-white font-medium hover:bg-[#e7692f] transition">
        Confirmar
      </button>
      <Loader v-else class="mx-5" />
    </div>

  </form>
</template>

<style scoped>
@keyframes fade-in {
  from {
    opacity: 0;
    transform: translateY(-8px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in {
  animation: fade-in 0.25s ease-out;
}
</style>
