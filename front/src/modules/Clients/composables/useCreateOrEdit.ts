import { onMounted, reactive, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import useHttp from "@/composables/useHttp";
import clientservice from "@/modules/Clients/services";
import type User from "../types/User"
import { alertWithToast } from '@/utils/toast';
import { FormatCurrency } from '@/utils/formatCurrency';

export default (userId?: string) => {
  const router = useRouter();
  const route = useRoute();

  const user: User = reactive({
    name: "",
    usuario: '',
    email: "",
    phone: "",
    cedula: "",
    dateN: "",
    dir: "",
    earnings: '',
    image: null,
    imageFile: '',
    imageApp: null
  })

  const {
    errors,
    sending,
    loading,
  } = useHttp()

  const url = import.meta.env.VITE_APP_API_URL

  onMounted(() => {
    if (userId) getUser()
  })

  const getUser = async () => {
    loading.value = true
    try {
      const apply_id = route.query.apply_id
      const response = await clientservice.getClient(userId, apply_id ? `apply_id=${apply_id}` : '')
      user.name = response.data.name
      user.usuario = response.data.usuario
      user.email = response.data.email
      user.phone = response.data.phone
      user.cedula = response.data.cedula
      user.dateN = response.data.dateN
      user.dir = response.data.dir
      user.earnings = FormatCurrency(response.data.earnings)
      user.imageApp = response.data.image

    } catch (error) {
      router.push('/clients').then(() => {
        if (error?.status === 404) {
          alertWithToast('Usuario no encontrado', 'error')
        } else {
          alertWithToast('Ocurrio un error, consulte a soporte', 'error')
        }
      })
    }
  }
  const insertUser = async (user: FormData) => {
    try {
      const response = await clientservice.insertClient({
        ...user,
        apply_id: route?.query?.apply_id ? route.query.apply_id : null
      })
      return response.data.message
    } catch (error) {
      throw error
    }
  }

  const updateUser = async (user: FormData, userId: string) => {
    try {
      const apply_id = route.query.apply_id
      user.append("_method", "PUT")
      const response = await clientservice.updateClient(userId, user, apply_id ? `apply_id=${apply_id}` : '')
      return response.data.message
    } catch (error) {
      throw error
    }
  }

  const submit = async (user: FormData, userId?: string) => {
    try {
      sending.value = true

      const response = !userId ? await insertUser(user) : await updateUser(user, userId)
      router.push({ path: '/clients' }).then(() => alertWithToast(response, 'success'))
    } catch (error) {
      let message = error.response ? error.response.data.message : 'Ha ocurrido un error inesperado'
      message = message.split('. (')[0]
      alertWithToast(message, 'error')
    } finally {
      sending.value = false
    }
  }


  return {
    user,
    errors,
    sending,
    router,
    submit
  }

}
