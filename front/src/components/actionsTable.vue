<script lang="ts" setup>
import { useAuthStore } from '@/modules/Auth/stores';

const props = defineProps<{
    showBtn?: boolean,
    editBtn?: boolean,
    deleteBtn?: boolean,
    recibesBtn?: boolean,
    expensesBtn?: boolean,
    earningsBtn?: boolean,
    emailBtn?: boolean,
    historyBtn?: boolean,
    reportElevatorBtn?: boolean,
    entryBtn?: boolean,
}>()

const store = useAuthStore()


</script>

<template>
  <div class="flex items-center gap-2 flex-wrap butons">

    <button
      @click="$emit('show')"
      class="bg-green-500 w-10 h-10 flex items-center justify-center rounded-full aspect-square text-white hover:opacity-80 transition"
      title="ver"
      v-if="showBtn"
    >
      <font-awesome-icon icon="fa-solid fa-eye" />
    </button>

    <button
      @click="$emit('entry')"
      class="bg-blue-600 w-10 h-10 flex items-center justify-center rounded-full aspect-square text-white hover:opacity-80 transition"
      title="Entrar"
      v-if="entryBtn"
    >
      <font-awesome-icon icon="right-to-bracket" />
    </button>

    <button
      @click="$emit('edit')"
      class="bg-blue-600 w-10 h-10 flex items-center justify-center rounded-full aspect-square text-white hover:opacity-80 transition"
      title="Editar este registro"
      v-if="editBtn"
    >
      <font-awesome-icon icon="pen" />
    </button>

    <button
      @click="$emit('earnings')"
      class="bg-green-600 w-10 h-10 flex items-center justify-center rounded-full aspect-square text-white hover:opacity-80 transition"
      title="Ver Ingresos"
      v-if="earningsBtn"
    >
      <font-awesome-icon icon="money-bill-trend-up" />
    </button>

    <button
      @click="$emit('report')"
      class="bg-orange-500 w-10 h-10 flex items-center justify-center rounded-full aspect-square text-white hover:opacity-80 transition"
      title="Hacer reporte"
      v-if="reportElevatorBtn"
    >
      <font-awesome-icon icon="flag" />
    </button>

    <button
      @click="$emit('history')"
      class="bg-green-600 w-10 h-10 flex items-center justify-center rounded-full aspect-square text-white hover:opacity-80 transition"
      title="Ver historial"
      v-if="historyBtn"
    >
      <font-awesome-icon icon="timeline" />
    </button>

    <button
      @click="$emit('expenses')"
      class="bg-blue-600 w-10 h-10 flex items-center justify-center rounded-full aspect-square text-white hover:opacity-80 transition"
      title="Ver Gastos"
      v-if="expensesBtn"
    >
      <font-awesome-icon icon="money-bill-transfer" />
    </button>

    <button
      @click="$emit('email')"
      class="bg-orange-600 w-10 h-10 flex items-center justify-center rounded-full aspect-square text-white hover:opacity-80 transition"
      title="Enviar por email"
      v-if="emailBtn"
    >
      <font-awesome-icon icon="envelope" />
    </button>

    <button
      @click="$emit('recives')"
      class="bg-orange-500 w-10 h-10 flex items-center justify-center rounded-full aspect-square text-white hover:opacity-80 transition"
      title="Ver Recibos pendientes"
      v-if="recibesBtn"
    >
      <font-awesome-icon icon="receipt" />
    </button>

    <button
      @click="$emit('remove')"
      class="bg-red-600 w-10 h-10 flex items-center justify-center rounded-full aspect-square text-white hover:opacity-80 transition"
      title="Eliminar este registro"
      v-if="deleteBtn && store.authUser?.role_id <= 2"
    >
      <font-awesome-icon icon="trash" />
    </button>
  </div>
</template>
