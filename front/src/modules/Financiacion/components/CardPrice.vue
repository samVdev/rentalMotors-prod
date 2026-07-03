<script lang="ts" setup>

const props = defineProps<{
    label: string,
    content: string,
    color?: string,
    showEdit?: boolean
}>()

const emit = defineEmits(['edit'])
</script>

<template>

    <article class="bg-white p-3 py-4 rounded-2xl shadow-md text-center border border-gray-200 relative">
        <p class="font-bold">
            {{ label }}
        </p>
        <p class="text-3xl font-bold mt-2" :class="color">
            {{ content}}
        </p>
        <button v-if="showEdit" @click="emit('edit')" class="absolute top-2 right-2 transition p-1 text-xs text-indigo-400 hover:text-indigo-600">
            <font-awesome-icon icon="fa-solid fa-pen" />
        </button>
    </article>
</template>