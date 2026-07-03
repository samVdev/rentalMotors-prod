import { onMounted, reactive, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import useHttp from "@/composables/useHttp";
import { alertWithToast } from '@/utils/toast';
import type MaintenanceForm from '../types/MaintenanceForm';
import MaintenanceService from '../services'
import type User from '@/modules/User/types/User';
import UserService from "@/modules/User/services"

export default (id?: string) => {
  const router = useRouter();
  const route = useRoute();

  const data: MaintenanceForm = reactive({
    id: 0,
    type: 0,
    id_for_mant: 0,
    total: null,
    fecha: '',
    descripcion: '',
    persona_id: null,
    cedula: ''
  })

  const {
    errors,
    sending,
    loading,
  } = useHttp()

  const users = ref<User[]>([])

  onMounted(() => {
    if (id) getMaintenance()
    getUserMin()
  })

  const getMaintenance = async () => {
    loading.value = true
    try {
      const response = await MaintenanceService.show(id)
      data.id = response.data.id
      data.type = response.data.type
      data.id_for_mant = response.data.id_for_mant
      data.total = response.data.total
      data.fecha = response.data.fecha
      data.descripcion = response.data.descripcion
      data.persona_id = response.data.persona_id
      data.cedula = route.params.cedula as string
    } catch (error) {
      router.push('/users').then(() => {
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

  const insertMaintenance = async (_data: MaintenanceForm) => {
    try {
      const response = await MaintenanceService.store(_data)
      return response.data.message
    } catch (error) {
      throw error
    }
  }

  const updateMaintenance = async (_data: MaintenanceForm) => {
    try {
      const response = await MaintenanceService.update(id, _data)
      return response.data.message
    } catch (error) {
      throw error
    }
  }

  const submit = async (_data: MaintenanceForm) => {
    try {
      sending.value = true
      const response = !id ? await insertMaintenance(_data) : await updateMaintenance(_data)
      router.push({ path: '/maintenance' }).then(() => alertWithToast(response, 'success'))
    } catch (error) {
      let message = error.response ? error.response.data.message : 'Ha ocurrido un error inesperado'
      message = message.split('. (')[0]
      alertWithToast(message, 'error')
    } finally {
      sending.value = false
    }
  }

  const getUserMin = async () => {
    try {
      const response = await UserService.getUsersMin('trabajador')
      users.value = response.data
    } catch (error) {
      alertWithToast("Ocurrió un error al obtener usuarios, consulte a soporte", "error")
    }
  }



  return {
    users,
    data,
    errors,
    sending,
    loading,
    router,
    submit,
    getUserMin,
  }

}
