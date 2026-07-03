<script lang="ts" setup>
const props = defineProps<{
  icon: string,
  color: string,
  value: any,
  label: string,
}>()
</script>

<template>
  <article
    class="ThisDiv flex items-center justify-between rounded-2xl p-6 bg-white border border-gray-100 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all cursor-pointer"
  >
    <div class="flex items-center gap-4">
      <div
        :class="color"
        class="w-14 h-14 flex items-center justify-center rounded-full shadow-inner"
      >
        <font-awesome-icon :icon="icon" class="w-6 h-6" />
      </div>
      <div>
        <p class="text-sm font-medium text-gray-500">{{ label }}</p>
        <h2 class="text-2xl font-bold text-gray-800">{{ value }}</h2>
      </div>
    </div>

  </article>
</template>

<style scoped>
.ThisDiv {
    animation: swing-top 1s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
</style>