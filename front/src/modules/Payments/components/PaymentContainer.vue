<script lang="ts" setup>
import Details from '@/modules/Financiacion/components/Details.vue';
import type { PagoInterface } from '../types/PagoType';
import PaymentCard from './paymentCard.vue';
import useShowFinancing from '@/modules/Financiacion/composables/useShowFinancing';
import PaymentsService from '../services'
import { questionSweet } from "@/utils/question"
import { alertWithToast } from "@/utils/toast"
import { ref } from 'vue';
import ViewFile from "@/components/ViewFile.vue";

const props = defineProps<{
    rows: PagoInterface[]
}>()

const emit = defineEmits(['statusChanged'])

const loaded = ref(true)
const docFile = ref('')

const {
    financingToShow,
    getShowData,
    emptyFinancingDetails
} = useShowFinancing()

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
            emit('statusChanged', { status })
        } else {
            alertWithToast(
                response.data.message || 'No se pudo actualizar el estado. Intenta de nuevo.',
                'error',
            )
        }
    } catch (error: any) {
        const detailedMessage = error.response?.data?.message
            || error.message
            || 'Ocurrió un error al cambiar el estado';

        alertWithToast(detailedMessage, 'error');
    } finally {
        loaded.value = false
    }
}


</script>

<template>
    <section class="w-full" v-if="rows.length > 0">
        <Details v-if="financingToShow.id" :financing="financingToShow"
            @close="financingToShow = emptyFinancingDetails()" />

        <section v-if="docFile != ''"
            class="fixed inset-0 flex items-center justify-center bg-black/50 backdrop-blur-sm z-50"
            @click.self="docFile = ''">
            <div class="bg-white rounded-2xl shadow-2xl w-full md:w-[55%] p-8 animate-fade-in border border-gray-100">
                <ViewFile :path="docFile" />
            </div>
        </section>

        <PaymentCard v-for="row in rows" :key="row.id" :pago="row"
            @statusChange="({ id, status }) => changeStatus(id, status)" @show="getShowData(row.financing_id)"
            @document="(document: string) => docFile = document" />
    </section>
</template>