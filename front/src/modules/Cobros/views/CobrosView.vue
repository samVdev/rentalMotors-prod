<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import CobrosService from '../services/index';
import Loader from "@/components/Loader.vue";
import { toast } from "vue3-toastify";
import Swal from 'sweetalert2';
import TablesHeader from '@/components/tablesHeader.vue';

// Components
import SummaryCard from '../components/SummaryCard.vue';
import PendingPaymentItem from '../components/PendingPaymentItem.vue';
import CompletedPaymentItem from '../components/CompletedPaymentItem.vue';

const summary = ref<any>({
    diario: { title: 'Diario', paid: 0, total: 0, pending: 0 },
    semanal: { title: 'Semanal', paid: 0, total: 0, pending: 0 },
    quincenal: { title: 'Quincenal', paid: 0, total: 0, pending: 0 },
    mensual: { title: 'Mensual', paid: 0, total: 0, pending: 0 },
    total: { title: 'Total General', paid: 0, total: 0, pending: 0 }
});

const pendingList = ref<any[]>([]);
const completedList = ref<any[]>([]);
const allList = ref<any[]>([]);
const loading = ref(true);
const activeFilter = ref<string | null>(null);
const currentTab = ref('pending');
const router = useRouter();

const fetchSummary = async () => {
    try {
        const response = await CobrosService.getSummary();
        summary.value = response.data;
    } catch (error) {
        console.error("Error fetching summary:", error);
    }
};

const fetchPending = async () => {
    try {
        const response = await CobrosService.getPending();
        pendingList.value = response.data;
    } catch (error) {
        console.error("Error fetching pending:", error);
    }
};

const fetchCompleted = async () => {
    try {
        const response = await CobrosService.getCompleted();
        completedList.value = response.data;
    } catch (error) {
        console.error("Error fetching completed:", error);
    }
};

onMounted(async () => {
    loading.value = true;
    await Promise.all([fetchSummary(), fetchPending(), fetchCompleted()]);
    loading.value = false;
});

const filteredPendingList = computed(() => {
    if (!activeFilter.value || activeFilter.value === 'Total General') return pendingList.value;
    return pendingList.value.filter(item => item.plan?.toLowerCase() === activeFilter.value?.toLowerCase());
});

const filteredCompletedList = computed(() => {
    if (!activeFilter.value || activeFilter.value === 'Total General') return completedList.value;
    return completedList.value.filter(item => item.plan?.toLowerCase() === activeFilter.value?.toLowerCase());
});

const toggleFilter = (plan: string) => {
    if (activeFilter.value === plan) {
        activeFilter.value = null;
    } else {
        activeFilter.value = plan;
    }
};

const toggleMoto = async (item: any) => {
    const isTurningOff = item.moto_status;
    const actionText = isTurningOff ? 'APAGAR' : 'ENCENDER';

    const result = await Swal.fire({
        title: '¿Estás seguro?',
        text: `Se enviará comando para ${actionText} el motor de ${item.client_name}`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: isTurningOff ? '#d33' : '#22c55e',
        cancelButtonColor: '#3085d6',
        confirmButtonText: `Sí, ${actionText}`,
        cancelButtonText: 'Cancelar'
    });

    if (result.isConfirmed) {
        try {
            await CobrosService.toggleMoto(item.id, !isTurningOff);
            item.moto_status = !isTurningOff;

            // Refresh data to get the new turned_off_at timestamp
            fetchPending();
            fetchCompleted();

            toast.success(`${actionText} exitoso`);
        } catch (error) {
            toast.error("Error al enviar comando");
        }
    }
};

const notifyOff = async (item: any) => {
    try {
        await CobrosService.notifyWhatsApp(item.id, 'warning');
        toast.success("Notificación de apagado enviada");
    } catch (error: any) {
        toast.error(error.response?.data?.message || "Error al enviar notificación");
    }
};

const notifySuccess = async (item: any) => {
    try {
        await CobrosService.notifyWhatsApp(item.id, 'success');
        toast.success("Notificación de felicitación enviada");
    } catch (error: any) {
        toast.error(error.response?.data?.message || "Error al enviar notificación");
    }
};

const handleCreateMora = async (item: any) => {
    const result = await Swal.fire({
        title: '¿Generar Mora?',
        text: `Se creará un registro de mora para ${item.client_name}. ¿Deseas continuar?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#FF7539',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, generar',
        cancelButtonText: 'Cancelar'
    });

    if (result.isConfirmed) {
        try {
            const response = await CobrosService.createMora(item.id);
            toast.success(response.data.message || "Mora generada con éxito");

            // Refresh lists
            fetchPending();
            fetchCompleted();
        } catch (error: any) {
            toast.error(error.response?.data?.message || "Error al generar la mora");
        }
    }
};
</script>

<template>
    <div class="px-6">

        <TablesHeader title="Gestión de Cobros" icon="fa-solid fa-money-bill-trend-up" :btnCreate="false"
            :searchActive="false" />

        <div v-if="loading" class="h-[60vh] flex items-center justify-center">
            <Loader />
        </div>

        <template v-else>
            <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 px-6 gap-6 my-6 pt-0">
                <SummaryCard v-for="(card, key) in summary" :key="key" :card="card"
                    :active="activeFilter === card.title" @click="toggleFilter(card.title)" />
            </section>

            <section class="mt-8 pb-20">
                <div class="flex gap-5 justify-between my-10 w-full md:w-[80%] mx-auto">
                    <button @click="currentTab = 'pending'" :class="[
                        'block w-full px-5 py-2 font-bold rounded-full shadow-md transition-all duration-300',
                        currentTab === 'pending'
                            ? 'bg-[#FF7539] text-white hover:text-[#FF7539] hover:bg-white'
                            : 'text-[#FF7539] bg-white hover:bg-[#FF7539] hover:text-white'
                    ]">
                        Pendientes
                    </button>

                    <button @click="currentTab = 'completed'" :class="[
                        'block w-full px-5 py-2 font-bold rounded-full shadow-md transition-all duration-300',
                        currentTab === 'completed'
                            ? 'bg-[#FF7539] text-white hover:text-[#FF7539] hover:bg-white'
                            : 'text-[#FF7539] bg-white hover:bg-[#FF7539] hover:text-white'
                    ]">
                        Completados
                    </button>
                </div>

                <!-- Pending Tab Content -->
                <div v-if="currentTab === 'pending'">
                    <div v-if="filteredPendingList.length === 0"
                        class="flex flex-col items-center justify-center p-20 bg-white rounded-3xl border border-dashed border-gray-200">
                        <font-awesome-icon icon="fa-solid fa-face-smile-wink" class="text-5xl text-orange-200 mb-4" />
                        <h3 class="text-lg font-bold text-gray-400">¡Excelente! No hay cobros pendientes</h3>
                    </div>

                    <div v-else class="relative md:px-4">
                        <div class="fakeTable mx-auto h-full">
                            <article class="fakeTable-head grid-cols-10 ">
                                <p>Código</p>
                                <p>Cliente</p>
                                <p>Plan</p>
                                <p>Cuota</p>
                                <p>Vencimiento</p>
                                <p>Apagado el</p>
                                <p>Mora Pagada</p>
                                <p>Fecha Pago Mora</p>
                                <p>Días Deudores</p>
                                <p class="text-center">Acciones</p>
                            </article>

                            <section class="space-y-2">
                                <PendingPaymentItem v-for="pending in filteredPendingList" :key="pending.id"
                                    :pending="pending" @toggleMoto="toggleMoto" @notifyOff="notifyOff"
                                    @createMora="handleCreateMora" />
                            </section>
                        </div>
                    </div>
                </div>

                <!-- Completed Tab Content -->
                <div v-else>
                    <div v-if="filteredCompletedList.length === 0"
                        class="flex flex-col items-center justify-center p-20 bg-white rounded-3xl border border-dashed border-gray-200">
                        <font-awesome-icon icon="fa-solid fa-clock-rotate-left" class="text-5xl text-blue-200 mb-4" />
                        <h3 class="text-lg font-bold text-gray-400">Aún no hay pagos completados en este periodo</h3>
                    </div>

                    <div v-else class="relative md:px-4">
                        <div class="fakeTable mx-auto h-full">
                            <article class="fakeTable-head grid-cols-6 ">
                                <p>Código</p>
                                <p>Cliente</p>
                                <p>Plan</p>
                                <p>Cuota</p>
                                <p>Vencimiento</p>
                                <p class="text-center">Acciones</p>
                            </article>

                            <section class="space-y-2">
                                <CompletedPaymentItem v-for="item in filteredCompletedList" :key="item.id" :item="item"
                                    @notifySuccess="notifySuccess" @toggleMoto="toggleMoto"
                                    @createMora="handleCreateMora" />
                            </section>
                        </div>
                    </div>
                </div>
            </section>
        </template>
    </div>
</template>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.5s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
