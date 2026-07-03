import type { FinancingDetailsInterface } from "../types/VehicleInterface";
import FinancingServices from '../services'
import { alertWithToast } from "@/utils/toast";
import { ref } from "vue";

export default () => {
    const url = import.meta.env.VITE_APP_API_URL

    const emptyFinancingDetails = (): FinancingDetailsInterface => ({
        id: null,
        codigo: '',
        image_vehiculo: '',
        vehicle: '',
        plan: null,
        vehiculo_marca: '',
        vehiculo_model: '',
        vehiculo_placa: '',

        cliente: '',
        cliente_ci: '',
        cliente_contact: '',
        tipo: '',
        cuotas: null,
        meses: null,
        fecha_inicio: '',
        observacion: '',
        estado: 'pendiente',
        payments: [],

        precio: 0,
        inicial: 0,
        cost_inicial: 0,
        intereses: 0,
        mensual: 0,
        quincenal: 0,

        semanal: 0,
        diario: 0,
        lote: '',
        cliente_archivo: '',
        mora_index: 0,
        application_id: null,
        recaudos_pdf: null,

        deudaAdquirida: 0, // o null, según lo que permita tu interfaz
        deudaPagar: 0,      // o null
    });

    const financingToShow = ref<FinancingDetailsInterface>(emptyFinancingDetails())

    const getShowData = async (id: string) => {
        try {
            const response = await FinancingServices.show(id)
            financingToShow.value = {
                ...response.data,
                image_vehiculo: response.data.image_vehiculo ? `${url}/${response.data.image_vehiculo}` : '',
                payments: response.data.pagos
            }
        } catch (error) {
            let message = error.response
                ? error.response.data.message
                : "Ha ocurrido un error inesperado"

            message = message.split(". (")[0]
            alertWithToast(message, "error")
        }
    }

    return {
        financingToShow,
        getShowData,
        emptyFinancingDetails
    }
}