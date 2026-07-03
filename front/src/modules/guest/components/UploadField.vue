<script setup lang="ts">
import { alerta } from '@/utils/alert';
import { ref, watch } from 'vue'

const props = defineProps<{
  id: string
  label: string
  initialUrl?: string
}>()

const emit = defineEmits<{
  (e: 'setImg', payload: { imgFile: File | null; imgURL: string | null }): void
}>()

const preview = ref<string | null>(props.initialUrl ?? null)
const filename = ref<string | null>(null)
const fileInput = ref<HTMLInputElement | null>(null)

watch(() => props.initialUrl, (v) => {
  preview.value = v ?? preview.value
})

function handleUpload(event: Event) {
  const target = event.target as HTMLInputElement
  const file = target.files?.[0] ?? null

  if (!file) {
    preview.value = null
    filename.value = null
    emit('setImg', { imgFile: null, imgURL: null })
    return
  }

  const validTypes = ['image/png', 'image/jpeg']
  if (!validTypes.includes(file.type)) {
    target.value = ''
    preview.value = null
    filename.value = null
    emit('setImg', { imgFile: null, imgURL: null })

    alerta('Info', 'Solo se permiten imágenes PNG o JPG.', 'info')

    return
  }

  filename.value = file.name

  const reader = new FileReader()
  reader.onload = () => {
    preview.value = String(reader.result)
    emit('setImg', { imgFile: file, imgURL: preview.value })
  }
  reader.readAsDataURL(file)
}


function clear() {
  preview.value = null
  filename.value = null
  if (fileInput.value) fileInput.value.value = ''
  emit('setImg', { imgFile: null, imgURL: null })
}
</script>

<template>
  <div class="flex flex-col gap-2">
    <div class="flex items-center justify-between">
      <span class="font-medium text-xl text-[#1F2937]">{{ label }}</span>
      <button v-if="preview" @click="clear" type="button"
        class="text-xs px-2 py-1 rounded bg-red-50 text-red-600 hover:bg-red-100 transition">
        Eliminar imagen
      </button>
    </div>

    <label :for="id"
      class="flex flex-col items-center justify-center gap-4 cursor-pointer border-2 border-dashed rounded-xl p-6 hover:border-[#FF7539] hover:bg-orange-50/30 transition-all group">

      <div
        class="w-32 h-24 flex-shrink-0 rounded-lg bg-gray-50 overflow-hidden flex items-center justify-center border border-gray-100 shadow-sm group-hover:scale-105 transition-transform">
        <img v-if="preview" :src="preview" class="object-cover w-full h-full" />
        <div v-else class="flex flex-col items-center">
          <svg class="w-10 h-10 text-gray-400 group-hover:text-[#FF7539] transition-colors"
            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
              d="M12 3v9m0 0l3-3m-3 3-3-3M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
          </svg>
        </div>
      </div>

      <div class="text-center">
        <div class="text-sm font-medium text-gray-700">Haz click aquí</div>
        <div class="text-xs text-gray-400 mt-1 uppercase tracking-wider">PNG o JPG (máx 20MB)</div>

        <div v-if="filename"
          class="mt-3 px-3 py-1 bg-white border border-gray-200 rounded-full text-[11px] text-gray-500 inline-block">
          📄 {{ filename }}
        </div>
      </div>

      <input :id="id" :name="id" ref="fileInput" class="hidden" type="file" accept="image/png, image/jpeg"
        @change="handleUpload" />
    </label>
  </div>
</template>