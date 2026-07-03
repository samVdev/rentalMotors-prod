<script setup lang="ts">
import { reactive, onMounted, ref, watch } from "vue"
import PaymentsService from '@/modules/Payments/services'
import ClientPaymentListCard from './ClientPaymentListCard.vue'
import Loader from '@/components/Loader.vue'
import useTableGrid from "@/composables/useTableGrid"
import { useRoute } from "vue-router"
import { alertWithToast } from "@/utils/toast"
import { questionSweet } from "@/utils/question"
import Details from '@/modules/Financiacion/components/Details.vue'
import useShowFinancing from '@/modules/Financiacion/composables/useShowFinancing'
import ViewFile from "@/components/ViewFile.vue"

const route = useRoute()
const currentType = ref('pending')
const loaded = ref(true)
const docFile = ref('')

const { financingToShow, getShowData, emptyFinancingDetails } = useShowFinancing()

const data = reactive({
    rows: <any[]>[],
    links: [],
    page: "1",
    search: "",
    sort: "",
    direction: "",
    offset: 0
})

const getScroll = () => PaymentsService.get(`user=${route.params.id}&offset=${data.offset}&type=${currentType.value}`)

const {
    loadScroll,
} = useTableGrid(data, getScroll)

const getData = async () => {
    loaded.value = true
    try {
        const response = await PaymentsService.get(`user=${route.params.id}&offset=0&type=${currentType.value}`)
        data.rows = response.data.rows
        data.offset = 10
    } catch (err) {
        console.error(err)
    } finally {
        loaded.value = false
    }
}

const changeStatus = async (id: string, status: boolean) => {
    try {
        const confirm = await questionSweet(
            '¿Estás seguro?',
            `Vas a marcar este pago como "${status ? 'Aceptado' : 'Rechazado'}".`,
            'warning',
        )

        if (!confirm) return

        loaded.value = true
        const response = await PaymentsService.put(id, status)

        if (response && response.status == 200) {
            alertWithToast(`El pago fue ${status ? 'aceptado' : 'rechazado'} correctamente.`, 'success')
            await getData()
        } else {
            alertWithToast(response.data.message || 'No se pudo actualizar el estado. Intenta de nuevo.', 'error')
        }
    } catch (error: any) {
        const detailedMessage = error.response?.data?.message || error.message || 'Ocurrió un error al cambiar el estado';
        alertWithToast(detailedMessage, 'error');
    } finally {
        loaded.value = false
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
        <Details v-if="financingToShow.id" :financing="financingToShow"
            @close="financingToShow = emptyFinancingDetails()" />

        <section v-if="docFile != ''"
            class="fixed inset-0 flex items-center justify-center bg-black/50 backdrop-blur-sm z-50"
            @click.self="docFile = ''">
            <div class="bg-white rounded-2xl shadow-2xl w-full md:w-[55%] p-8 animate-fade-in border border-gray-100">
                <ViewFile :path="docFile" />
            </div>
        </section>

        <div class="flex gap-5 justify-between my-6 md:w-[90%] mx-auto">
            <button @click="currentType = 'pending'" :class="[
                'block w-full px-5 py-2 font-bold rounded-full shadow-md transition-all duration-300',
                currentType === 'pending'
                    ? 'bg-[#FF7539] text-white hover:text-[#FF7539] hover:bg-white'
                    : 'text-[#FF7539] bg-white hover:bg-[#FF7539] hover:text-white'
            ]">
                Pendientes
            </button>

            <button @click="currentType = 'approved'" :class="[
                'block w-full px-5 py-2 font-bold rounded-full shadow-md transition-all duration-300',
                currentType === 'approved'
                    ? 'bg-[#FF7539] text-white hover:text-[#FF7539] hover:bg-white'
                    : 'text-[#FF7539] bg-white hover:bg-[#FF7539] hover:text-white'
            ]">
                Completados
            </button>

            <button @click="currentType = 'rejected'" :class="[
                'block w-full px-5 py-2 font-bold rounded-full shadow-md transition-all duration-300',
                currentType === 'rejected'
                    ? 'bg-[#FF7539] text-white hover:text-[#FF7539] hover:bg-white'
                    : 'text-[#FF7539] bg-white hover:bg-[#FF7539] hover:text-white'
            ]">
                Rechazados
            </button>
        </div>

        <section class="relative mx-auto my-2 overflow-auto">
            <div class="md:w-[90%] mx-auto h-[60vh] py-4" @scroll="loadScroll">
                <section class="flex flex-col gap-1 w-full" v-if="data.rows.length > 0">
                    <ClientPaymentListCard v-for="row in data.rows" :key="row.id" :pago="row"
                        @statusChange="({ id, status }) => changeStatus(id, status)"
                        @show="getShowData(row.financing_id)" @document="(document: string) => docFile = document" />
                </section>

                <div class="flex flex-col items-center justify-center h-full" v-if="data.rows.length === 0">
                    <Loader v-if="loaded" />
                    <p v-else class="text-gray-500 font-medium">Sin datos de pagos.</p>
                </div>
            </div>
        </section>
    </div>
</template>
