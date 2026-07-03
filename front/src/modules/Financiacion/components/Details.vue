<script setup lang="ts">
import { parsePrices } from '@/utils/parsePrices';
import type { FinancingDetailsInterface } from '../types/VehicleInterface'
import formatDate from '@/utils/formatDate';
import MiniTitle from '@/components/miniTitle.vue';
import CardPrice from "./CardPrice.vue"
import { onMounted, ref, watch, computed } from 'vue';
import ViewFile from "@/components/ViewFile.vue";
import FinancingService from "@/modules/Financiacion/services"
import RequirementsModal from "./RequirementsModal.vue";
import { alertWithToast } from "@/utils/toast";
import { useFinancingDetails } from '../hooks/useFinancingDetails';
import { FormatCurrency } from '@/utils/formatCurrency';

import { useAuthStore } from '@/modules/Auth/stores';

interface Props {
  financing: FinancingDetailsInterface
}

const formatDateFn = formatDate;
const props = defineProps<Props>()
const authStore = useAuthStore();

const emit = defineEmits(['close', 'update'])
const close = () => emit('close')

const isAdmin = computed(() => {
  const user = authStore.authUser;
  return user && (user.role_id == 1 || user.role_id == 2);
});

const {
  docFile,
  statusMora,
  loadingMora,
  loadingInvoice,
  showRequirementsModal,
  isEditingPlaca,
  newPlaca,
  savingPlaca,
  isEditingInterest,
  isEditingFinancingPrice,
  isEditingInteresPrice,
  isEditingInstallments,
  isEditingSuggested,
  isEditingServices,
  isEditingPlan,
  isEditingStatus,
  savingFinance,
  savingServices,
  financeForm,
  servicesForm,
  openPlacaModal,
  cancelEditPlaca,
  updatePlaca,
  activarMora,
  handleDownloadInvoice,
  handleUpdateRequirements,
  startEditingFinance,
  saveFinance,
  computedPrecioVehiculo,
  computedNetoTotal,
  computedFaltante,
  handlePriceInput,
  handleMesesInput,
  reCalculatePrices,
  liveFinancing,
  startEditingServices,
  toggleService,
  saveServices
} = useFinancingDetails(props as any, emit);

const formatValue = (val: any) => (val !== null && val !== undefined && val !== '' ? val : '—')

// Sync initial values if needed or just use startEditingFinance
</script>

<template>
  <section class="fixed inset-0 z-[2000] bg-black/60 backdrop-blur-sm flex items-center justify-center p-2 md:p-4">

    <section v-if="docFile != ''" class="fixed inset-0 flex items-center justify-center bg-black/70 z-[60] p-4"
      @click.self="docFile = ''">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl p-4 md:p-8 animate-in fade-in zoom-in duration-200">
        <div class="flex justify-end mb-2">
          <button @click="docFile = ''" class="text-gray-500 hover:text-black text-xl">✕</button>
        </div>
        <ViewFile :path="docFile" />
      </div>
    </section>



    <div
      class="relative bg-gray-50 rounded-2xl md:rounded-3xl shadow-2xl w-full md:max-w-[90%] h-full max-h-[95vh] flex flex-col overflow-hidden border border-gray-200">

      <header class="sticky top-0 z-10 flex justify-between items-center p-4 md:p-6 bg-white border-b border-gray-200">
        <div class="flex flex-col md:flex-row md:items-center gap-1 md:gap-3">
          <h2 class="text-lg md:text-xl font-bold text-gray-800">Detalles de Financiación:</h2>
          <span class="text-red-500 text-xl md:text-2xl font-black">({{ props.financing.lote }} - {{
            props.financing.codigo }})</span>
        </div>
        <button @click="close"
          class="bg-gray-100 hover:bg-gray-200 p-2 md:p-3 rounded-full transition-colors text-gray-600" title="Cerrar">
          <span class="block w-5 h-5 flex items-center justify-center">✕</span>
        </button>
      </header>

      <div class="flex-grow overflow-y-auto">
        <section
          class="flex flex-col md:flex-row gap-4 p-4 md:p-6 items-center justify-between bg-white/50 border-b border-gray-100">
          <div
            class="flex items-center gap-3 px-4 py-2 rounded-full border w-full md:w-auto justify-center transition-all"
            :class="{
              'bg-yellow-50 border-yellow-200 text-yellow-700': liveFinancing?.estado === 'pendiente',
              'bg-red-50 border-red-200 text-red-700': liveFinancing?.estado === 'rechazado',
              'bg-orange-50 border-orange-200 text-orange-700': liveFinancing?.estado === 'finalizada' || liveFinancing?.estado === 'finished',
              'bg-green-50 border-green-200 text-green-700': liveFinancing?.estado === 'activa',
            }">
            <div v-if="!isEditingStatus" class="flex items-center gap-3">
              <span class="w-3 h-3 rounded-full animate-pulse" :class="{
                'bg-yellow-500': liveFinancing?.estado === 'pendiente',
                'bg-red-500': liveFinancing?.estado === 'rechazado',
                'bg-green-500': liveFinancing?.estado === 'activa',
                'bg-orange-500': liveFinancing?.estado === 'finalizada' || liveFinancing?.estado === 'finished'
              }"></span>
              <p class="font-bold uppercase tracking-wider text-sm flex items-center gap-2">
                Estado: {{ formatValue(liveFinancing?.estado) }}
                <button v-if="isAdmin" @click="startEditingFinance('status')"
                  class="ml-1 opacity-60 hover:opacity-100 transition-opacity">
                  <font-awesome-icon icon="fa-solid fa-pen" class="text-[10px]" />
                </button>
              </p>
            </div>
            <div v-else class="flex items-center gap-2">
              <select v-model="financeForm.status"
                class="bg-transparent border-b border-current outline-none font-bold uppercase text-sm">
                <option value="pending">Pendiente</option>
                <option value="active">Activa</option>
                <option value="finished">Finalizada (Cerrar)</option>
                <option value="cancelled">Rechazado</option>
              </select>
              <button @click="saveFinance('status')" class="text-green-700 font-bold px-1 hover:scale-110 transiton"
                title="Guardar">✓</button>
              <button @click="isEditingStatus = false" class="text-red-700 font-bold px-1 hover:scale-110 transition"
                title="Cancelar">✕</button>
            </div>
          </div>

          <div class="flex flex-col md:flex-row gap-2 w-full md:w-auto">
            <button @click="handleDownloadInvoice"
              class="w-full md:w-auto bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl font-bold transition-all shadow-lg active:scale-95 flex items-center justify-center gap-2 disabled:opacity-60"
              :disabled="loadingInvoice">
              <span v-if="loadingInvoice"
                class="animate-spin inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full"></span>
              <font-awesome-icon v-else icon="fa-solid fa-file-invoice" />
              {{ loadingInvoice ? 'Generando...' : 'Generar Factura' }}
            </button>

            <template v-if="props.financing.recaudos_pdf">
              <button v-if="isAdmin" @click="showRequirementsModal = true"
                class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-bold transition-all shadow-lg active:scale-95 flex items-center justify-center gap-2">
                <font-awesome-icon icon="fa-solid fa-upload" />
                Actualizar Recaudos
              </button>

              <button @click="docFile = props.financing.recaudos_pdf"
                class="w-full md:w-auto bg-green-600 hover:bg-green-700 text-white px-6 py-2.5 rounded-xl font-bold transition-all shadow-lg active:scale-95 flex items-center justify-center gap-2">
                <font-awesome-icon icon="fa-solid fa-eye" />
                Ver Recaudos
              </button>
            </template>
            <div v-else
              class="w-full md:w-auto bg-gray-100 text-gray-500 px-6 py-2.5 rounded-xl font-bold border border-gray-200 flex items-center justify-center gap-2">
              <font-awesome-icon icon="fa-solid fa-circle-info" />
              Sin recaudos
            </div>

            <button v-if="isAdmin" @click="activarMora"
              :class="[statusMora ? 'bg-red-600 hover:bg-red-700' : 'bg-indigo-600 hover:bg-indigo-700', 'w-full md:w-auto text-white px-6 py-2.5 rounded-xl font-bold transition-all shadow-lg active:scale-95 flex items-center justify-center gap-2']"
              :disabled="loadingMora">
              <font-awesome-icon :icon="statusMora ? 'fa-solid fa-ban' : 'fa-solid fa-check'" />
              {{ loadingMora ? (statusMora ? 'Desactivando...' : 'Activando...') : (statusMora ? 'Desactivar Mora' :
                'Activar Mora') }}
            </button>
          </div>
        </section>

        <div class="p-4 md:p-8 space-y-8">

          <div class="space-y-4">
            <div class="flex items-center gap-4">
              <MiniTitle :text="`Resumen Económico (Tasa: ${liveFinancing.interes_porcent}%)`" />
              <button v-if="isAdmin && !isEditingInterest" @click="startEditingFinance('interest')"
                class="text-indigo-600 hover:bg-indigo-50 p-1 rounded-md transition text-sm">
                <font-awesome-icon icon="fa-solid fa-pen" />
              </button>
              <div v-if="isAdmin && isEditingInterest" class="flex items-center gap-2">
                <input v-model="financeForm.interes_porcent" type="number" class="w-20 px-2 py-1 border rounded text-sm"
                  @input="handlePriceInput('interes_porcent', $event)" />
                <button @click="saveFinance('interest')"
                  class="text-green-600 hover:text-green-700 font-bold text-sm">✓</button>
                <button @click="isEditingInterest = false"
                  class="text-red-500 hover:text-red-700 font-bold text-sm">✕</button>
              </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
              <CardPrice label="Precio Vehículo" :content="parsePrices(liveFinancing.precio)" color="text-gray-700" />
              <CardPrice label="Pago Inicial" :content="parsePrices(liveFinancing.inicial)" color="text-blue-600" />

              <div class="relative group">
                <CardPrice v-if="!isEditingFinancingPrice" label="Monto a Financiar"
                  :content="parsePrices(liveFinancing.cost_inicial)" color="text-orange-600"
                  :show-edit="isAdmin && liveFinancing.estado === 'activa'"
                  @edit="startEditingFinance('financing_price')" />
                <div v-else class="bg-white p-3 py-4 rounded-2xl shadow-md text-center border border-indigo-300">
                  <p class="font-bold text-sm">Monto a Financiar</p>
                  <input v-model="financeForm.financing_price" type="text"
                    class="w-full text-xl font-bold mt-2 text-center text-orange-600 focus:outline-none"
                    @input="handlePriceInput('financing_price', $event)" />
                  <div class="flex justify-center gap-4 mt-2">
                    <button @click="saveFinance('financing_price')" class="text-green-600 font-bold">Guardar</button>
                    <button @click="isEditingFinancingPrice = false" class="text-red-500 font-bold">✕</button>
                  </div>
                </div>
              </div>

              <div class="relative group">
                <CardPrice v-if="!isEditingInteresPrice" label="Total Intereses"
                  :content="parsePrices(liveFinancing.intereses)" color="text-indigo-600"
                  :show-edit="isAdmin && liveFinancing.estado === 'activa'"
                  @edit="startEditingFinance('interes_price')" />
                <div v-else class="bg-white p-3 py-4 rounded-2xl shadow-md text-center border border-indigo-300">
                  <p class="font-bold text-sm">Total Intereses</p>
                  <input v-model="financeForm.interes_price" type="text"
                    class="w-full text-xl font-bold mt-2 text-center text-indigo-600 focus:outline-none"
                    @input="handlePriceInput('interes_price', $event)" />
                  <div class="flex justify-center gap-4 mt-2">
                    <button @click="saveFinance('interes_price')" class="text-green-600 font-bold">Guardar</button>
                    <button @click="isEditingInteresPrice = false" class="text-red-500 font-bold">✕</button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="space-y-4">
            <MiniTitle text="Estado del Crédito" />
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
              <CardPrice label="Costo Neto Total" :content="parsePrices(liveFinancing.precio_final)"
                color="text-red-600" />
              <CardPrice label="Total Pagado" class="!bg-emerald-600 !text-white"
                :content="parsePrices(liveFinancing.pagado)" />
              <CardPrice label="Saldo Faltante" class="!bg-rose-500 !text-white"
                :content="parsePrices(liveFinancing.faltante)" />
              <CardPrice label="Mora Acumulada" :content="parsePrices(liveFinancing.precio_mora)"
                color="text-amber-600" />
            </div>
          </div>

          <div class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
              <div />
              <CardPrice label="Deuda A Pagar" class="!bg-blue-600 !text-white"
                :content="parsePrices(liveFinancing.deudaPagar)" />
              <CardPrice label="Deuda Adquirida" class="!bg-red-500 !text-white"
                :content="parsePrices(liveFinancing.deudaAdquirida)" />
              <div />
            </div>
          </div>

          <div class="space-y-4">
            <div class="flex items-center gap-4">
              <MiniTitle text="Valores de Cuota Sugeridos" />
              <button v-if="isAdmin && !isEditingSuggested" @click="startEditingFinance('suggested')"
                class="text-indigo-600 hover:bg-indigo-50 p-1 rounded-md transition text-sm">
                <font-awesome-icon icon="fa-solid fa-pen" />
              </button>
            </div>

            <div v-if="isAdmin && isEditingSuggested"
              class="bg-indigo-50 p-4 rounded-2xl border border-indigo-200 mb-4 flex flex-col md:flex-row items-center gap-4 justify-between">
              <p class="text-sm font-bold text-indigo-800">Modificando valores sugeridos. Al terminar click en Guardar.
              </p>
              <div class="flex gap-2">
                <button @click="saveFinance('suggested')"
                  class="bg-indigo-600 text-white px-4 py-1 rounded-lg text-sm font-bold shadow-sm">Guardar
                  Todos</button>
                <button @click="isEditingSuggested = false"
                  class="bg-white text-gray-500 px-4 py-1 rounded-lg text-sm border font-bold">Cancelar</button>
              </div>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
              <div class="relative">
                <CardPrice v-if="!isEditingSuggested" label="Mensual" :content="parsePrices(liveFinancing.mensual)"
                  color="text-blue-700" />
                <div v-else class="bg-white p-3 py-4 rounded-2xl shadow-md text-center border border-indigo-200">
                  <p class="font-bold text-xs text-gray-500">Mensual</p>
                  <input v-model="financeForm.price_mensual" type="text"
                    class="w-full text-xl font-bold mt-2 text-center text-blue-700 outline-none"
                    @input="handlePriceInput('price_mensual', $event)" />
                </div>
              </div>

              <div class="relative">
                <CardPrice v-if="!isEditingSuggested" label="Quincenal" :content="parsePrices(liveFinancing.quincenal)"
                  color="text-orange-700" />
                <div v-else class="bg-white p-3 py-4 rounded-2xl shadow-md text-center border border-indigo-200">
                  <p class="font-bold text-xs text-gray-500">Quincenal</p>
                  <input v-model="financeForm.price_quincenal" type="text"
                    class="w-full text-xl font-bold mt-2 text-center text-orange-700 outline-none"
                    @input="handlePriceInput('price_quincenal', $event)" />
                </div>
              </div>

              <div class="relative">
                <CardPrice v-if="!isEditingSuggested" label="Semanal" :content="parsePrices(liveFinancing.semanal)"
                  color="text-blue-700" />
                <div v-else class="bg-white p-3 py-4 rounded-2xl shadow-md text-center border border-indigo-200">
                  <p class="font-bold text-xs text-gray-500">Semanal</p>
                  <input v-model="financeForm.price_semanal" type="text"
                    class="w-full text-xl font-bold mt-2 text-center text-blue-700 outline-none"
                    @input="handlePriceInput('price_semanal', $event)" />
                </div>
              </div>

              <div class="relative">
                <CardPrice v-if="!isEditingSuggested" label="Diario" :content="parsePrices(liveFinancing.diario)"
                  color="text-orange-700" />
                <div v-else class="bg-white p-3 py-4 rounded-2xl shadow-md text-center border border-indigo-200">
                  <p class="font-bold text-xs text-gray-500">Diario</p>
                  <input v-model="financeForm.price_diario" type="text"
                    class="w-full text-xl font-bold mt-2 text-center text-orange-700 outline-none"
                    @input="handlePriceInput('price_diario', $event)" />
                </div>
              </div>
            </div>
          </div>

          <div v-if="liveFinancing.services?.length" class="space-y-4">
            <div class="flex items-center gap-4">
              <MiniTitle text="Servicios Adicionales" />
              <button v-if="isAdmin && !isEditingServices" @click="startEditingServices"
                class="text-indigo-600 hover:bg-indigo-50 p-1 rounded-md transition text-sm">
                <font-awesome-icon icon="fa-solid fa-pen" />
              </button>
            </div>

            <div v-if="isAdmin && isEditingServices"
              class="bg-indigo-50 p-4 rounded-2xl border border-indigo-200 mb-4 flex flex-col md:flex-row items-center gap-4 justify-between">
              <p class="text-sm font-bold text-indigo-800">Selecciona los servicios incluidos y sus respectivos precios.
              </p>
              <div class="flex gap-2">
                <button @click="saveServices" :disabled="savingServices"
                  class="bg-indigo-600 text-white px-4 py-1 rounded-lg text-sm font-bold shadow-sm disabled:opacity-60">
                  {{ savingServices ? 'Guardando...' : 'Guardar Servicios' }}
                </button>
                <button @click="isEditingServices = false"
                  class="bg-white text-gray-500 px-4 py-1 rounded-lg text-sm border font-bold">Cancelar</button>
              </div>
            </div>

            <div v-if="!isEditingServices" class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
              <CardPrice v-for="service in liveFinancing.services" :key="service.id" :label="service.name"
                :content="parsePrices(service.price)" :color="service.is_included ? 'text-orange-600' : 'text-gray-300'"
                :class="!service.is_included && 'opacity-60 bg-gray-100/50'" />
            </div>

            <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
              <div v-for="service in servicesForm" :key="service.id"
                class="bg-white p-4 rounded-2xl border transition-all"
                :class="service.is_included ? 'border-indigo-300 shadow-md' : 'border-gray-200 opacity-70'">

                <div class="flex justify-between items-center mb-3">
                  <p class="font-bold text-xs uppercase text-gray-500">{{ service.name }}</p>
                  <button @click="toggleService(service.id)"
                    class="w-6 h-6 rounded-full flex items-center justify-center transition-colors"
                    :class="service.is_included ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400'">
                    <font-awesome-icon :icon="service.is_included ? 'fa-solid fa-check' : 'fa-solid fa-times'"
                      class="text-[10px]" />
                  </button>
                </div>

                <div v-if="service.is_included" class="space-y-1">
                  <p class="text-[10px] text-gray-400 font-bold uppercase">Precio</p>
                  <input v-model="service.price" type="text"
                    class="w-full text-lg font-bold text-indigo-600 bg-transparent border-b border-indigo-100 focus:border-indigo-400 outline-none"
                    @input="service.price = FormatCurrency(($event.target as any).value)" />
                </div>
                <div v-else class="flex items-center justify-center h-10 italic text-gray-400 text-xs text-center">
                  Servicio no incluido
                </div>
              </div>
            </div>
          </div>

          <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <aside class="space-y-6">
              <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-200">
                <div class="flex justify-between items-center mb-6">
                  <h3 class="text-lg font-bold text-gray-800">📄 Información</h3>
                  <router-link v-if="isAdmin" :to="{ name: 'financing-form', params: { id: props.financing.id } }"
                    class="text-indigo-600 hover:bg-indigo-50 p-2 rounded-lg transition">
                    <font-awesome-icon icon="fa-regular fa-pen-to-square" />
                  </router-link>
                </div>


                <div class="space-y-4">
                  <div class="grid grid-cols-1 gap-4">
                    <div class="bg-gray-50 p-4 rounded-xl flex flex-col items-center md:items-start">
                      <p class="text-md text-gray-400 mb-2 text-center mx-auto">Cliente</p>

                      <div class="flex flex-col md:flex-row items-center gap-4 w-full">
                        <div v-if="props.financing?.cliente_archivo"
                          class="w-24 h-24 md:w-24 md:h-24 shrink-0 overflow-hidden rounded-full border-2 border-white shadow-sm bg-gray-200">
                          <ViewFile :path="props.financing.cliente_archivo" class="w-full h-full object-cover" />
                        </div>
                        <div class="text-center md:text-left">
                          <p class="font-bold text-gray-900 leading-tight">{{ formatValue(props.financing?.cliente) }}
                          </p>
                          <p class="text-sm text-gray-500 font-medium">{{ formatValue(props.financing?.cliente_ci) }}
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="grid grid-cols-3 gap-4">
                    <div>
                      <div class="flex items-center gap-2">
                        <p class="text-xs text-gray-400 uppercase font-bold">Plan</p>
                        <button v-if="isAdmin && !isEditingPlan" @click="startEditingFinance('plan')"
                          class="text-indigo-400 hover:text-indigo-600 transition p-1">
                          <font-awesome-icon icon="fa-solid fa-pen" class="text-[10px]" />
                        </button>
                      </div>

                      <div v-if="isEditingPlan" class="flex flex-col gap-1">
                        <select v-model="financeForm.plan" @change="reCalculatePrices()"
                          class="w-full text-sm font-bold text-indigo-600 bg-transparent border-b border-indigo-100 focus:border-indigo-400 outline-none">
                          <option value="Diario">Diario</option>
                          <option value="Semanal">Semanal</option>
                          <option value="Quincenal">Quincenal</option>
                          <option value="Mensual">Mensual</option>
                        </select>
                        <div class="flex gap-2 mt-1">
                          <button @click="saveFinance()" :disabled="savingFinance"
                            class="text-[10px] font-bold text-green-600">Guardar</button>
                          <button @click="isEditingPlan = false"
                            class="text-[10px] font-bold text-gray-400">Cancelar</button>
                        </div>
                      </div>
                      <p v-else class="font-medium text-gray-800">{{ formatValue(liveFinancing.plan) }}</p>
                    </div>
                    <div>
                      <p class="text-xs text-gray-400 uppercase font-bold">Meses</p>
                      <div v-if="!isEditingInstallments" class="flex items-center gap-2">
                        <p class="font-medium text-gray-800">{{ formatValue(liveFinancing.meses) }}</p>
                        <button v-if="isAdmin" @click="startEditingFinance('installments')"
                          class="text-gray-400 hover:text-indigo-600 transition-colors">
                          <font-awesome-icon icon="fa-solid fa-pen" class="text-[10px]" />
                        </button>
                      </div>
                      <div v-if="isAdmin && isEditingInstallments" class="flex items-center gap-1">
                        <input v-model="financeForm.meses" type="number" class="w-12 border rounded text-xs px-1"
                          @input="reCalculatePrices()" />
                        <button @click="saveFinance('installments')" class="text-green-600 font-bold text-xs">✓</button>
                        <button @click="isEditingInstallments = false" class="text-red-500 font-bold text-xs">✕</button>
                      </div>
                    </div>
                    <div>
                      <p class="text-xs text-gray-400 uppercase font-bold">Cuotas</p>
                      <div v-if="!isEditingInstallments" class="flex items-center gap-2">
                        <p class="font-medium text-gray-800">{{ formatValue(liveFinancing.cuotas) }}</p>
                      </div>
                      <div v-else class="flex items-center gap-1">
                        <input v-model="financeForm.cuotas" type="number" class="w-16 border rounded text-xs px-1"
                          @input="reCalculatePrices('cuotas')" />
                      </div>
                    </div>
                    <div>
                      <p class="text-xs text-gray-400 uppercase font-bold">Tipo</p>
                      <p class="font-medium text-gray-800 capitalize">{{ formatValue(liveFinancing.tipo) }}</p>
                    </div>
                    <div>
                      <p class="text-xs text-gray-400 uppercase font-bold">Fecha Inicio</p>
                      <p class="font-medium text-gray-800">{{ formatValue(liveFinancing.fecha_inicio) }}</p>
                    </div>

                    <div v-if="props.financing.observacion" class="md:col-span-3">
                      <p class="text-xs text-gray-400 uppercase font-bold">Detalles</p>
                      <p class="font-medium text-gray-800">{{ props.financing?.observacion }}</p>
                    </div>
                  </div>
                </div>
                <div v-if="props.financing?.tipo === 'Vehículo'" class="mt-6 pt-6 border-t border-gray-100">
                  <h4 class="font-bold text-gray-800 mb-4">Vehículo Asociado</h4>
                  <div class="flex flex-col gap-4">
                    <img v-if="props.financing?.image_vehiculo" :src="props.financing.image_vehiculo"
                      class="w-full h-40 object-cover rounded-xl border border-gray-100 shadow-inner" />
                    <div class="grid grid-cols-3 gap-2 text-center text-sm">
                      <div class="bg-gray-50 p-2 rounded-lg">
                        <p class="text-[10px] text-gray-400 uppercase font-bold">Marca</p>
                        <p class="font-bold">{{ props.financing?.vehiculo_marca }}</p>
                      </div>
                      <div class="bg-gray-50 p-2 rounded-lg">
                        <p class="text-[10px] text-gray-400 uppercase font-bold">Modelo</p>
                        <p class="font-bold">{{ props.financing?.vehiculo_model }}</p>
                      </div>
                      <div class="bg-gray-50 p-2 rounded-lg relative group">
                        <p class="text-[10px] text-gray-400 uppercase font-bold">Placa</p>
                        <div v-if="!isEditingPlaca" class="flex items-center justify-center gap-2">
                          <p class="font-bold">{{ props.financing?.vehiculo_placa }}</p>
                          <button v-if="isAdmin && props.financing.estado == 'activa'" @click="openPlacaModal"
                            class="text-gray-400 hover:text-indigo-600 transition-colors" title="Actualizar placa">
                            <font-awesome-icon icon="fa-solid fa-pen" class="text-xs" />
                          </button>
                        </div>
                        <div v-if="isAdmin && isEditingPlaca" class="flex items-center justify-center gap-1 mt-1">
                          <input v-model="newPlaca" type="text"
                            class="w-full min-w-0 text-center font-bold text-sm px-1 py-1 border border-gray-300 rounded focus:outline-none focus:border-indigo-500 bg-white uppercase"
                            @keyup.enter="updatePlaca" />
                          <button @click="updatePlaca" :disabled="savingPlaca"
                            class="text-green-600 hover:text-green-700 disabled:opacity-50 p-1">
                            <font-awesome-icon v-if="savingPlaca" icon="fa-solid fa-spinner" class="fa-spin text-xs" />
                            <font-awesome-icon v-else icon="fa-solid fa-check" class="text-xs" />
                          </button>
                          <button @click="cancelEditPlaca" :disabled="savingPlaca"
                            class="text-red-500 hover:text-red-700 disabled:opacity-50 p-1">
                            <font-awesome-icon icon="fa-solid fa-times" class="text-xs" />
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </aside>

            <div
              class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden flex flex-col">
              <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-white">
                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                  💳 Historial de Pagos
                </h3>
                <span class="bg-indigo-100 text-indigo-700 text-xs font-bold px-3 py-1 rounded-full">
                  {{ props.financing?.payments?.length || 0 }} REGISTROS
                </span>
              </div>

              <div class="overflow-x-auto overflow-y-auto max-h-[600px]">
                <table class="w-full text-left border-collapse">
                  <thead class="bg-gray-50 sticky top-0 z-10">
                    <tr class="text-[11px] uppercase tracking-wider text-gray-500 font-bold border-b border-gray-200">
                      <th class="p-4"># Cuota</th>
                      <th class="p-4">Tipo</th>
                      <th class="p-4">Capital</th>
                      <th class="p-4">Interés</th>
                      <th class="p-4">%</th>
                      <th class="p-4">Total</th>
                      <th class="p-4">Estado</th>
                      <th class="p-4">Fecha</th>
                      <th class="p-4">Doc</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-100">
                    <tr v-for="pago in props.financing.payments.sort((a, b) => a.nro_cuota - b.nro_cuota)"
                      class="hover:bg-indigo-50/30 transition-colors">
                      <td class="p-4">
                        <span v-if="!pago.mora_index"
                          :class="pago.nro_cuota == 0 ? 'bg-green-100 text-green-700 px-2 py-1 rounded-md font-bold text-xs' : 'font-semibold text-gray-700'">
                          {{ pago.nro_cuota == 0 ? 'INICIAL' : 'N° ' + pago.nro_cuota }}
                        </span>
                        <span v-else class="text-gray-300">—</span>
                      </td>
                      <td class="p-4">
                        <span v-if="pago.mora_index"
                          class="bg-rose-100 text-rose-700 text-[10px] font-black px-2 py-1 rounded-md uppercase">
                          Mora
                        </span>
                        <span v-else
                          class="bg-indigo-100 text-indigo-700 text-[10px] font-black px-2 py-1 rounded-md uppercase">
                          Cuota
                        </span>
                      </td>
                      <td class="p-4 font-bold text-gray-700 text-[11px]">{{ parsePrices(pago.total_capital || 0) }}
                      </td>
                      <td class="p-4 font-bold text-gray-500 text-[11px]">{{ parsePrices(pago.total_interes || 0) }}
                      </td>
                      <td class="p-4 text-gray-500 text-[11px]">{{ pago.interes_porcent || 0 }}%</td>
                      <td class="p-4 font-bold text-gray-900 text-sm">{{ parsePrices(pago.total) }}</td>
                      <td class="p-4">
                        <span :class="{
                          'bg-amber-100 text-amber-700': pago.status === 'pendiente',
                          'bg-emerald-100 text-emerald-700': pago.status === 'aprobado',
                          'bg-rose-100 text-rose-700': pago.status === 'rechazado'
                        }" class="text-[10px] font-black px-2 py-1 rounded-md uppercase">
                          {{ pago.status }}
                        </span>
                      </td>
                      <td class="p-4 text-gray-500 text-xs font-medium">{{ formatDateFn(pago.date) }}</td>
                      <td class="p-4">
                        <button v-if="pago.archivo" @click="docFile = pago.archivo"
                          class="bg-blue-50 text-blue-600 p-2 rounded-lg hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                          <font-awesome-icon icon="fa-solid fa-eye" />
                        </button>
                        <span v-else class="text-gray-300">—</span>
                      </td>
                    </tr>
                  </tbody>
                </table>

                <div v-if="!props.financing?.payments?.length"
                  class="flex flex-col items-center justify-center p-12 text-gray-400">
                  <font-awesome-icon icon="fa-solid fa-receipt" class="text-4xl mb-4 opacity-20" />
                  <p class="font-medium">No se han registrado pagos para este crédito.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Requirements Modal -->
    <div v-if="showRequirementsModal"
      class="fixed inset-0 z-[2001] bg-black/70 backdrop-blur-md flex items-center justify-center p-2 md:p-4"
      @click.self="showRequirementsModal = false">
      <div
        class="bg-white rounded-3xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden animate-fade-in border border-gray-100">
        <div class="flex justify-end p-4 border-b border-gray-100">
          <button @click="showRequirementsModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
            <span class="text-2xl">✕</span>
          </button>
        </div>
        <div class="overflow-y-auto w-full">
          <RequirementsModal @close="showRequirementsModal = false" @submit="handleUpdateRequirements" />
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
/* Transición suave para el modal */
.animate-fade-in {
  animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Scrollbar personalizado para la tabla */
::-webkit-scrollbar {
  width: 6px;
  height: 6px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
  background: #d1d1d1;
  border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
  background: #a1a1a1;
}
</style>