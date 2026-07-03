import {reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import useHttp from "@/composables/useHttp";
import LotesService from "@/modules/Lotes/services";
import type Role from "../types/Role"
import { alertWithToast } from '@/utils/toast';

export default () => {
  const router = useRouter();

  const data: any = reactive({
    nombre: "",
  })

  const roles = ref<Role[]>([])

  const {
    errors,
    sending,
    loading,
  } = useHttp()


  const insert = async (data: any) => {
    try {
      const response = await LotesService.insert(data)
      return response.data.message
    } catch (error) {
      throw error
    }
  }

  const submit = async (data: any) => {
    try {
      sending.value = true
      await insert(data)
      router.push({ path: '/lotes' }).then(() => alertWithToast('Lote creado exitosamente', 'success'))
    } catch (error) {
      let message = error.response ? error.response.data.message : 'Ha ocurrido un error inesperado'
      message = message.split('. (')[0]
      alertWithToast(message, 'error')
    } finally {
      sending.value = false
    }
  }
  

  return {
    data,
    errors,
    roles,
    sending,
    loading,
    router,
    submit
  }

}
