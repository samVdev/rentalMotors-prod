<script lang="ts" setup>
import CardDash from '@/modules/Auth/components/CardDash.vue';
import { onMounted, ref } from 'vue';
import MiniTitle from '@/components/miniTitle.vue';
import PaymentModal from '../components/PaymentModal.vue';
import useIndex from '../composable/useIndex';
import NotElements from '@/components/NotElements.vue';
import MaintenanceCard from '../components/MaintenanceCard.vue';
import GuestFinancingCard from '../components/GuestFinancingCard.vue';
import MoraCard from '../components/MoraCard.vue';


const {
    data,
    pay_id,
    pay_total,
    pay_mora_id,
    getData,
    close,
    mantenimientSend
} = useIndex()

onMounted(() => {
    getData()
})

</script>


<template>
    <main class="mx-auto md:w-[90%] 2xl:w-[80%]">

        <PaymentModal v-if="pay_id" :id="pay_id" :total="pay_total" :mora-id="pay_mora_id" @close="close" />

        <div class="grid grid-cols-1 gap-5 mt-10 md:grid-cols-2">
            <section>
                <div v-if="data.moras.length > 0">
                    <MiniTitle text="Moras Pendientes:" class="!text-2xl text-red-600" />
                    <MoraCard v-for="mora in data.moras" :key="mora.id" :mora="mora"
                        @payMora="({ id, financing_id, total }) => { pay_id = String(financing_id); pay_total = total; pay_mora_id = id }" />
                </div>

                <MiniTitle text="Pagos:" class="!text-2xl" />
                <div v-if="data.financings.length > 0">
                    <GuestFinancingCard v-for="pay in data.financings" :key="pay.id" :pay="pay"
                        @pay="({ id, total }) => { pay_id = String(id); pay_total = total; pay_mora_id = null }" />
                </div>
                <NotElements v-else title="Sin financiaciones activas" class="mb-5" />



            </section>

            <section>
                <MiniTitle text="Mantenimientos:" class="!text-2xl mb-6" />

                <div v-if="data.mantenimientos.length > 0">
                    <MaintenanceCard v-for="mante in data.mantenimientos" :key="mante.id" :mante="mante"
                        @mantenimientSend="mantenimientSend" />
                </div>
                <NotElements v-else title="Sin Mantenimientos pendientes" class="mb-5" />
            </section>
        </div>


        <MiniTitle text="Accesos rápidos" class="mb-10" />

        <div class="grid grid-cols-1 px-2 gap-5 md:px-10 md:grid-cols-3">
            <CardDash icon="fa-solid fa-dollar-sign" color="bg-orange-100 text-orange-600"
                :value="data.countedItems.payments" label="Pagos realizados de las financiaciones activas"
                class="hover:-translate-y-0 cursor-default" />
            <CardDash icon="fa-regular fa-map" color="bg-blue-100 text-blue-600" :value="data.countedItems.cuotas"
                label="Cuotas restantes de las financiaciones activas" class="hover:-translate-y-0 cursor-default" />
            <CardDash icon="fa-solid fa-triangle-exclamation" color="bg-red-100 text-red-600"
                :value="data.mantenimientos.length" label="Mantenimientos pendientes"
                class="hover:-translate-y-0 cursor-default" />
        </div>

    </main>
</template>

<style scoped></style>