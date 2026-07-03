<script setup lang="ts">
import { reactive } from "vue"
import type { AccountMethod } from "./useAccountMethod"
import type { Error } from "@/types/Error"
import useAccountMethodStepForm from "./useAccountMethodStepForm"

const props = defineProps<{
  id?: string | number
  method: AccountMethod  
  sending: boolean
  errors: Error | undefined
}>()

const emit = defineEmits<{
  (e: 'submit', methodData: AccountMethod, id?: string | number): void
}>()

const form: AccountMethod = reactive(props.method)

const {
    currentStep,
    totalSteps,
    isStep1Valid,
    isStep2Valid,
    nextStep,
    prevStep
} = useAccountMethodStepForm(form)

const submit = async () => {
  if (!isStep1Valid.value || !isStep2Valid.value) return;
  emit('submit', form, props.id)  
}

</script>

<template>
  <div class="w-full mx-auto">
    
    <div class="mb-8">
      <div class="flex items-center justify-between relative">
        <div class="absolute left-0 top-1/2 transform -translate-y-1/2 w-full h-1 bg-gray-200 z-0 rounded-full"></div>
        <div class="absolute left-0 top-1/2 transform -translate-y-1/2 h-1 bg-gradient-to-r from-[#FF7539] to-[#F15A24] z-0 rounded-full transition-all duration-500 ease-in-out" :style="{ width: ((currentStep - 1) / (totalSteps - 1)) * 100 + '%' }"></div>
        
        <div v-for="step in totalSteps" :key="step" class="relative z-10 flex flex-col items-center">
            <div 
              class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all duration-300 border-2"
              :class="[
                  step < currentStep ? 'bg-[#FF7539] text-white border-[#FF7539]' : 
                  step === currentStep ? 'bg-white text-[#FF7539] border-[#FF7539] shadow-md ring-4 ring-[#FF7539] ring-opacity-20' : 
                  'bg-white text-gray-400 border-gray-300'
              ]"
            >
              <font-awesome-icon v-if="step < currentStep" icon="check" />
              <span v-else>{{ step }}</span>
            </div>
            <span class="mt-2 text-xs font-semibold hidden sm:block" 
                :class="step <= currentStep ? 'text-gray-800' : 'text-gray-400'">
              <span v-if="step === 1">Entidad</span>
              <span v-else-if="step === 2">Titular</span>
              <span v-else>Configuración</span>
            </span>
        </div>
      </div>
    </div>

    <form @submit.prevent="submit">    
      
      <transition name="slide-fade" mode="out-in">
        <div v-if="currentStep === 1" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6 min-h-[300px]">
          <h3 class="text-xl font-semibold text-gray-800 mb-2 flex items-center gap-2">
            <font-awesome-icon icon="building-columns" class="text-[#FF7539]"/>
            Detalles de la Entidad
          </h3>
          <p class="text-sm text-gray-500 mb-6 border-b pb-4">Define la plataforma o banco donde se recibirán o manejarán los pagos.</p>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">          
            <label class="block">
              <span class="text-sm font-medium text-gray-700">Nombre del Proveedor <span class="text-red-500">*</span></span>
              <input v-model="form.provider_name" type="text" class="mt-2 block w-full rounded-xl border-gray-200 shadow-sm focus:border-[#FF7539] focus:ring focus:ring-[#FF7539] focus:ring-opacity-20 transition-all bg-gray-50 hover:bg-white px-4 py-3" required placeholder="Ej: Bancolombia" />
            </label>          

            <label class="block">
              <span class="text-sm font-medium text-gray-700">Identificador (Cuenta / Correo / Teléfono) <span class="text-red-500">*</span></span>
              <input v-model="form.identifier" type="text" class="mt-2 block w-full rounded-xl border-gray-200 shadow-sm focus:border-[#FF7539] focus:ring focus:ring-[#FF7539] focus:ring-opacity-20 transition-all bg-gray-50 hover:bg-white px-4 py-3" required placeholder="Ej: 123456789" />
            </label>
            
            <label class="block">
              <span class="text-sm font-medium text-gray-700">Tipo de Entidad <span class="text-red-500">*</span></span>
              <select v-model="form.type" class="mt-2 block w-full rounded-xl border-gray-200 shadow-sm focus:border-[#FF7539] focus:ring focus:ring-[#FF7539] focus:ring-opacity-20 transition-all bg-gray-50 hover:bg-white px-4 py-3" required>
                  <option value="bank">Banco</option>
                  <option value="wallet">Billetera Electrónica</option>
                  <option value="crypto">Criptomonedas</option>
                  <option value="other">Otro</option>
              </select>
            </label>
          </div>
        </div>
      </transition>

      <transition name="slide-fade" mode="out-in">
        <div v-if="currentStep === 2" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6 min-h-[300px]">
          <h3 class="text-xl font-semibold text-gray-800 mb-2 flex items-center gap-2">
            <font-awesome-icon icon="user-tag" class="text-[#FF7539]"/>
            Información del Titular
          </h3>
          <p class="text-sm text-gray-500 mb-6 border-b pb-4">Detalles de la persona o entidad a nombre de la cuenta para validación externa.</p>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <label class="block">
              <span class="text-sm font-medium text-gray-700">Nombre del Titular <span class="text-red-500">*</span></span>
              <input v-model="form.holder_name" type="text" class="mt-2 block w-full rounded-xl border-gray-200 shadow-sm focus:border-[#FF7539] focus:ring focus:ring-[#FF7539] focus:ring-opacity-20 transition-all bg-gray-50 hover:bg-white px-4 py-3" required placeholder="Ej: Juan Perez" />
            </label>
            
            <label class="block">
              <span class="text-sm font-medium text-gray-700">DNI del Titular <span class="text-gray-400 font-normal ml-1">(Opcional)</span></span>
              <input v-model="form.holder_dni" type="text" class="mt-2 block w-full rounded-xl border-gray-200 shadow-sm focus:border-[#FF7539] focus:ring focus:ring-[#FF7539] focus:ring-opacity-20 transition-all bg-gray-50 hover:bg-white px-4 py-3" placeholder="Cédula o NIT si aplica" />
            </label>

            <label class="block md:col-span-2">
              <span class="text-sm font-medium text-gray-700">Tipo o Red <span class="text-gray-400 font-normal ml-1">(Ej: Ahorros, TRC20, Nequi)</span></span>
              <input v-model="form.network_or_type" type="text" class="mt-2 block w-full rounded-xl border-gray-200 shadow-sm focus:border-[#FF7539] focus:ring focus:ring-[#FF7539] focus:ring-opacity-20 transition-all bg-gray-50 hover:bg-white px-4 py-3" placeholder="Ej: Cuenta de Ahorros" />
            </label>
          </div>
        </div>
      </transition>

      <transition name="slide-fade" mode="out-in">
        <div v-if="currentStep === 3" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8 min-h-[300px]">
          <h3 class="text-xl font-semibold text-gray-800 mb-2 flex items-center gap-2">
            <font-awesome-icon icon="gear" class="text-[#FF7539]"/>
            Configuración Adicional
          </h3>
          <p class="text-sm text-gray-500 mb-6 border-b pb-4">Ajustes finales e instrucciones complementarias.</p>

          <div class="mb-6">
            <label class="inline-flex items-center cursor-pointer bg-gray-50 px-5 py-4 rounded-xl border border-gray-200 hover:bg-white hover:shadow-sm transition-all w-full sm:w-auto">
              <div class="relative">
                <input type="checkbox" v-model="form.is_active" class="sr-only" />
                <div class="block w-12 h-6 bg-gray-300 rounded-full transition-colors duration-300" :class="{'!bg-green-500': form.is_active}"></div>
                <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform duration-300" :class="{'transform translate-x-6': form.is_active}"></div>
              </div>
              <div class="ml-4">
                <span class="text-sm font-semibold text-gray-700">Cuenta Activa</span>
                <p class="text-xs text-gray-500 font-normal mt-0.5" v-if="form.is_active">La cuenta está lista para ser usada en pagos.</p>
                <p class="text-xs text-gray-500 font-normal mt-0.5" v-else>Si está inactiva, no se mostrará como opción de pago.</p>
              </div>
            </label>
          </div>

          <div>
            <label class="block">
              <span class="text-sm font-medium text-gray-700">Notas Adicionales <span class="text-gray-400 font-normal ml-1">(Opcional)</span></span>
              <textarea v-model="form.notes" rows="4" class="mt-2 block w-full rounded-xl border-gray-200 shadow-sm focus:border-[#FF7539] focus:ring focus:ring-[#FF7539] focus:ring-opacity-20 transition-all bg-gray-50 hover:bg-white px-4 py-3" placeholder="Instrucciones especiales para transferencias, referencias o información interna..."></textarea>
            </label>
          </div>
        </div>
      </transition>

      <div class="flex items-center" :class="currentStep > 1 ? 'justify-between' : 'justify-end'">
        
        <button 
          v-if="currentStep > 1" 
          @click.prevent="prevStep" 
          type="button" 
          class="bg-white border-2 border-gray-200 text-gray-700 hover:bg-gray-50 hover:text-gray-900 shadow-sm hover:shadow transition-all font-bold px-8 py-3 rounded-full flex items-center justify-center gap-2"
        >
          <font-awesome-icon icon="arrow-left" />
          Atrás
        </button>

        <button 
          v-if="currentStep < totalSteps" 
          @click.prevent="nextStep" 
          type="button" 
          :disabled="(currentStep === 1 && !isStep1Valid) || (currentStep === 2 && !isStep2Valid)"
          class="bg-[#FF7539] hover:bg-[#E65C20] shadow-md hover:shadow-lg transition-transform hover:-translate-y-0.5 active:translate-y-0 text-white font-bold px-8 py-3 rounded-full flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
        >
          Continuar
          <font-awesome-icon icon="arrow-right" />
        </button>

        <button 
          v-if="currentStep === totalSteps"
          :disabled='sending' 
          type="submit" 
          class="bg-gradient-to-r from-[#FF7539] to-[#F15A24] hover:from-[#E65C20] hover:to-[#E0440C] shadow-md hover:shadow-lg transition-transform hover:-translate-y-0.5 active:translate-y-0 text-white font-bold px-10 py-3 rounded-full flex items-center justify-center gap-3 disabled:opacity-70 disabled:cursor-not-allowed disabled:transform-none"
        >
            <font-awesome-icon v-if="!sending" icon="save" class="text-lg" />
            <font-awesome-icon v-else icon="spinner" class="animate-spin text-lg" />
            <span class="tracking-wide">{{sending ? 'Procesando...' : (id ? 'Actualizar Método' : 'Guardar Método')}}</span>
        </button>
      </div>
    </form>  
  </div>
</template>

<style scoped>
.dot {
  transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.slide-fade-enter-active {
  transition: all 0.4s ease-out;
}

.slide-fade-leave-active {
  transition: all 0.2s cubic-bezier(1, 0.5, 0.8, 1);
}

.slide-fade-enter-from {
  transform: translateX(20px);
  opacity: 0;
}

.slide-fade-leave-to {
  transform: translateX(-20px);
  opacity: 0;
}
</style>
