import { alerta } from '@/utils/alert'
import Http from '@/utils/Http'
import { alertWithToast } from '@/utils/toast'
import { ref } from 'vue'
import { useRouter } from 'vue-router'

export default () => {
  const router = useRouter()
  const loading = ref(false)
  const error = ref<string | null>(null)
  const success = ref(false)

  const submitForm = async (formData: FormData) => {
    loading.value = true
    error.value = null
    success.value = false

    try {
      await Http.post('/api/guest/apply', formData)
      success.value = true
      router.push('/').then(() =>
        alerta(
          'Completado',
          'Se puso en revisión tus requisitos, nos pondremos en contacto próximamente',
          'success'
        )
      )
    } catch (err: any) {
      if (err.response) {
        const status = err.response.status
        if (status === 413) {
          error.value = 'El archivo que intentas subir es demasiado grande. Máximo 20MB.'
          alertWithToast(error.value, 'error')
        } else if (status === 422) {
          const errors = err.response.data.errors
          // @ts-ignore
          const firstError = errors ? Object.values(errors)[0][0] : 'Error de validación'
          error.value = firstError
          alertWithToast(error.value, 'error')
        } else {
          error.value = 'Ocurrió un error en el servidor. Intenta nuevamente.'
          alertWithToast(error.value, 'error')
        }
      } else {
        error.value = 'No se pudo conectar con el servidor. Verifica tu conexión.'
        alertWithToast(error.value, 'error')
      }
    } finally {
      loading.value = false
    }
  }

  return { loading, success, submitForm }
}
