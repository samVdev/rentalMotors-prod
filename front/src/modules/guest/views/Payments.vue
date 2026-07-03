<script lang="ts" setup>
import { ref } from 'vue';
import MiniTitle from '@/components/miniTitle.vue';
import NotElements from '@/components/NotElements.vue';
import { parsePrices } from '@/utils/parsePrices';

// Mocked Data
const tabs = ['Vehículos', 'Impuestos', 'Mantenimientos'];
const activeTab = ref('Vehículos');

interface PaymentMock {
    id: number;
    codigo: string;
    date: string;
    amount: number;
    base_amount?: number;
    mora?: number;
    concept: string;
    cuota?: string;
    plan?: string;
    period?: string;
    details?: string;
    status: string;
    method: string;
}

const mockPayments: Record<string, PaymentMock[]> = {
    'Vehículos': [
        {
            id: 1,
            codigo: 'FNC-10293',
            date: '2026-03-01',
            amount: 500,
            base_amount: 500,
            mora: 0,
            concept: 'Financiación - Toyota Corolla',
            cuota: '5/48',
            plan: 'Premium Mensual',
            status: 'Pagado',
            method: 'Tarjeta de Crédito'
        },
        {
            id: 2,
            codigo: 'FNC-10293',
            date: '2026-02-01',
            amount: 500,
            base_amount: 500,
            mora: 0,
            concept: 'Financiación - Toyota Corolla',
            cuota: '4/48',
            plan: 'Premium Mensual',
            status: 'Pagado',
            method: 'Transferencia'
        },
    ],
    'Impuestos': [
        {
            id: 3,
            codigo: 'IMP-2026',
            date: '2026-03-15',
            amount: 150,
            base_amount: 150,
            mora: 0,
            concept: 'Impuesto de Circulación 2026',
            period: 'Anual 2026',
            status: 'Pagado',
            method: 'Efectivo'
        },
        {
            id: 4,
            codigo: 'IMP-2025',
            date: '2025-03-10',
            amount: 155,
            base_amount: 145,
            mora: 10,
            concept: 'Impuesto de Circulación 2025',
            period: 'Anual 2025',
            status: 'Pagado',
            method: 'Tarjeta de Débito'
        },
    ],
    'Mantenimientos': [
        {
            id: 5,
            codigo: 'MNT-8821',
            date: '2026-01-20',
            amount: 80,
            concept: 'Cambio de Aceite y Filtros',
            details: 'Revisión de 10,000 km, cambio de fluidos',
            status: 'Pagado',
            method: 'Tarjeta de Crédito'
        },
        {
            id: 6,
            codigo: 'MNT-4311',
            date: '2025-08-15',
            amount: 200,
            concept: 'Revisión General 10,000 km',
            details: 'Alineación, balanceo, cambio de bujías',
            status: 'Pagado',
            method: 'Transferencia'
        },
    ]
};

const getStatusColor = (status: string) => {
    switch (status) {
        case 'Pagado': return 'bg-green-100 text-green-700';
        case 'Pendiente': return 'bg-orange-100 text-orange-700';
        case 'Atrasado': return 'bg-red-100 text-red-700';
        default: return 'bg-gray-100 text-gray-700';
    }
};
</script>

<template>
    <main class="mx-auto md:w-[90%] pb-10">
        <MiniTitle text="Mis Pagos" class="!text-3xl mb-8 mt-10" />

        <!-- Tabs -->
        <div class="flex space-x-4 border-b border-gray-200 mb-6 overflow-x-auto">
            <button v-for="tab in tabs" :key="tab" @click="activeTab = tab" :class="[
                'py-3 px-4 text-sm font-medium transition-colors duration-200 whitespace-nowrap',
                activeTab === tab
                    ? 'border-b-2 border-orange-500 text-orange-600'
                    : 'text-gray-500 hover:text-gray-700 hover:border-gray-300'
            ]">
                {{ tab }}
            </button>
        </div>

        <!-- Content -->
        <div>
            <div v-if="mockPayments[activeTab].length > 0" class="space-y-4">
                <div v-for="payment in mockPayments[activeTab]" :key="payment.id"
                    class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col hover:shadow-md transition-shadow">
                    <!-- Main Row -->
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-800">{{ payment.concept }}</h3>
                            <div class="text-sm text-gray-500 mt-1 flex flex-wrap items-center gap-x-4 gap-y-2">
                                <span class="font-medium text-gray-700"><i class="fa-solid fa-hashtag mr-1"></i> {{
                                    payment.codigo }}</span>
                                <span><i class="fa-regular fa-calendar mr-1"></i> {{ payment.date }}</span>
                                <span><i class="fa-solid fa-money-check-dollar mr-1"></i> {{ payment.method }}</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between md:flex-col md:items-end gap-2">
                            <span class="text-xl font-bold text-gray-900">{{ parsePrices(payment.amount) }}</span>
                            <div class="flex items-center gap-3">
                                <span
                                    :class="['px-3 py-1 text-xs font-semibold rounded-full', getStatusColor(payment.status)]">
                                    {{ payment.status }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Details Accordion -->
                    <div
                        class="mt-4 pt-4 border-t border-gray-100 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-sm bg-gray-50 p-4 rounded-xl">

                        <div v-if="activeTab === 'Vehículos'" class="flex flex-col">
                            <span class="text-gray-500 font-medium">Plan</span>
                            <span class="text-gray-900 font-semibold">{{ payment.plan }}</span>
                        </div>

                        <div v-if="activeTab === 'Vehículos'" class="flex flex-col">
                            <span class="text-gray-500 font-medium">Cuota</span>
                            <span class="text-gray-900 font-semibold">{{ payment.cuota }}</span>
                        </div>

                        <div v-if="activeTab === 'Impuestos'" class="flex flex-col">
                            <span class="text-gray-500 font-medium">Periodo</span>
                            <span class="text-gray-900 font-semibold">{{ payment.period }}</span>
                        </div>

                        <div v-if="activeTab === 'Vehículos' || activeTab === 'Impuestos'" class="flex flex-col">
                            <span class="text-gray-500 font-medium">Monto base</span>
                            <span class="text-gray-900 font-semibold">{{ parsePrices(payment.base_amount) }}</span>
                        </div>

                        <div v-if="activeTab === 'Vehículos' || activeTab === 'Impuestos'" class="flex flex-col">
                            <span class="text-gray-500 font-medium">Mora</span>
                            <span class="text-orange-600 font-semibold">{{ parsePrices(payment.mora) }}</span>
                        </div>

                        <div v-if="activeTab === 'Mantenimientos'" class="flex flex-col sm:col-span-2">
                            <span class="text-gray-500 font-medium">Detalles de mantenimiento</span>
                            <span class="text-gray-900 font-semibold">{{ payment.details }}</span>
                        </div>
                    </div>

                </div>
            </div>

            <NotElements v-else :title="`No hay pagos de ${activeTab.toLowerCase()} registrados`" class="mt-10" />
        </div>
    </main>
</template>

<style scoped>
/* Scrollbar hide for webkit */
.overflow-x-auto::-webkit-scrollbar {
    display: none;
}

.overflow-x-auto {
    -ms-overflow-style: none;
    /* IE and Edge */
    scrollbar-width: none;
    /* Firefox */
}
</style>
