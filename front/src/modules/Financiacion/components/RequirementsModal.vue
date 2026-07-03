<script setup lang="ts">
import { reactive, ref } from 'vue'
import UploadField from '@/modules/guest/components/UploadField.vue'
import Loader from "@/components/Loader.vue";
import { alertWithToast } from '@/utils/toast'

const emit = defineEmits<{
    (e: 'close'): void,
    (e: 'submit', form: FormData): void;
}>();

const loading = ref(false)

const form = reactive({
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

function onSet(key: keyof typeof form.documents, payload: { imgFile: File | null; imgURL: string | null }) {
    form.documents[key].file = payload.imgFile
    form.documents[key].url = payload.imgURL ?? ''
}

function onSubmit() {
    // @ts-ignore
    const hasAnyFile = Object.values(form.documents).some(doc => doc.file !== null)
    
    if (!hasAnyFile) {
        alertWithToast('Debes subir al menos un documento', 'error')
        return
    }

    loading.value = true
    const fd = new FormData()

    // @ts-ignore
    Object.entries(form.documents).forEach(([k, obj]) => {
        if (obj.file) {
            fd.append(`documents[${k}]`, obj.file)
        }
    })

    emit('submit', fd)
}

</script>

<template>
    <div class="p-4">
        <header class="mb-6 text-center">
            <h2 class="text-2xl font-bold text-gray-800">Actualizar Recaudos</h2>
            <p class="text-sm text-gray-500 mt-1">Sube los documentos actualizados para esta financiación</p>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-h-[60vh] overflow-y-auto px-2 py-4 border-y border-gray-100 mb-6">
            <UploadField id="req-cedula" label="Cédula de ciudadanía"
                :initialUrl="form.documents.cedula.url" @setImg="onSet('cedula', $event)" />
            
            <UploadField id="req-carnet" label="Foto tamaño carnet"
                :initialUrl="form.documents.carnet.url" @setImg="onSet('carnet', $event)" />
            
            <UploadField id="req-cedula-150" label="Cédula al 150%"
                :initialUrl="form.documents.cedulaAmpliada.url"
                @setImg="onSet('cedulaAmpliada', $event)" />
            
            <UploadField id="req-recibo" label="Recibo de servicio público"
                :initialUrl="form.documents.recibo.url" @setImg="onSet('recibo', $event)" />
            
            <UploadField id="req-reffam" label="Referencias familiares"
                :initialUrl="form.documents.refFamiliares.url"
                @setImg="onSet('refFamiliares', $event)" />
            
            <UploadField id="req-refpers" label="Referencias personales"
                :initialUrl="form.documents.refPersonales.url"
                @setImg="onSet('refPersonales', $event)" />
            
            <UploadField id="req-pep" label="PPT (si aplica)" 
                :initialUrl="form.documents.pep.url"
                @setImg="onSet('pep', $event)" />
        </div>

        <div class="flex justify-end gap-3">
            <button 
                type="button"
                @click="emit('close')"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
            >
                Cancelar
            </button>
            
            <button 
                v-if="!loading"
                @click="onSubmit"
                class="px-6 py-2 text-sm font-bold text-white bg-[#FF7539] rounded-lg hover:bg-[#e66a33] shadow-md transition-all"
            >
                Guardar cambios
            </button>
            <Loader v-else />
        </div>
    </div>
</template>

<style scoped>
/* Estilos para el scroll en el grid de UploadField */
::-webkit-scrollbar {
  width: 6px;
}
::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}
::-webkit-scrollbar-thumb {
  background: #ccc;
  border-radius: 10px;
}
::-webkit-scrollbar-thumb:hover {
  background: #bbb;
}
</style>
