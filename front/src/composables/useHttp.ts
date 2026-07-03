import { ref } from 'vue'

export default () => {
  const sending = ref(false)
  const loading = ref(false)
  const pending = ref(false)
  const errors = ref({})
  
  return {
    errors,
    loading,
    sending,
    pending,
  }
}

