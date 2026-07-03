<script setup lang="ts">
import { reactive, onMounted, ref, watch } from "vue"
import MaintenanceService from '@/modules/Maintenance/services'
import ClientMaintenanceListCard from './ClientMaintenanceListCard.vue'
import CompletedMantenece from '@/modules/Maintenance/components/CompletedMantenece.vue'
import Loader from '@/components/Loader.vue'
import useTableGrid from "@/composables/useTableGrid"
import { useRoute, useRouter } from "vue-router"
import { alertWithToast } from "@/utils/toast"
import { questionSweet } from "@/utils/question"
import { useAuthStore } from "@/modules/Auth/stores"

const route = useRoute()
const router = useRouter()
const store = useAuthStore()

const currentType = ref('pending')
const loaded = ref(true)
const mantenenceToAccept = ref('0')

const data = reactive({
    rows: <any[]>[],
    links: [],
    page: "1",
    search: "",
    sort: "",
    direction: "",
    offset: 0
})

const getScroll = () => MaintenanceService.get(`user=${route.params.id}&offset=${data.offset}&status=${currentType.value}`)

const {
    loadScroll,
} = useTableGrid(data, getScroll)

const getData = async () => {
    loaded.value = true
    try {
        const response = await MaintenanceService.get(`user=${route.params.id}&offset=0&status=${currentType.value}`)
        data.rows = response.data.rows
        data.offset = 10
    } catch(err) {
        console.error(err)
    } finally {
        loaded.value = false
    }
}

const deleteMantenience = async (id: string) => {
    const confirm = await questionSweet(
        '¿Estás seguro?',
        `Vas a eliminar este mantenimiento".`,
        'warning',
    )
    if (!confirm) return
    try {
        const response = await MaintenanceService.destroy(id);
        alertWithToast(response.data.message || "Correcto", "success");
        data.rows = data.rows.filter((e: any) => e.id != id)
    } catch (error: any) {
        alertWithToast(error?.message || "Ocurrió un error al anexar el documento", "error");
    }
}

const changeStatus = async (id: string, status: string) => {
    if (status !== 'completed') return mantenenceToAccept.value = id;
    await manteneceChnageStatus(id);
}

const acceptMantenence = async (formData: any) => {
    await manteneceChnageStatus(mantenenceToAccept.value, formData);
    mantenenceToAccept.value = '0';
}

const manteneceChnageStatus = async (id: string, payload?: any) => {
    const confirm = await questionSweet(
        '',
        `¿Seguro que quieres cambiar el estado de este mantenimiento?`,
        'warning',
    )
    if (!confirm) return
    try {
        const response = await MaintenanceService.toggleStatus(id, payload);
        alertWithToast(response.data.message || "Correcto", "success");
        await getData()
    } catch (error: any) {
        alertWithToast(error?.message || "Ocurrió un error al cambiar el estado", "error");
    }
}

watch(currentType, () => {
    getData()
})

onMounted(() => {
    getData()
})

</script>

<template>
    <div class="px-5">
        <CompletedMantenece v-if="mantenenceToAccept != '0'" @close="mantenenceToAccept = '0'" @submit="acceptMantenence"/>

        <div class="flex gap-5 justify-between my-6 md:w-[90%] mx-auto">
            <button @click="currentType = 'pending'" :class="[
                'block w-full px-5 py-2 font-bold rounded-full shadow-md transition-all duration-300',
                currentType === 'pending'
                ? 'bg-[#FF7539] text-white hover:text-[#FF7539] hover:bg-white'
                : 'text-[#FF7539] bg-white hover:bg-[#FF7539] hover:text-white'
            ]">
                Pendientes
            </button>

            <button @click="currentType = 'checking'" :class="[
                'block w-full px-5 py-2 font-bold rounded-full shadow-md transition-all duration-300',
                currentType === 'checking'
                ? 'bg-[#FF7539] text-white hover:text-[#FF7539] hover:bg-white'
                : 'text-[#FF7539] bg-white hover:bg-[#FF7539] hover:text-white'
            ]">
                Por revisar
            </button>

            <button @click="currentType = 'completed'" :class="[
                'block w-full px-5 py-2 font-bold rounded-full shadow-md transition-all duration-300',
                currentType === 'completed'
                ? 'bg-[#FF7539] text-white hover:text-[#FF7539] hover:bg-white'
                : 'text-[#FF7539] bg-white hover:bg-[#FF7539] hover:text-white'
            ]">
                Completados
            </button>
        </div>

        <section class="relative mx-auto my-2 overflow-auto">
            <div class="md:w-[90%] mx-auto h-[60vh] py-4" @scroll="loadScroll">
                <section class="flex flex-col gap-1 w-full" v-if="data.rows.length > 0">
                    <ClientMaintenanceListCard v-for="row in data.rows" :key="row.id" :row="row" :showBtnDel="store.authUser?.role_id <= 2" 
                        @edit="$router.push({ name: 'maintenance-form', params: { id: row.id, cedula: row.cedula } })"
                        @delete="() => deleteMantenience(row.id as unknown as string)" @toggle-status="changeStatus(row.id as unknown as string, row.status)" />
                </section>

                <div class="flex flex-col items-center justify-center h-full" v-if="data.rows.length === 0">
                    <Loader v-if="loaded" />
                    <p v-else class="text-gray-500 font-medium">Sin datos de mantenimiento.</p>
                </div>
            </div>
        </section>
    </div>
</template>
