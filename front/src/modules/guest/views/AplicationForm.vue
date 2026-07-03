<script setup lang="ts">
import { reactive, ref } from 'vue'
import useAplicattionForm from '../composable/useAplicattionForm'
import UploadField from '../components/UploadField.vue'
import VehiclesCarrousel from '../components/vehiclesCarrousel.vue'
import type { vehicleMinType } from '../types/vehicleMinType'
import { alertWithToast } from '@/utils/toast'

const { loading, success, submitForm } = useAplicattionForm()

const vehicle = ref<vehicleMinType>({
    id: '',
    image: '',
    title: '',
    description: '',
    price: ''
})

const form = reactive({
    vehicleType: '' as '' | 'bike' | 'car',
    selectedVehicleId: '' as string,
    telefono: '',
    identity: '',
    type: '',
    cuotas: '',
    tipoDocumento: '',
    direccion: '',
    fullName: '',
    documents: {
        cedula: { file: null as File | null, url: '' },
        carnet: { file: null as File | null, url: '' },
        cedulaAmpliada: { file: null as File | null, url: '' },
        recibo: { file: null as File | null, url: '' },
        refFamiliares: { file: null as File | null, url: '' },
        refPersonales: { file: null as File | null, url: '' },
        pep: { file: null as File | null, url: '' },
    },
})

function setType(t: 'bike' | 'car') {
    form.vehicleType = t
    form.selectedVehicleId = ''
}

function selectVehicle(element?: vehicleMinType) {
    form.selectedVehicleId = element ? element.id : ''
    vehicle.value = element ? element : {
        id: '',
        image: '',
        title: '',
        description: '',
        price: ''
    }
}

function onSet(key: keyof typeof form.documents, payload: { imgFile: File | null; imgURL: string | null }) {
    form.documents[key].file = payload.imgFile
    form.documents[key].url = payload.imgURL ?? ''
}

function typeBtnClass(t: 'bike' | 'car') {
    return [
        'px-4 py-2 rounded-md font-medium',
        form.vehicleType === t ? 'bg-[#FF7539] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
    ].join(' ')
}

function validate() {
    if (!form.vehicleType) {
        return 'Selecciona si es Moto o Carro'
    }
    if (!form.selectedVehicleId) {
        return 'Selecciona un modelo de vehículo'
    }
    if (!form.telefono) {
        return 'Ingresa un número de teléfono'
    }
    if (!form.documents.cedula.file) {
        return 'Sube la cédula de ciudadanía'
    }
    return null
}

function onSubmit() {
    const v = validate()
    if (v) {
        alertWithToast(v, 'error')
        return
    }

    const fd = new FormData()
    fd.append('fullName', form.fullName)
    fd.append('vehicleType', form.vehicleType)
    fd.append('selectedVehicleId', form.selectedVehicleId)
    fd.append('telefono', form.telefono)
    fd.append('identity', form.identity)
    fd.append('type', form.type)
    fd.append('cuotas', form.cuotas)
    fd.append('tipoDocumento', form.tipoDocumento)
    fd.append('direccion', form.direccion)

    // @ts-ignore
    Object.entries(form.documents).forEach(([k, obj]) => {
        const file = (obj as any).file as File | null
        if (file) {
            fd.append(`documents[${k}]`, file)
        }
    })

    submitForm(fd)
}

const url = import.meta.env.VITE_APP_API_URL

</script>

<template>
    <main class="w-full">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-3">
                <div class="lg:col-span-2 p-8 space-y-6">
                    <header class="flex items-center justify-between">
                        <h1 class="text-2xl font-bold text-[#FF7539]">Formulario de Financiación</h1>
                        <div class="text-sm text-gray-500">Llena los datos y sube los documentos</div>
                    </header>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                        <div class="md:col-span-3">
                            <label class="block text-sm font-medium mb-2">Nombre Completo (*)</label>
                            <div class="flex">
                                <input v-model="form.fullName" type="text" placeholder="Ingrese su nombre completo"
                                    class="w-full p-3 border rounded-r-lg focus:ring-2 focus:ring-[#FF7539]" />
                            </div>
                        </div>

                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium mb-2">Tipo de documento (*)</label>
                            <select v-model="form.tipoDocumento"
                                class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-[#FF7539]">
                                <option value="">Sin selección</option>
                                <option value="C">Cédula (Colombia)</option>
                                <option value="P">PPT</option>
                                <option value="E">Cédula Extranjera</option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium mb-2">Número de documento (*)</label>
                            <div class="flex">
                                <span class="flex items-center px-3 border border-r-0 rounded-l-lg bg-gray-100">
                                    {{ form.tipoDocumento }}
                                </span>
                                <input v-model="form.identity" type="text" placeholder="Ingrese su número de documento"
                                    class="w-full p-3 border rounded-r-lg focus:ring-2 focus:ring-[#FF7539]" />
                            </div>
                        </div>

                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium mb-2">Forma de pago (*)</label>
                            <select v-model="form.type"
                                class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-[#FF7539]">
                                <option value="">Sin selección</option>
                                <option value="contado">Contado</option>
                                <option value="financiacion">Financiación</option>
                            </select>
                        </div>

                        <div class="md:col-span-2" v-if="form.type === 'financiacion'">
                            <label class="block text-sm font-medium mb-2">Plan de financiamiento (*)</label>
                            <select v-model="form.cuotas"
                                class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-[#FF7539]">
                                <option disabled value="">Seleccione una opción</option>
                                <option value="Diario">Diario</option>
                                <option value="Semanal">Semanal</option>
                                <option value="Quincenal">Quincenal</option>
                                <option value="Mensual">Mensual</option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium mb-2">Teléfono (WhatsApp) (*)</label>
                            <input v-model="form.telefono" type="text" placeholder="+58 4xx xxx xxxx"
                                class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-[#FF7539]" />
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-2">Dirección (*)</label>
                        <textarea v-model="form.direccion" type="text" placeholder="Ingrese su dirección completa"
                            class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-[#FF7539]" />
                    </div>

                    <div class="w-full mx-auto md:w-[80%]">
                        <label class="block text-md text-gray-500 font-medium my-2">Tipo de vehículo </label>
                        <div class="flex flex-col md:flex-row gap-3 w-full">
                            <button :class="typeBtnClass('bike')" @click="setType('bike')" type="button"
                                class="w-full md:w-1/2 py-3 rounded-lg border text-center font-medium transition duration-200">
                                Moto
                            </button>

                            <button :class="typeBtnClass('car')" @click="setType('car')" type="button"
                                class="w-full md:w-1/2 py-3 rounded-lg border text-center font-medium transition duration-200">
                                Carro
                            </button>
                        </div>
                    </div>


                    <div v-if="form.vehicleType" class="space-y-3">
                        <div class="flex items-center justify-between">
                            <h2 class="font-semibold text-[#1F2937]">Modelos disponibles</h2>
                            <div class="text-xs text-gray-500">Selecciona el modelo que prefieras</div>
                        </div>

                        <VehiclesCarrousel :bntAdd="true" :type="form.vehicleType == 'car' ? 'cars' : 'bikes'"
                            @select="({ element }) => selectVehicle(element)" />
                    </div>

                    <div>
                        <h2 class="font-semibold text-[#1F2937] mb-4">Documentos requeridos</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <UploadField id="doc-cedula" label="Cédula de ciudadanía (*)"
                                :initialUrl="form.documents.cedula.url" @setImg="onSet('cedula', $event)" />
                            <UploadField id="doc-carnet" label="Foto tamaño carnet (opcional)"
                                :initialUrl="form.documents.carnet.url" @setImg="onSet('carnet', $event)" />
                            <UploadField id="doc-cedula-150" label="Cédula al 150% (opcional)"
                                :initialUrl="form.documents.cedulaAmpliada.url"
                                @setImg="onSet('cedulaAmpliada', $event)" />
                            <UploadField id="doc-recibo" label="Recibo de servicio público (opcional)"
                                :initialUrl="form.documents.recibo.url" @setImg="onSet('recibo', $event)" />
                            <UploadField id="doc-reffam" label="Referencias familiares (opcional)"
                                :initialUrl="form.documents.refFamiliares.url"
                                @setImg="onSet('refFamiliares', $event)" />
                            <UploadField id="doc-refpers" label="Referencias personales (opcional)"
                                :initialUrl="form.documents.refPersonales.url"
                                @setImg="onSet('refPersonales', $event)" />
                            <UploadField id="doc-pep" label="PPT (si aplica)" :initialUrl="form.documents.pep.url"
                                @setImg="onSet('pep', $event)" />
                        </div>
                    </div>

                </div>

                <aside class="lg:col-span-1 p-6 bg-gray-50">
                    <div class="sticky top-6 space-y-4">
                        <div class="bg-white rounded-lg p-4 shadow-sm">
                            <div class="text-xs text-gray-500">Resumen</div>
                            <div class="mt-3">
                                <div class="text-sm font-medium">Vehículo:</div>
                                <div class="text-sm mt-1">{{ form.vehicleType ? (form.vehicleType === 'bike' ? 'Moto' :
                                    'Carro') : '—' }}</div>
                                <div class="text-sm text-gray-500 mt-2">{{ vehicle.title || 'No has seleccionado modelo'
                                    }}</div>
                            </div>

                            <div class="mt-4">
                                <div class="text-sm font-medium">Teléfono</div>
                                <div class="text-sm mt-1">{{ form.telefono || '—' }}</div>
                            </div>

                            <div class="mt-4">
                                <div class="text-sm font-medium">Documentos subidos</div>
                                <ul class="text-xs mt-2 space-y-1">
                                    <li>Cédula: <span class="font-medium">{{ form.documents.cedula.file ? '✔' : '—'
                                            }}</span></li>
                                    <li>Recibo: <span class="font-medium">{{ form.documents.recibo.file ? '✔' :
                                        '—' }}</span></li>
                                    <li>Referencias: <span class="font-medium">{{ (form.documents.refFamiliares.file ||
                                        form.documents.refPersonales.file) ? '✔' : '—' }}</span></li>
                                </ul>
                            </div>
                        </div>

                        <div v-if="vehicle.id" class="bg-white rounded-lg p-4 shadow-sm">
                            <img :src="`${url}/${vehicle.image}`" class="w-full h-36 object-cover rounded" />
                            <div class="mt-3">
                                <div class="font-semibold">{{ vehicle.title }}</div>
                            </div>
                            <div class="mt-3 text-right">
                                <button @click="selectVehicle()" type="button"
                                    class="text-xs text-gray-500">Deseleccionar</button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 items-end gap-4">
                            <div class="text-right">
                                <button @click="onSubmit" :disabled="loading"
                                    class="bg-[#FF7539] text-white px-6 py-3 rounded-lg font-semibold shadow">
                                    {{ loading ? 'Enviando...' : 'Enviar solicitud' }}
                                </button>
                            </div>
                        </div>

                        <div v-if="success" class="text-green-600 font-medium mt-2">✅ Formulario enviado con éxito</div>
                    </div>
                </aside>
            </div>
        </div>
    </main>
</template>