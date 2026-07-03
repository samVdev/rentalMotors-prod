<script setup lang="ts">
import FinancingServices from '@/modules/Financiacion/services'
import ClientFinancingListCard from './ClientFinancingListCard.vue'
import Details from '@/modules/Financiacion/components/Details.vue'
import useShowFinancing from '@/modules/Financiacion/composables/useShowFinancing'
import Loader from '@/components/Loader.vue'
import { reactive, onMounted, ref } from "vue"
import useTableGrid from "@/composables/useTableGrid"
import { useRoute } from "vue-router"
import { alertWithToast } from "@/utils/toast"
import { questionSweet } from "@/utils/question"

const {
    financingToShow,
    getShowData,
    emptyFinancingDetails
} = useShowFinancing()

const data = reactive({
    rows: <any[]>[],
    links: [],
    page: "1",
    search: "",
    sort: "",
    direction: "",
    offset: 0
})

const route = useRoute()
const loaded = ref(true)

const getScroll = () => FinancingServices.get(`user=${route.params.id}&offset=${data.offset}`)

const {
    router,
    loadScroll,
} = useTableGrid(data, getScroll)

const getData = async () => {
    loaded.value = true
    try {
        const response = await FinancingServices.get(`user=${route.params.id}&offset=0`)
        data.rows = response.data.rows
        data.search = response.data.search
        data.sort = response.data.sort
        data.direction = response.data.direction
        data.offset = 10
    } catch (err) {
        console.error(err)
    } finally {
        loaded.value = false
    }
}

const deleteItem = async (id: number) => {
    try {
        const confirm = await questionSweet(
            '¿Estás seguro?',
            `Vas a eliminar esta financiación.`,
            'warning',
        )

        if (!confirm) return

        loaded.value = true

        const response = await FinancingServices.delete(id)

        if (response && response.status == 200) {
            alertWithToast(
                `La financiación se ha eliminado correctamente.`,
                'success',
            )
        } else {
            alertWithToast(
                response.data.message || 'No se pudo eliminar. Intenta de nuevo.',
                'error',
            )
        }

        await getData()

    } catch (error: any) {
        alertWithToast(
            error?.message || 'Ocurrió un error al eliminar',
            'error',
        )
    } finally {
        loaded.value = false
    }
}

onMounted(() => {
    getData()
})

</script>

<template>
    <section class="relative mx-auto my-2 overflow-auto">
        <Details v-if="financingToShow.id" :financing="financingToShow"
            @close="financingToShow = emptyFinancingDetails()" />

        <div class="md:w-[90%] mx-auto h-[60vh] py-4" @scroll="loadScroll">
            <section class="flex flex-col gap-1 w-full" v-if="data.rows.length > 0">
                <ClientFinancingListCard v-for="row in data.rows" :key="row.id + row.cliente" :id="row.id"
                    :lote="row.lote" :responsable="row.responsable" :codigo="row.codigo"
                    :type="row.tipo" :installments="row.cuotas" :startDate="row.fecha_inicio"
                    @details="getShowData(row.id)" />
            </section>

            <div class="flex flex-col items-center justify-center h-full" v-if="data.rows.length === 0">
                <Loader v-if="loaded" />
                <p v-else class="text-gray-500 font-medium">Sin datos de financiamientos.</p>
            </div>
        </div>
    </section>
</template>
