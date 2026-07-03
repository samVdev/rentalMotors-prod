<script setup lang="ts">
import { ref, onMounted } from "vue"
import Http from "@/utils/Http";

interface Props {
    path: string,
    client?: boolean
}

const props = defineProps<Props>()

const loading = ref(true)
const fileURL = ref("")
const isImage = ref(false)
const isPDF = ref(false)

onMounted(async () => {
    try {
        const url = props.client ? 'api/home-client/doc/guest/' : 'api/documents/'
        const response = await Http.getFile(`${url}${props.path}`)

        const blob = response.data
        const mime = blob.type

        fileURL.value = URL.createObjectURL(blob)
        isImage.value = mime.startsWith("image/")
        isPDF.value = mime === "application/pdf"

    } catch (e) {
        console.error("Error cargando archivo privado:", e)
    } finally {
        loading.value = false
    }
})
</script>


<template>
    <div>
        <div v-if="loading" class="text-gray-500">Cargando archivo...</div>

        <div class="text-center" v-else>
            <img v-if="isImage" :src="fileURL" class="max-w-full max-h-[80vh] object-contain rounded shadow mx-auto" />
            <iframe v-else-if="isPDF" :src="fileURL" class="w-full h-[80vh] border rounded"></iframe>
            <p v-else class="text-red-500">
                No se puede visualizar este tipo de archivo.
            </p>
        </div>
    </div>
</template>