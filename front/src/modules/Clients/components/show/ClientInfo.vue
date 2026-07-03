<script setup lang="ts">
import { ref, onMounted } from "vue";
import { library } from '@fortawesome/fontawesome-svg-core'
import { faUser, faPhone, faEnvelope, faIdCard, faCalendar, faHouse, faDollarSign, faIdBadge } from '@fortawesome/free-solid-svg-icons'
import clientservice from "@/modules/Clients/services";
import { alertWithToast } from "@/utils/toast";
import { useRoute, useRouter } from "vue-router";
import useHttp from "@/composables/useHttp";
import Loader from "@/components/Loader.vue";
import ViewFile from "@/components/ViewFile.vue";
import { FormatCurrency } from "@/utils/formatCurrency";
import type User from "../../types/User";

library.add(faUser, faPhone, faEnvelope, faIdCard, faCalendar, faHouse, faDollarSign, faIdBadge)

const cliente = ref<User>({
  name: null,
  email: null,
  usuario: null,
  phone: null,
  cedula: null,
  dateN: null,
  dir: null,
  earnings: null,
  imageApp: null,
});

const router = useRouter();
const route = useRoute();

const {
  loading,
} = useHttp()

const url = import.meta.env.VITE_APP_API_URL

const getUser = async () => {
  loading.value = true
  try {
    const response = await clientservice.getClient(route.params.id as string, '')
    cliente.value.name = response.data.name
    cliente.value.email = response.data.email
    cliente.value.phone = response.data.phone
    cliente.value.cedula = response.data.cedula
    cliente.value.dateN = response.data.dateN
    cliente.value.dir = response.data.dir
    cliente.value.earnings = FormatCurrency(response.data.earnings)
    cliente.value.imageApp = response.data.imageApp
  } catch (error) {
    router.push('/clients').then(() => {
      if (error?.status === 404) {
        alertWithToast('Usuario no encontrado', 'error')
      } else {
        alertWithToast('Ocurrio un error, consulte a soporte', 'error')
      }
    })
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  getUser()
});
</script>

<style scoped>
.w-5.h-5 {
  font-size: 1.25rem;
}
</style>

<template>
  <aside
    class="w-full h-full bg-white dark:bg-gray-800 overflow-y-auto overflow-x-hidden md:rounded-tr-[3rem] border-r border-gray-200 dark:border-gray-700 shadow-2xl relative flex flex-col custom-scrollbar">
    <Loader v-if="loading" class="m-auto" />

    <div v-else class="flex flex-col h-full opacity-0 animate-fade-in relative" style="animation-fill-mode: forwards;">
      <div class="h-32 w-full bg-[#FF7539] relative shrink-0">
        <div class="absolute inset-0 bg-black/10"></div>
      </div>

      <div class="flex flex-col items-center px-6 pb-6 relative -mt-16 shrink-0">
        <div
          class="w-32 h-32 rounded-full overflow-hidden border-4 border-white dark:border-gray-800 shadow-xl mb-4 bg-white dark:bg-gray-800 relative z-10 flex items-center justify-center">
          <ViewFile v-if="cliente.imageApp" :path="cliente.imageApp" class="w-full h-full object-cover" />
          <div v-else class="w-full h-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-400">
            <font-awesome-icon :icon="['fas', 'user']" class="text-5xl" />
          </div>
        </div>

        <h3 class="text-2xl font-black text-gray-800 dark:text-white text-center leading-tight">{{ cliente.name ?? 'Sin Nombre' }}</h3>
        <span
          class="mt-2 px-4 py-1.5 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-300 rounded-full text-xs font-bold tracking-widest uppercase shadow-sm">
          CÉDULA: {{ cliente.cedula ?? 'N/A' }}
        </span>
      </div>

      <div class="px-6 pb-8 flex-1 space-y-3">
        <div
          class="flex items-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl hover:bg-[#ff814b]/5 dark:hover:bg-gray-700 transition-colors border border-transparent hover:border-[#ff814b]/20 group">
          <div
            class="w-10 h-10 rounded-full bg-white dark:bg-gray-800 flex items-center justify-center text-[#ff814b] shadow-sm mr-4 shrink-0 group-hover:scale-110 transition-transform">
            <font-awesome-icon :icon="['fas', 'phone']" />
          </div>
          <div class="flex flex-col min-w-0">
            <span class="text-[0.65rem] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Teléfono</span>
            <span class="text-sm font-bold text-gray-800 dark:text-gray-100 truncate">{{ cliente.phone ?? 'N/A'
              }}</span>
          </div>
        </div>

        <div
          class="flex items-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl hover:bg-[#ff814b]/5 dark:hover:bg-gray-700 transition-colors border border-transparent hover:border-[#ff814b]/20 group">
          <div
            class="w-10 h-10 rounded-full bg-white dark:bg-gray-800 flex items-center justify-center text-[#ff814b] shadow-sm mr-4 shrink-0 group-hover:scale-110 transition-transform">
            <font-awesome-icon :icon="['fas', 'envelope']" />
          </div>
          <div class="flex flex-col min-w-0">
            <span class="text-[0.65rem] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Correo</span>
            <span class="text-sm font-bold text-gray-800 dark:text-gray-100 truncate" :title="cliente.email ?? 'N/A'">{{
              cliente.email ?? 'N/A' }}</span>
          </div>
        </div>

        <!-- Item Date -->
        <div
          class="flex items-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl hover:bg-[#ff814b]/5 dark:hover:bg-gray-700 transition-colors border border-transparent hover:border-[#ff814b]/20 group">
          <div
            class="w-10 h-10 rounded-full bg-white dark:bg-gray-800 flex items-center justify-center text-[#ff814b] shadow-sm mr-4 shrink-0 group-hover:scale-110 transition-transform">
            <font-awesome-icon :icon="['fas', 'calendar']" />
          </div>
          <div class="flex flex-col min-w-0">
            <span class="text-[0.65rem] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Fecha Nacimiento</span>
            <span class="text-sm font-bold text-gray-800 dark:text-gray-100 truncate">{{ cliente.dateN ?? 'N/A'
              }}</span>
          </div>
        </div>

        <!-- Item Address -->
        <div
          class="flex items-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl hover:bg-[#ff814b]/5 dark:hover:bg-gray-700 transition-colors border border-transparent hover:border-[#ff814b]/20 group">
          <div
            class="w-10 h-10 rounded-full bg-white dark:bg-gray-800 flex items-center justify-center text-[#ff814b] shadow-sm mr-4 shrink-0 group-hover:scale-110 transition-transform">
            <font-awesome-icon :icon="['fas', 'house']" />
          </div>
          <div class="flex flex-col min-w-0">
            <span class="text-[0.65rem] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Dirección</span>
            <span class="text-sm font-bold text-gray-800 dark:text-gray-100 truncate" :title="cliente.dir ?? 'N/A'">{{
              cliente.dir ?? 'N/A' }}</span>
          </div>
        </div>

        <!-- Item Earnings -->
        <div
          class="flex items-center p-4 mt-2 bg-gradient-to-r from-[#ff814b]/10 to-transparent dark:from-gray-700/80 rounded-xl border border-[#ff814b]/30 relative overflow-hidden group">
          <div
            class="absolute -right-4 -top-4 text-[#ff814b] opacity-5 text-7xl rotate-12 group-hover:scale-110 transition-transform">
            <font-awesome-icon :icon="['fas', 'dollar-sign']" />
          </div>
          <div
            class="w-12 h-12 rounded-full bg-white dark:bg-gray-800 flex items-center justify-center text-[#ff814b] shadow-md mr-4 shrink-0 z-10 group-hover:-translate-y-1 transition-transform">
            <font-awesome-icon :icon="['fas', 'dollar-sign']" class="text-xl" />
          </div>
          <div class="flex flex-col min-w-0 z-10 block">
            <span class="text-[0.7rem] text-[#ff814b] font-black uppercase tracking-wider mb-0.5">Ingresos
              Mensuales</span>
            <span class="text-xl font-black text-gray-800 dark:text-white truncate">${{ cliente.earnings ?? 'N/A'
              }}</span>
          </div>
        </div>
      </div>
    </div>
  </aside>
</template>