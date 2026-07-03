<script lang="ts" setup>
import { computed } from 'vue';
import { useSidebar } from '@/composables/useSidebar';
import { useAuthStore } from '@/modules/Auth/stores';
import ViewFile from '@/components/ViewFile.vue';

const { isClose } = useSidebar()
const store = computed(() => useAuthStore())
const user = computed(() => store.value.authUser)

const props = defineProps<{
  menus: any
}>()

</script>

<template>
  <nav class="overflow-hidden mt-0 flex flex-col"
    :class="isClose ? `h-full items-center justify-center lg:ml-5 text-[#FF7539] py-4` : 'bg-[#FF7539] text-white h-full pb-4'">

    <div v-if="!isClose" class="shrink-0">
      <img src="/logo.png" class="w-[60%] mx-auto block my-8">
    </div>

    <ul class="flex-1 w-full" :class="isClose ? `flex flex-col items-center justify-center overflow-auto` : 'overflow-auto'">
      <li v-for="item in menus" :key="item.name"
        :class="isClose ? `justify-center bg-white rounded-full border-1 shadow-md overflow-hidden hover:text-white hover:bg-[#FF7539] activeOpen w-12 h-12 flex items-center` : 'hover:bg-white hover:text-[#FF7539] activeClose'"
        class="my-4 mx-4 rounded-xl transition ease-in duration-[.1s]">
        <router-link :to="`/${item.path}`" :title="`Ir a ${item.title}`" :class="isClose ? `justify-center` : ''"
          class="flex items-center gap-3 px-4 py-3 text-sm font-medium w-full h-full"
          @click="$emit('closeMenuAuto', true)">
          <font-awesome-icon :icon="item.icon" class="md:h-[3vh] 2xl:h-[2vh]" />
          <p :class="isClose ? `hidden` : 'block capitalize'">{{ item.title }}</p>
        </router-link>
      </li>
    </ul>

    <div v-if="user && user.role_id == 3" class="shrink-0 mt-auto transition-all w-full"
      :class="isClose ? 'px-0 pt-6 pb-2' : 'px-4 pt-6 pb-6 border-t border-white/20'">
      <div class="flex flex-col items-center gap-3 w-full">
        <ViewFile v-if="user.avatar" :path="user.avatar" :client="true"
          class="rounded-full object-cover border-white/60 shrink-0 shadow-lg bg-white overflow-hidden transition-all duration-300"
          :class="isClose ? 'w-12 h-12 border-2' : 'w-24 h-24 border-4 shadow-xl'" />
        <div v-else
          class="rounded-full bg-white/20 flex items-center justify-center border-white/60 shrink-0 shadow-lg transition-all duration-300"
          :class="isClose ? 'w-12 h-12 border-2' : 'w-24 h-24 border-4 shadow-xl'">
          <font-awesome-icon icon="fa-solid fa-user" class="text-white" :class="isClose ? 'text-xl' : 'text-4xl'" />
        </div>

        <div v-if="!isClose"
          class="flex flex-col items-center justify-center overflow-hidden w-full px-2 text-center mt-2">
          <span class="text-lg xl:text-xl font-bold truncate text-white drop-shadow-sm leading-tight w-full">{{
            user.name || 'Cliente' }}</span>
          <span
            class="text-sm text-orange-50 font-semibold tracking-wide mt-1.5 bg-black/10 px-4 py-1 rounded-full uppercase">C.I:
            {{ user.ci || 'N/A' }}</span>
        </div>
      </div>
    </div>

  </nav>
</template>

<style>
.activeClose>.router-link-active {
  @apply rounded-xl bg-[white] text-[#FF7539]
}

.activeOpen>.router-link-active {
  @apply rounded-xl bg-[#FF7539] text-white
}
</style>