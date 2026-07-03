<script setup lang="ts">
import { onMounted} from "vue";
import useEditProfile from "../composables/useEditProfile";
import InputPassword from "@/components/inputPassword.vue";

const { 
  userInfo,
  passwords,
  getUser,
  submitInfoUser,
  submitPasswordUser
} = useEditProfile()


onMounted(() => {
  getUser()
})

</script>

<template>
  <div class="p-0 xl:px-0">
      <div class="w-full mx-auto py-5 sm:px-5 lg:px-5 md:w-[80%]">
        <div class="flex gap-2 items-center text-xl mb-4 text-gray-800">
          <font-awesome-icon icon="user-gear" />
          <h1 class="font-bold">Mi Perfil</h1>
        </div>

        <form @submit.prevent="submitInfoUser" class="bg-white pt-5 pb-20 px-20 w-full shadow-lg rounded-xl">
          <h4 class="my-5 text-gray-400 text-xl">Datos personales</h4>
          <div class="grid gap-4 mb-4 sm:grid-cols-2 sm:gap-6 sm:mb-5">
              <div class="sm:col-span-2">
                  <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nombre Completo</label>
                  <input v-model="userInfo.name" type="text" name="name" id="name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5" 
                  placeholder="Ingresa tu nombre" required>
              </div>
              <div class="w-full">
                  <label for="brand" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Correo</label>
                  <input type="text" v-model="userInfo.email" name="email" id="email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5" 
                  placeholder="Ingresa tu correo" required>
              </div>
              <div class="w-full">
                  <label for="price" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Teléfono</label>
                  <input type="tel" v-model="userInfo.tel" name="tel" id="tel" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5" 
                  placeholder="Ingresa tu teléfono" required>
              </div>
          </div>
            <button type="submit" class="text-white block bg-[#FF7539] hover:bg-[#f3455c] focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                  Actualizar información
            </button>
      </form>

      <form @submit.prevent="submitPasswordUser" class="bg-white pt-5 pb-20 px-20 w-full shadow-lg rounded-xl mt-10">
          <h4 class="my-5 text-gray-400 text-xl">Contraseña</h4>
          <div class="grid gap-4 mb-4 sm:grid-cols-2 sm:gap-6 sm:mb-5">
              <div class="sm:col-span-2">
                  <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Contraseña actual</label>
                  <InputPassword v-model="passwords.current_password" name="current_password" placeholder="Ingresa tu contraseña actual" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"/>
              </div>
              <div class="w-full">
                  <label for="brand" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nueva Contraseña</label>
                  <InputPassword v-model="passwords.password" name="password" placeholder="Ingresa tu nueva contraseña" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"/>
              </div>
              <div class="w-full">
                  <label for="price" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Confirmar nueva Contraseña</label>
                  <InputPassword v-model="passwords.password_confirmation" name="password_confirmation" placeholder="Ingresa tu nueva contraseña nuevamente" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"/>
              </div>
          </div>
            <button type="submit" class="text-white block bg-[#FF7539] hover:bg-[#f3455c] focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                  Actualizar contraseña
            </button>
      </form>
      </div>
  </div>
</template>
