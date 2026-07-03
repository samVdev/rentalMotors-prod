<script setup lang="ts">
import InputPassword from "@/components/inputPassword.vue"
import { ref } from "vue"

defineProps({
  sending: Boolean
})
const emit = defineEmits(['submit'])
const user = ref(null)
const password = ref(null)
const submit = async () => {
  emit('submit', {
    user: user.value,
    password: password.value
  })
}
</script>

<template>
  <form @submit.prevent="submit" class="grid md:grid-cols-3 gap-5 p-5">
    <div>
      <p>Usuario</p>
      <input type="text" name="user" v-model="user" autocomplete="email" placeholder="email@domain.ext"
        class="mb-2 my-3 !p-[15px] !h-auto " />
    </div>

    <div>
      <p>Contraseña</p>
      <InputPassword v-model="password" name="password" placeholder="Ingresa la contraseña" class="mb-4 my-3"/>
    </div>

    <div class="block my-6">

      <button
        class="block w-3/4 mb-4 mt-3 bg-[#FF7539] rounded-md px-3 py-2 font-bold hover:bg-[#010c41] hover:w-4/5 text-white mx-auto cursor-pointer !p-[15px] transition-all"
        id="buttonSubmit" type="submit" :isDisabled='sending' data-testid="submit-btn">
        {{ sending ? 'Iniciando sesión...' : 'Iniciar sesión' }}
      </button>
    </div>

  </form>
</template>


<style>

#password {
  padding: 20px!important;
  height: 5.5vh;
}

</style>
