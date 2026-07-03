<script setup lang="ts">
import { ref, computed } from "vue";
import { useAuthStore } from "@/modules/Auth/stores"
import { useSidebar } from "@/composables/useSidebar"

const url = import.meta.env.VITE_APP_API_URL

const { isOpen, isClose } = useSidebar()
const dropdownOpen = ref(false)
const store = computed(() => useAuthStore())

const logout = async () => await store.value.logout()

</script>

<template>
  <header
    class="sticky z-5 w-[95%] top-[1vh] mx-auto rounded-2xl flex justify-between items-center p-3 text-[#FF7539]">
    <div
      class="transition ease-in duration-[.1s] justify-center hover:bg-white hover:text-[#FF7539] rounded-2xl border-1 shadow-md text-white bg-[#FF7539]">
      <button @click="isClose = !isClose;" class="flex items-center gap-3 px-4 py-3 text-sm font-medium">
        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M4 6H20M4 12H20M4 18H11" stroke="currentColor" stroke-width="2" stroke-linecap="round"
            stroke-linejoin="round" />
        </svg>
      </button>
    </div>


    <div class="flex gap-5 justify-center items-center justify-self-end">
      <div
        class="transition ease-in duration-[.1s] justify-center bg-white rounded-2xl border-1 shadow-md hover:text-white hover:bg-[#FF7539]">
        <router-link :to="`/profile`" :title="`Ir a perfil`"
          class="flex items-center gap-3 px-4 py-4 text-sm font-medium">
          <font-awesome-icon icon="fa-solid fa-user-pen" class="md:h-[3vh] 2xl:h-[2vh]" />
        </router-link>
      </div>

      <div
        class="transition ease-in duration-[.1s] justify-center bg-white rounded-2xl border-1 shadow-md hover:text-white hover:bg-[#FF7539]">
        <button @click="logout" class="flex items-center gap-3 px-4 py-4 text-sm font-medium w-full">
          <font-awesome-icon icon="fa-solid fa-arrow-right-from-bracket" class="md:h-[3vh] 2xl:h-[2vh]" />
        </button>
      </div>
    </div>


  </header>
</template>
