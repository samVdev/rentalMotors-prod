<script setup lang="ts">
import { onMounted, ref } from 'vue'
import MaintenanceService from '../../services'
import { alertWithToast } from '@/utils/toast'
import Loader from '@/components/Loader.vue'

const props = defineProps<{
    form: any
}>()

const maintenanceType = ref('')
const cedula = ref('')
const sending = ref(false)

const url = import.meta.env.VITE_APP_API_URL

const emit = defineEmits<{
    (e: 'data', payload: { data: any[], type: number, cedula: string}): void
    (e: 'error', message: string): void
}>()

const onTypeChange = async () => {
    if (!maintenanceType.value) {
        alertWithToast('Selecciona el tipo de mantenimiento', 'info')
        return
    }

    if (!cedula.value) {
        alertWithToast('Ingresa la cédula del cliente', 'info')
        return
    }

    sending.value = true

    try {
        const type = maintenanceType.value === 'financed' ? 1 : 2

        const response = await MaintenanceService.getApplyOrFinancing(type, cedula.value)
        let information = response?.data?.data ?? []

        const data = information.map(e => {
            return {
                ...e,
                img: e.img ? `${url}/${e.img}` : '',
            }
        })

        if (data.length > 0) {
            emit('data', {
                data,
                type,
                cedula: cedula.value
            })
        } else {
            emit('error', 'No se encontró información para este cliente')
            alertWithToast('No se encontró información para este cliente', 'info')
        }
    } catch (error: any) {
        const message =
            error?.response?.data?.message ||
            error?.message ||
            'Ocurrió un error al consultar la información'

        emit('error', message)
        alertWithToast(message, 'error')
    } finally {
        sending.value = false
    }
}


onMounted(() => {
    maintenanceType.value = props.form?.type || ''
    cedula.value = props.form?.cedula || ''
})

</script>

<template>
    <form @submit.prevent="onTypeChange" class="max-w-sm mx-auto grid gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Cédula del cliente
            </label>
            <input v-model="cedula" type="text" placeholder="Ingresa la cédula" class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700
                   focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Tipo de mantenimiento
            </label>
            <select v-model="maintenanceType" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-700
                   focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option disabled value="">
                    Selecciona una opción
                </option>
                <option value="financed">Vehículo financiado</option>
                <option value="cash">Vehículo de contado</option>
            </select>
        </div>

        <div class="flex justify-end mt-4">
            <button v-if="!sending"
                class="w-full bg-[#FF7539] hover:bg-[#D54A5C] text-white px-6 py-3 rounded-2xl font-bold shadow-md transition transform hover:scale-[1.02]">
                Siguiente
            </button>
            <Loader v-else class="mx-auto" />
        </div>
    </form>
</template>