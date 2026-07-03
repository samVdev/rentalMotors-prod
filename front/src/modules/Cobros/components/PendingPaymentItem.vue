<script setup lang="ts">
import { parsePrices } from '@/utils/parsePrices';
import { ref } from 'vue';

defineProps<{
    pending: {
        id: number;
        code: string;
        client_name: string;
        user_id: number;
        plan: string;
        installments: number;
        plan_price: number;
        start_date: string;
        due_date?: string;
        client_phone?: string;
        moto_status: boolean;
        turned_off_at?: string | null;
        mora_paid?: boolean;
        mora_date?: string;
        days_late?: number;
    }
}>();

defineEmits(['toggleMoto', 'notifyOff', 'createMora']);

const showActions = ref(false);
const dropdownStyle = ref({ top: '0px', left: '0px' });
const menuBtn = ref<HTMLElement | null>(null);

const toggleActions = () => {
    if (menuBtn.value) {
        const rect = menuBtn.value.getBoundingClientRect();
        const vh = window.innerHeight;
        const menuHeight = 300;

        let top = rect.bottom - 5;
        if (rect.bottom + menuHeight > vh) {
            top = rect.top - 310;
        }

        dropdownStyle.value = {
            top: `${top}px`,
            left: `${rect.right - 224}px` // w-56(224px) align to right
        };
    }
    showActions.value = !showActions.value;
};

const closeActions = () => {
    setTimeout(() => {
        showActions.value = false;
    }, 200);
};

</script>

<template>
    <div class="fakeTable-body grid-cols-10 transition-all relative">
        <div class="flex flex-col">
            <p class="font-black text-gray-700">{{ pending.code }}</p>
        </div>

        <div class="text-center">
            <p class="font-bold text-gray-900 leading-tight uppercase">{{ pending.client_name }}</p>
            <p class="text-[10px] text-gray-400 font-medium">{{ pending.user_id }}</p>
        </div>

        <div>
            <span
                class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider shadow-sm bg-orange-100 text-[#FF7539]">
                {{ pending.plan }}
            </span>
        </div>

        <div class="flex flex-col">
            <span class="text-sm font-black text-gray-800">{{ parsePrices(pending.plan_price) }}</span>
            <small class="text-[9px] text-gray-400 font-bold tracking-tighter">{{ pending.installments }} rest.</small>
        </div>

        <div class="flex flex-col text-center">
            <span class="text-sm font-bold text-red-600">{{ pending.due_date || 'Hoy' }}</span>
        </div>

        <div class="text-center">
            <span v-if="!pending.moto_status" class="text-[10px] font-bold text-red-500 uppercase leading-tight">
                {{ pending.turned_off_at }}
            </span>
            <span v-else class="text-[10px] font-bold text-green-500 uppercase">No apagada</span>
        </div>

        <div class="text-center">
            <font-awesome-icon v-if="pending.mora_paid" icon="fa-solid fa-circle-check"
                class="text-green-500 text-lg" />
            <font-awesome-icon v-else icon="fa-solid fa-circle-xmark" class="text-red-500 text-lg" />
        </div>

        <div class="text-center">
            <span class="text-sm font-bold" :class="pending.mora_paid ? 'text-gray-700' : 'text-gray-400'">
                {{ pending.mora_date || '--' }}
            </span>
        </div>

        <div class="text-center">
            <span class="text-sm font-bold text-red-500">{{ pending.days_late ?? 0 }} días</span>
        </div>


        <div class="flex items-center justify-center relative">
            <button ref="menuBtn" @click="toggleActions" @blur="closeActions"
                class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 transition-all text-gray-400 hover:text-[#FF7539]">
                <font-awesome-icon icon="fa-solid fa-ellipsis-vertical" />
            </button>

            <!-- Dropdown Menu Teleported -->
            <Teleport to="body">
                <div v-if="showActions" :style="dropdownStyle"
                    class="fixed bg-white shadow-2xl rounded-2xl z-[9999] w-56 py-3 border border-gray-100 animate-fade-in overflow-hidden">
                    <div class="px-4 py-2 border-b border-gray-50 mb-1">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Acciones</p>
                    </div>

                    <button @mousedown="$emit('toggleMoto', pending)"
                        class="w-full flex items-center gap-3 px-4 py-3 text-left hover:bg-gray-50 transition-colors group">
                        <div :class="pending.moto_status ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600'"
                            class="w-8 h-8 rounded-lg flex items-center justify-center transition-transform group-hover:scale-110">
                            <font-awesome-icon icon="fa-solid fa-power-off" />
                        </div>
                        <span class="text-xs font-bold text-gray-700">{{ pending.moto_status ? 'Apagar Moto' :
                            'Encender Moto'
                            }}</span>
                    </button>

                    <button @mousedown="$emit('notifyOff', pending)"
                        class="w-full flex items-center gap-3 px-4 py-3 text-left hover:bg-gray-50 transition-colors group">
                        <div
                            class="w-8 h-8 bg-green-50 text-green-600 rounded-lg flex items-center justify-center transition-transform group-hover:scale-110">
                            <font-awesome-icon icon="fa-brands fa-whatsapp" />
                        </div>
                        <span class="text-xs font-bold text-gray-700">Notificar Apagado</span>
                    </button>

                    <a href="https://app.plaspy.com/" target="_blank"
                        class="w-full flex items-center gap-3 px-4 py-3 text-left hover:bg-gray-50 transition-colors group">
                        <div
                            class="w-8 h-8 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center transition-transform group-hover:scale-110">
                            <font-awesome-icon icon="fa-solid fa-location-crosshairs" />
                        </div>
                        <span class="text-xs font-bold text-gray-700">Rastreo Plaspy</span>
                    </a>
                    
                    <button @mousedown="$emit('createMora', pending)"
                        class="w-full flex items-center gap-3 px-4 py-3 text-left hover:bg-gray-50 transition-colors group">
                        <div
                            class="w-8 h-8 bg-orange-50 text-orange-600 rounded-lg flex items-center justify-center transition-transform group-hover:scale-110">
                            <font-awesome-icon icon="fa-solid fa-triangle-exclamation" />
                        </div>
                        <span class="text-xs font-bold text-gray-700">Generar Mora</span>
                    </button>

                    <router-link :to="`/clients/show/s/${pending.user_id}`"
                        class="w-full flex items-center gap-3 px-4 py-3 text-left hover:bg-gray-50 transition-colors group border-t border-gray-50 mt-1">
                        <div
                            class="w-8 h-8 bg-gray-100 text-gray-600 rounded-lg flex items-center justify-center transition-transform group-hover:scale-110">
                            <font-awesome-icon icon="fa-solid fa-eye" />
                        </div>
                        <span class="text-xs font-bold text-gray-700">Ver Expediente</span>
                    </router-link>
                </div>
            </Teleport>
        </div>
    </div>
</template>
