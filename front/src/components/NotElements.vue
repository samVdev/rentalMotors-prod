<script lang="ts" setup>

const props = defineProps<{
    title: string,
    subtitle?: string,
}>()

</script>

<template>
    <div class="p-8 text-center max-w-md mx-auto">
            <font-awesome-icon icon="ban" class="h-[6vh] text-[#E65729] my-5"/>
            <h1 class="text-2xl font-bold text-gray-800 mb-2">     
                {{ title }}
            </h1>
            <p class="text-gray-600">
                {{ subtitle }}
            </p>
        </div>
</template>