<script setup lang="ts">
import { reactive, ref } from "vue"
import Loader from "@/components/Loader.vue";
import SelectType from "./SelectType.vue";
import SearchSelect from "@/components/SearchSelect.vue";
import type User from "@/modules/User/types/User";
import { descripcionesPredefinidas } from "@/utils/constantes/MantenenceTypes";

const props = defineProps<{
    id?: string
    data: any
    sending: boolean
    errors: any
    users: User[]
}>()

const emit = defineEmits<{
    (e: 'submit', data: any, userId?: string): void
}>()

const form = reactive(props.data)

const dataSubmited = ref([])

const mostrarTextarea = ref(false);

const submit = async () => {
    emit('submit', {
        type: form.type,
        id_for_mant: form.id_for_mant,
        fecha: form.fecha,
        descripcion: form.descripcion,
        persona_id: form.persona_id,
    }, props.id)
}

const putInfo = async (payload: { data: any[], type: number, cedula: string }) => {
    dataSubmited.value = payload.data
    form.type = payload.type
    form.cedula = payload.cedula
}

const handleTipoChange = (event: Event) => {
    const target = event.target as HTMLSelectElement;
    const value = target.value;

    if (value === 'otros') {
        mostrarTextarea.value = true;
        form.descripcion = '';
    } else {
        mostrarTextarea.value = false;
        form.descripcion = descripcionesPredefinidas[value];
    }
}

</script>

<template>
    <SelectType :form="form" @data="putInfo" v-if="dataSubmited.length == 0" />

    <form @submit.prevent="submit" class="p-6 space-y-6 h-full" v-else>

        <p>Mantenimiento para: <strong>{{ form.cedula }}</strong> en la modalidad de <strong>{{ form.type == 1 ? 'Financiamiento' : 'Contado' }}</strong></p>

        <div class="grid lg:grid-cols-2 gap-6">

            <div v-if="dataSubmited.length > 0">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Vehiculo</label>
                <SearchSelect v-model="form.id_for_mant" :items="dataSubmited" displayField="vehicle_label"
                    placeholder="Buscar ..." />
            </div>

            <div>
                <label for="fecha" class="block text-sm font-semibold text-gray-700 mb-2">Fecha para el
                    mantenimiento</label>
                <input type="date" id="fecha" required v-model="form.fecha" placeholder="fecha"
                    class="p-3 w-full border border-gray-200 rounded-xl shadow-sm focus:ring-2 focus:ring-[#FF7539] focus:border-[#FF7539] transition-all" />
            </div>

            <div v-if="users.length > 0 && !id">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Encargado</label>
                <SearchSelect v-model="form.persona_id" :items="users" displayField="name"
                    placeholder="Buscar cliente..." />
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Tipo de Servicio</label>
            <select @change="handleTipoChange"
                class="p-3 w-full border border-gray-200 rounded-xl shadow-sm focus:ring-2 focus:ring-[#FF7539] focus:border-[#FF7539] transition-all bg-white">
                <option value="" disabled selected>Seleccione una opción...</option>
                <option value="cambio_aceite">🛢️ Cambio de Aceite</option>
                <option value="preventivo">🔧 Mantenimiento Preventivo</option>
                <option value="correctivo">⚠️ Mantenimiento Correctivo</option>
                <option value="revision">🔍 Revisión General</option>
                <option value="llantas">⭕ Llantas</option>
                <option value="frenos">🛑 Frenos</option>
                <option value="transmision">⚙️ Transmisión</option>
                <option value="cadena">🔗 Cadena</option>
                <option value="filtros">🧽 Filtros</option>
                <option value="otros">🔨 Otros (Especificar)</option>
            </select>
        </div>

        <div v-if="mostrarTextarea" class="transition-all duration-300">
            <label for="descripcion" class="block text-sm font-semibold text-gray-700 mb-2">Especifique la Descripción</label>
            <textarea id="descripcion" placeholder="Escriba aquí los detalles del trabajo..." v-model="form.descripcion"
                maxlength="500" required
                class="p-3 w-full border border-gray-200 rounded-xl shadow-sm focus:ring-2 focus:ring-[#FF7539] focus:border-[#FF7539] transition-all resize-none h-32" />
        </div>

        <div class="flex justify-end space-x-3">
            <button v-if="!sending" type="submit"
                class="w-full md:w-auto bg-[#FF7539] hover:bg-[#D54A5C] text-white px-6 py-3 rounded-2xl font-bold shadow-md transition transform hover:scale-[1.02]">
                Guardar
            </button>

            <Loader v-else class="mx-auto" />
        </div>
    </form>
</template>

<style>
#password {
    @apply p-3 w-full border border-gray-200 shadow-sm focus:ring-2 focus:ring-[#FF7539] focus:border-[#FF7539] transition-all;
}
</style>