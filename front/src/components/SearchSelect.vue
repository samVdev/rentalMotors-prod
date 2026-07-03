<script setup lang="ts">
import { ref, computed, onMounted, onBeforeUnmount, watch, nextTick } from 'vue'

interface Item {
  [key: string]: any
}

const props = defineProps<{
  items: Item[]
  modelValue: string | number | null
  displayField?: string
  placeholder?: string
  name?: string
}>()

const emit = defineEmits(['update:modelValue'])

const search = ref('')
const open = ref(false)
const box = ref<HTMLElement | null>(null)
const boxWidth = ref(0)
const boxLeft = ref(0)
const boxTop = ref(0)

const displayField = props.displayField || 'name'

const filteredItems = computed(() => {
  if (!search.value) return props.items
  return props.items.filter(item => {
    const val = item[displayField]
    if (!val) return false
    return val.toString().toLowerCase().includes(search.value.toLowerCase())
  })
})

const updatePosition = () => {
  if (box.value) {
    const rect = box.value.getBoundingClientRect()
    boxWidth.value = rect.width
    boxLeft.value = rect.left
    boxTop.value = rect.top + rect.height
  }
}

const selectItem = (item: Item) => {
  search.value = item[displayField]
  emit('update:modelValue', item.id)
  open.value = false
}

const clickOutside = (e: Event) => {
  if (box.value && !box.value.contains(e.target as Node)) {
    open.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', clickOutside)
  window.addEventListener('resize', updatePosition)
  window.addEventListener('scroll', updatePosition, true)

  const selected = props.items.find(i => i.id === props.modelValue)
  if (selected) search.value = selected[displayField]
})

onBeforeUnmount(() => {
  document.removeEventListener('click', clickOutside)
  window.removeEventListener('resize', updatePosition)
  window.removeEventListener('scroll', updatePosition)
})

watch(open, async (isOpen) => {
  if (isOpen) {
    await nextTick()
    updatePosition()
  }
})

watch(
  () => props.modelValue,
  val => {
    const selected = props.items.find(i => i.id === val)
    search.value = selected ? selected[displayField] : ''
  }
)
</script>

<template>
  <div class="relative w-full" ref="box">
    <input
      type="text"
      v-model="search"
      @focus="open = true"
      :placeholder="placeholder || 'Buscar...'"
      class="p-3 w-full border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-[#FF7539] focus:border-[#FF7539]"
    />

    <input v-if="name" type="hidden" :name="name" :value="modelValue" />

    <ul
      v-if="open && filteredItems.length > 0"
      class="fixed z-50 bg-white border border-gray-200 rounded-xl shadow-lg mt-1 max-h-64 overflow-y-auto"
      :style="{ 
        width: `${boxWidth}px`, 
        left: `${boxLeft}px`, 
        top: `${boxTop}px` 
      }"
    >
      <li
        v-for="item in filteredItems"
        :key="item.id"
        @click="selectItem(item)"
        class="p-2 cursor-pointer hover:bg-gray-100 flex items-center gap-2 capitalize"
      >
        <img
          v-if="item.img"
          :src="item.img"
          alt="img"
          class="w-12 h-12 rounded-lg object-cover mr-2"
        />
        <span>{{ item[displayField] }}</span>
      </li>
    </ul>

    <div
      v-if="open && filteredItems.length === 0"
      class="fixed z-50 bg-white border border-gray-200 rounded-xl shadow-lg mt-1 p-3 text-gray-500"
      :style="{ 
        width: `${boxWidth}px`, 
        left: `${boxLeft}px`, 
        top: `${boxTop}px` 
      }"
    >
      No hay resultados
    </div>
  </div>
</template>