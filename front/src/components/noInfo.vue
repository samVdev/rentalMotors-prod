<script lang="ts" setup>

const props = defineProps<{
    title: string,
    icon: string,
}>()
</script>

<template>
    <article class="w-full h-full grid justify-center items-center text-gray-500">
        <div class="text-center">
            <font-awesome-icon :icon="icon" class="text-3xl mb-6 md:text-7xl"/>
            <h5 class="mb-4 text-2xl font-normal leading-none tracking-tight md:text-3xl">{{ title }}</h5>
        </div>
    </article>
</template>