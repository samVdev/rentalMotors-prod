import { reactive, onMounted, ref } from "vue"
import { useRoute, useRouter } from "vue-router"
import useHttp from "@/composables/useHttp"
import VehicleService from "@/modules/Vehicles/services"
import UserService from "@/modules/User/services"
import { alertWithToast } from "@/utils/toast"

import type { VehicleType } from "../types/vehicleType"
import type User from "@/modules/User/types/User"

export default (vehicleId?: string) => {
  const router = useRouter()
  const route = useRoute()

  const vehicle: VehicleType = reactive({
    marca: "",
    modelo: "",
    image: "",
    year: null,
    cc: "",
    color: "",
    precio: null,
    kilometraje: null,
    type: null,
    show: false,
  })

  const users = ref<User[]>([])

  const {
    errors,
    sending,
    loading,
  } = useHttp()

  onMounted(() => {
    if (vehicleId) getVehicle()
  })

  const getVehicle = async () => {
    loading.value = true
    try {
      const response = await VehicleService.getVehicle(vehicleId)
      vehicle.marca = response.data.marca
      vehicle.modelo = response.data.modelo
      vehicle.image = response.data.image
      vehicle.year = response.data.year
      vehicle.cc = response.data.cc
      vehicle.color = response.data.color
      vehicle.precio = response.data.precio
      vehicle.kilometraje = response.data.kilometraje
      vehicle.type = response.data.type
      vehicle.show = response.data.show
    } catch (error: any) {
      let paths = route.fullPath.split('/')
      const path = '/' + paths[1] + '/' +  paths[2]
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

  const getUserMin = async () => {
    try {
      const response = await UserService.getUsersMin('admin')
      users.value = response.data
    } catch (error) {
      alertWithToast("Ocurrió un error al obtener usuarios, consulte a soporte", "error")
    }
  }

  const insertVehicle = async (vehicle: any) => {
    try {
      const response = await VehicleService.post(vehicle)
      return response.data.message
    } catch (error) {
      throw error
    }
  }

  const updateVehicle = async (vehicle: any, id: string) => {
    try {
      const response = await VehicleService.put(id, vehicle)
      return response.data.message
    } catch (error) {
      throw error
    }
  }

  const submit = async (form: FormData) => {
  
    try {
      form.append('type', route.fullPath.includes('bikes') ? 'bike' : 'car')
      
      if (route.params.id) form.append("_method", "PUT")
      
      sending.value = true
      const response = !route.params.id
        ? await insertVehicle(form)
        : await updateVehicle(form, route.params.id as string)
  

      let paths = route.fullPath.split('/')
      const path = '/' + paths[1] + '/' +  paths[2]
      
      router.push({ path }).then(() =>
        alertWithToast(response, "success")
      )
    } catch (error: any) {
      let message = error.response
        ? error.response.data.message
        : "Ha ocurrido un error inesperado"
  
      message = message.split(". (")[0]
      alertWithToast(message, "error")
    } finally {
      sending.value = false
    }
  }

  return {
    users,
    vehicle,
    errors,
    sending,
    loading,
    router,
    submit,
    getUserMin
  }
}
