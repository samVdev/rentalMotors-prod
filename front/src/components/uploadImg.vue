<script lang="ts" setup>
const props = defineProps<{
    img: string
}>()

const $emit = defineEmits(['setImg'])

function handleBackgroundUpload(event: Event): void {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0]; 

    if (!file) return

    const Reader = new FileReader()
    Reader.onload = function (e) {
        $emit('setImg', {
            imgFile: file,
            imgURL: e.target.result,
        });
    }
    Reader.readAsDataURL(file)
}
</script>

<template> 
<div class="flex items-center justify-center w-full relative">
    <img :src="img" alt="" class="absolute inset-0 mx-auto h-full" accept="image/*" v-if="img">
    <label for="dropzone-file" 
    :class="img ? 'bg-[#ffeaea49]' : 'bg-gray-50'"
    class="flex flex-col items-center justify-center z-[1] w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer">
        <div class="flex flex-col items-center justify-center pt-5 pb-6">
            <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
            </svg>
            <p class="mb-2 text-sm text-gray-500 dark:text-gray-400">Click para subir</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">SVG, PNG, JPG</p>
        </div>
        <input id="dropzone-file" name="file" type="file" class="mt-[-4vh] opacity-0" @change="handleBackgroundUpload"/>
    </label>
</div> 
</template>