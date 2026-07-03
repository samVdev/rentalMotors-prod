<script lang="ts" setup>

const props = defineProps<{
    title: string
    subtitle: string
    icon: string
}>()

</script>

<template>
    <article class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-all duration-300 ease-in-out">
        <div class="w-12 h-12 rounded-lg bg-[#FDEDEF] flex items-center justify-center mb-4">
            <font-awesome-icon :icon="icon" class="lucide lucide-bell h-6 w-6 text-[#FF7539]"/>
        </div>
        <h5 class="text-xl font-semibold mb-2 text-gray-900">{{ title }}</h5>
        <p class="text-gray-700">{{ subtitle }}</p>
    </article>
</template>