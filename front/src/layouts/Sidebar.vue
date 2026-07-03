<script setup lang="ts">
import { useAuthStore } from "@/modules/Auth/stores/index"
import { useSidebar } from "@/composables/useSidebar";
import Menu from "@/layouts/menu.vue";
import { getAuthMenu } from "@/modules/Auth/services"
import { ref, onMounted } from "vue"

const menus = ref<any[]>([])
const loading = ref(false)
const store = useAuthStore()
const { isClose } = useSidebar()

onMounted(async () => {
  if (store.authUser) {
    loading.value = true
    const response = await getAuthMenu()
    menus.value = response.data
    loading.value = false
  }
})

</script>




<template>
  <aside class="flex h-screen" v-if="store.authUser && menus.length > 0">
    <!-- Backdrop -->
    <div :class="!isClose ? 'block' : 'hidden'" @click="isClose = true"
      class="fixed z-20 inset-0 bg-black opacity-50 transition-opacity lg:hidden">
    </div>
    <!-- End Backdrop -->

    <!-- on Mobile -->

    <div :class="!isClose ? 'translate-x-0 ease-out' : '-translate-x-full ease-in'"
      class="w-full h-full overflow-auto fixed z-30 inset-y-0 left-0 transition duration-300 transform overflow-y-auto lg:static lg:inset-0 block lg:hidden">
      <button @click="isClose = true"
        class="absolute right-5 top-5 w-10 h-10 rounded-full bg-white flex items-center justify-center focus:outline-none transition ease-in duration-[.1s] ">
        <font-awesome-icon icon="fa-solid fa-xmark" />
      </button>
      <Menu :menus="menus" @closeMenuAuto="(value) => isClose = value" />
    </div>

    <!-- on PC -->

    <div
      :class="!isClose ? 'translate-x-0 ease-out shadow-lg w-[40vw] md:w-[30vw] lg:w-[15vw] 2xl:w-[12vw]' : '-translate-x-1 ease-in w-full md:w-[9vw] 2xl:w-[4vw]'"
      class="fixed h-full overflow-auto rounded-r-[5vh] inset-y-0 left-0 transition duration-300 transform overflow-y-auto lg:static lg:inset-x-0 hidden md:block">
      <Menu :menus="menus" @closeMenuAuto="() => null" />
    </div>


  </aside>
</template>
