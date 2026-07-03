import { ref } from "vue"
import GuestService from '../services'
import { alertWithToast } from "@/utils/toast"
import { questionSweet } from "@/utils/question"

export default () => {

    const data = ref({
        financings: [],
        mantenimientos: [],
        moras: [],
        countedItems: {
            payments: 0,
            cuotas: 0,
        }
    })

    const pay_id = ref('')
    const pay_total = ref(0)
    const pay_mora_id = ref<number | null>(null)

    const getData = async () => {
        try {
            const response = await GuestService.getHomeService()
            data.value.financings = response.data.financings
            data.value.mantenimientos = response.data.mantenimientos
            data.value.moras = response.data.moras || []

            let totalPayments = data.value.financings.length > 0 ? data.value.financings[0]?.approvedCount : 0
            let totalCuotas = 0

            data.value.financings.forEach(f => {
                totalCuotas += f?.total_cuotas ?? 0
            })

            data.value.countedItems = {
                payments: totalPayments,
                cuotas: totalCuotas,
            }

        } catch (error) {
            const message = error.response.data.errors.msg || 'Error inesperado';
            alertWithToast(message, 'error')
        }
    }

    const close = () => {
        pay_id.value = ''
        pay_total.value = 0
        pay_mora_id.value = null
        getData()
    }

    const mantenimientSend = async (id: string) => {

        const confirm = await questionSweet(
            'Atención',
            `¿Seguro que quieres notificar este mantenimiento?`,
            'warning',
        )

        if (!confirm) return


        try {
            const response = await GuestService.mantenimientService(id)
            alertWithToast(response.data.message, 'success')
            data.value.mantenimientos = data.value.mantenimientos.filter(m => m.id !== id)
        } catch (error) {
            const message = error.response.data.errors.message || 'Error inesperado';
            alertWithToast(message, 'error')
        }
    }

    return {
        data,
        pay_id,
        pay_total,
        pay_mora_id,
        getData,
        close,
        mantenimientSend
    }
}