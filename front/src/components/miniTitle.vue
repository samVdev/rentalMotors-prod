<script lang="ts" setup>
const props = defineProps<{
    text: string
}>()
</script>

<template>
    <div class="text-lg">
        <h2 class="font-bold text-gray-800 flex items-center gap-2">
          <span class="w-1.5 h-6 bg-[#FF7539] rounded"></span>
          {{text}}
        </h2>
      </div>
</template>