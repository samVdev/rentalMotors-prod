<template>
  <div class="min-h-screen w-full bg-gray-50 flex flex-col items-center justify-center p-4 relative overflow-hidden">
    <div class="absolute top-[-10%] right-[-10%] w-[40vw] h-[40vw] bg-orange-100 rounded-full blur-3xl opacity-50">
    </div>
    <div class="absolute bottom-[-10%] left-[-10%] w-[30vw] h-[30vw] bg-orange-200 rounded-full blur-3xl opacity-30">
    </div>

    <div class="z-10 text-center max-w-md w-full">
      <div class="mb-10 transform transition-all duration-700 hover:scale-105">
        <img src="/logo.png" alt="Logo" class="w-48 mx-auto drop-shadow-xl" />
      </div>

      <div class="relative inline-block mb-6">
        <h1 class="text-9xl font-black text-orange-600/20 select-none">404</h1>
        <div class="absolute inset-0 flex items-center justify-center">
          <p class="text-3xl font-black text-gray-800 uppercase tracking-tighter">Oops!</p>
        </div>
      </div>

      <h2 class="text-2xl font-bold text-gray-800 mb-4">Acceso no encontrado</h2>
      <p class="text-gray-600 mb-10 leading-relaxed px-4">
        Parece que intentas acceder a una sección que no existe o a la que no tienes permisos actualmente.
      </p>

      <button @click="$router.go(-1)"
        class="inline-flex items-center gap-3 bg-[#FF7539] hover:bg-[#e66932] text-white font-black py-4 px-10 rounded-2xl shadow-lg shadow-orange-200 transition-all duration-300 transform hover:-translate-y-1 active:scale-95 group">
        <font-awesome-icon icon="fa-solid fa-arrow-left" class="transition-transform group-hover:-translate-x-1" />
        VOLVER ATRÁS
      </button>
    </div>
  </div>
</template>

<style scoped>
.z-10 {
  animation: fadeIn 0.8s ease-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
