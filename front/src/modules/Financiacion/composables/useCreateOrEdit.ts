import { reactive, onMounted, ref } from "vue"
import { useRoute, useRouter } from "vue-router"
import useHttp from "@/composables/useHttp"
import FinancingService from "@/modules/Financiacion/services"
import UserService from "@/modules/User/services"
import { alertWithToast } from "@/utils/toast"
import VehiclesServices from '@/modules/Vehicles/services'

import type User from "@/modules/User/types/User"
import type { VehicleType } from "@/modules/Vehicles/types/vehicleType"

export default (finan_id?: string) => {
  const router = useRouter()
  const route = useRoute()

  const financiamiento = reactive({
    plan: null,
    vehiculo: null,
    cliente: null,
    tipo: "",
    meses: null,
    fecha_inicio: "",
    observacion: "",
    estado: "pendiente",
    image: "",
    pago_inicial: '',
    priceTotal: '',
    mora: 0,
    lote_name: '',
    interes: 0
  })

  const users = ref<User[]>([])
  const vehicles = ref<VehicleType[]>([])
  const url = import.meta.env.VITE_APP_API_URL

  const {
    errors,
    sending,
    loading,
  } = useHttp()

  onMounted(() => {
    if (finan_id) getOne()
  })

  const getOne = async () => {
    loading.value = true
    try {
      const response = await FinancingService.getOne(finan_id)
      financiamiento.plan = response.data.plan
      financiamiento.meses = response.data.meses
      financiamiento.observacion = response.data.observacion
      financiamiento.mora = response.data.mora ? response.data.mora : 0
      financiamiento.lote_name = response.data.lote_name ? response.data.lote_name : ''
    } catch (error: any) {
      const path = '/financing'
      router.push(path).then(() => {
        if (error?.status === 404) {
          alertWithToast("Vehículo no encontrado", "error")
        } else {
          alertWithToast("Ocurrió un error, consulte a soporte", "error")
        }
      })
    } finally {
      loading.value = false
    }
  }


  const getMinVehicle = async () => {
    const response = await VehiclesServices.getMin()
    vehicles.value = response.data.map(e => {
      return {
          ...e,
          img: `${url}/${e.img}`,
      }
  })
  }


  const getUserMin = async () => {
    try {
      const response = await UserService.getUsersMin('cliente')
      users.value = response.data
    } catch (error) {
      alertWithToast("Ocurrió un error al obtener usuarios, consulte a soporte", "error")
    }
  }

  const insertFinancing = async (form: any) => {
    try {
      const response = await FinancingService.post(form)
      return response.data.message
    } catch (error) {
      throw error
    }
  }

  const updateFinancing = async (form: FormData, id: string) => {
    try {
      form.append("_method", "PUT")
      const response = await FinancingService.put(id, form)
      return response.data.message
    } catch (error) {
      throw error
    }
  }

  const submit = async (data: FormData) => {
    try {
      sending.value = true
      const response = !route.params.id
        ? await insertFinancing(data)
        : await updateFinancing(data, route.params.id as string)

      let paths = route.fullPath.split('/')
      const path = '/' + paths[1]

      router.push({ path }).then(() =>
        alertWithToast(response, "success")
      )
    } catch (error: any) {
      let message = error.response
        ? error.response.data.message
        : "Ha ocurrido un error inesperado"

      console.log(error)
      message = message.split(". (")[0]
      alertWithToast(message, "error")
    } finally {
      sending.value = false
    }
  }

  return {
    users,
    vehicles,
    financiamiento,
    errors,
    sending,
    loading,
    router,
    submit,
    getUserMin,
    getMinVehicle
  }
}
