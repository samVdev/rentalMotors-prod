import { alertWithToast } from "@/utils/toast";
import { ref } from "vue"
import adminService from "../services/panel";
import { useRouter } from 'vue-router'
import type { PagoInterface } from "@/modules/Payments/types/PagoType";
import type { FinancingInterface } from "@/modules/Financiacion/types/FinancingInterface";

export default () => {

    const url = import.meta.env.VITE_APP_API_URL
    const countedData = ref({
        clients: 0,
        bikes: 0,
        earnings: 0,
        activesPayment: 0,
        manteniment: 0,
    })

    const payments = ref<PagoInterface[]>([])
    const financings = ref<FinancingInterface[]>([])
    const financingsFinishing = ref<FinancingInterface[]>([])

    const router = useRouter()

    const getCountedData = async () => {
        try {
            const response = await adminService.getCountedDataService()
            countedData.value = {
                ...response.data,
            }
        } catch (error) {
            const message = error.response.data.errors.msg || 'Error inesperado';
            alertWithToast(message, 'error')
        }
    }

    const getPayments = async () => {
        const response = await adminService.getPayments()
        payments.value = response.data.map(e => {
            return {
                ...e,
                file: e.file ? `${url}/public/${e.file}` : '',
            }
        })
    }

    const getFinancings = async () => {
        const response = await adminService.getFinancing()
        financings.value = response.data.actives
        financingsFinishing.value = response.data.finishing
    }

    return {
        financings,
        financingsFinishing,
        payments,
        countedData,
        getCountedData,
        getPayments,
        getFinancings
    }
}