<script lang="ts" setup>

const props = defineProps<{
    icon?: string,
    text?: string
}>()
</script>

<template>
    <article class="grid place-items-center text-center text-[#808080] w-full h-full">
        <div class="scale-[1.5]">
            <font-awesome-icon :icon="icon || 'folder-open'" class="h-[5vh] mx-auto"/>
            <p class="mt-2 text-md md:mt-5 md:text-xl">{{ text || 'No se encontraron resultados' }}</p>
        </div>
    </article>
</template>