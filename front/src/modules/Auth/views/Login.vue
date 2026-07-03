<script setup lang="ts">
import Welcome from "@/components/welcome.vue";
import FormLogin from "../components/FormLogin.vue";
import { useLogin } from '../composables/useLogin'

const { login, sending } = useLogin()
</script>

<template>
  <main class="block bg-[#F4F5FA] md:grid relative w-full h-screen overflow-auto z-[10] md:pb-[24vh]">

    <div class="mt-[-6vh]">
      <Welcome />
    </div>

    <div 
      class="slide-in-bottom md:absolute mt-5 md:mt-0 bottom-0 left-0 right-0 p-5 mx-auto w-11/12 bg-white rounded-3xl border border-gray-300 shadow-2xl md:mb-[-12vh]">
      <h2 class="my-4 text-2xl text-left mx-10">Iniciar Sesión</h2>
      <FormLogin @submit='login($event)' :sending='sending' />
    </div>

  </main>
</template>


<style scoped>
.slide-in-bottom {
	-webkit-animation: slide-in-bottom 0.5s cubic-bezier(0.250, 0.460, 0.450, 0.940) both;
	        animation: slide-in-bottom 0.5s cubic-bezier(0.250, 0.460, 0.450, 0.940) both;
}

 @-webkit-keyframes slide-in-bottom {
  0% {
    -webkit-transform: translateY(1000px);
            transform: translateY(1000px);
    opacity: 0;
  }
  100% {
    -webkit-transform: translateY(0);
            transform: translateY(0);
    opacity: 1;
  }
}
@keyframes slide-in-bottom {
  0% {
    -webkit-transform: translateY(1000px);
            transform: translateY(1000px);
    opacity: 0;
  }
  100% {
    -webkit-transform: translateY(0);
            transform: translateY(0);
    opacity: 1;
  }
}

</style>

