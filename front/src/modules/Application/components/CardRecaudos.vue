<script lang="ts" setup>
import { ref } from "vue";
import type { applyInterface } from "../types/applyType";
import ApplyService from "../services";

const props = defineProps<{
  row: applyInterface;
}>();

const expandedRow = ref<number | null>(null);
const loadingInvoice = ref(false);

function toggleDetails(id: number) {
  expandedRow.value = expandedRow.value === id ? null : id;
}

async function handleDownloadInvoice() {
  try {
    loadingInvoice.value = true;
    await ApplyService.downloadInvoice(props.row.id, props.row.codigo);
  } catch (e) {
    alert('No se pudo generar la factura. Intenta de nuevo.');
  } finally {
    loadingInvoice.value = false;
  }
}
</script>

<template>
  <div :key="row.id">
    <div class="grid grid-cols-4 fakeTable-body cursor-pointer relative hover:bg-gray-100 items-center"
      @click="toggleDetails(row.id)">
      <p><strong>Fecha</strong> <br />{{ row.date }}</p>
      <p><strong>Cédula o PPT</strong> <br />{{ row.cedula }}</p>
      <p><strong>Teléfono</strong> <br />{{ row.phone }}</p>
      <p><strong>Código</strong> <br />{{ row.codigo ? row.codigo : 'Sin codigo' }}</p>

      <div class="absolute right-10 top-1/2 -translate-y-1/2 select-none text-gray-600 text-2xl">
        <span>
          {{ expandedRow === row.id ? '▲' : '▼' }}
        </span>
      </div>
    </div>

    <div v-if="expandedRow === row.id" class="bg-gray-50 p-4 border rounded-lg grid grid-cols-3 gap-4 mb-2">
      <div class="col-span-2">
        <h3 class="text-lg mb-2"> <strong>Detalles de la solicitud </strong>- ({{ row.codigo ? row.codigo : '' }})</h3>

        <div class="grid grid-cols-2 gap-3">
          <p><span class="font-semibold">Nombre Completo:</span> {{ row.full_name }}</p>
          <p><span class="font-semibold">Teléfono:</span> {{ row.phone }}</p>
          <p><span class="font-semibold">Cédula:</span> {{ row.cedula }}</p>
          <p><span class="font-semibold">Tipo de financiamiento:</span> {{ row.financiacion_type }}</p>
          <p><span class="font-semibold">Plan de cuotas:</span> {{ row.plan }}</p>
          <p class="col-span-2"><span class="font-semibold">Código:</span> {{ row.codigo ? row.codigo : 'Sin codigo' }}
          </p>

          <div class="mt-4">
            <p class="font-semibold mb-1">Documento PDF:</p>
            <div v-if="row.document">
              <p @click="$emit('document', 'document')" class="text-blue-600 underline cursor-pointer">
                Ver documento
              </p>
            </div>
            <p v-else class="text-gray-400">No enviado</p>
          </div>

          <div class="mt-4">
            <p class="font-semibold mb-1">Primer pago:</p>
            <div v-if="row.pay_one">
              <p class="text-blue-600 underline cursor-pointer" @click="$emit('document', 'pay_one')">
                Ver captura
              </p>
            </div>
            <p v-else class="text-gray-400">No enviado aun</p>
          </div>
        </div>


      </div>

      <div class="flex flex-col items-center justify-center border-l pl-4">
        <img v-if="row.vehicle_image" :src="row.vehicle_image" alt="Vehículo"
          class="w-42 h-36 object-cover rounded mb-2" />
        <p class="font-semibold">{{ row.vehicle_brand }}</p>
        <p class="text-gray-600">{{ row.vehicle_model }}</p>

        <div class="mt-4 flex gap-2">
          <template v-if="$route.query.type === 'pending'">
            <button class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600"
              @click.stop="$emit('statusChange', { id: row.id, status: true })">
              Aceptar &nbsp;
              <font-awesome-icon icon="fa-solid fa-check" />
            </button>
            <button class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600"
              @click.stop="$emit('statusChange', { id: row.id, status: false })">
              Rechazar &nbsp;
              <font-awesome-icon icon="fa-solid fa-xmark" />
            </button>
          </template>

          <template v-else-if="$route.query.type === 'accept' && $route.name == 'view-apply'">
            <!-- Invoice button: visible only when client and financing exist -->

            <template v-if="row.document">
              <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 font-bold flex items-center gap-2 shadow-sm"
                @click.stop="$emit('actualizar_recaudos', row.id)">
                Actualizar Recaudos &nbsp;
                <font-awesome-icon icon="fa-solid fa-upload" />
              </button>
            </template>
            <div v-else class="px-4 py-2 bg-gray-100 text-gray-500 rounded font-bold border border-gray-200 flex items-center gap-2 text-sm">
              <font-awesome-icon icon="fa-solid fa-circle-info" />
              Sin recaudos
            </div>

            <button
              v-if="(row.pay_one && row.estatus && row.financiacion_type == 'financiación') || row.financiacion_type == 'De contado'"
              class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
              @click.stop="$router.push({ name: 'clientEdit', params: { uuid: row.client_id }, query: { apply_id: row.id } })">
              Cliente &nbsp;
              <font-awesome-icon icon="fa-solid fa-user-pen" />
            </button>

            <button v-else-if="!row.pay_one && row.estatus"
              class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600" @click.stop="$emit('anexar_doc')">
              Anexar pago Inicial &nbsp;
              <font-awesome-icon icon="fa-solid fa-file" />
            </button>


            <button v-if="row.client_id" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
              @click.stop="$router.push(`/clients/show/s/${row.client_id}`)">
              Ver cliente &nbsp;
              <font-awesome-icon icon="fa-solid fa-eye" />
            </button>

            <button v-else-if="!row.pay_one && row.estatus"
              class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600"
              @click.stop="$emit('statusChange', { id: row.id, status: false })">
              Rechazar &nbsp;
              <font-awesome-icon icon="fa-solid fa-xmark" />
            </button>
          </template>

          <template v-else-if="$route.query.type === 'reject'">
            <button class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600"
              @click.stop="$emit('statusChange', { id: row.id, status: true })">
              Aceptar &nbsp;
              <font-awesome-icon icon="fa-solid fa-check" />
            </button>
          </template>
        </div>
      </div>
    </div>
  </div>
</template>
