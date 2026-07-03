<script lang="ts" setup>
import { FormatCurrency } from "@/utils/formatCurrency";
const props = defineProps<{
    image: string,
    title: string,
    description: string,
    price?: string | number,
    bntAdd?: boolean
}>()

const emit = defineEmits<{
    (e: 'add'): void,
    (e: 'details'): void
}>()

</script>

<template>
    <div class="group relative bg-white rounded-2xl shadow-md hover:shadow-2xl transition transform hover:-translate-y-2 overflow-hidden"
        data-aos="fade-up">
        <div class="h-48 w-full overflow-hidden">
            <img :src="image" :alt="title"
                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
        </div>

        <div class="p-6 text-center">
            <h3 class="text-xl font-bold text-gray-800 mb-2 group-hover:text-orange-500 transition">
                {{ title }}
            </h3>
            <p class="text-gray-600 text-sm mb-2">
                {{ description }}
            </p>
            <p v-if="price" class="text-lg font-black text-orange-500 mb-4">
                ${{ FormatCurrency(price) }}
            </p>

            <div class="text-center ">
                <button @click="$emit('details')"
                    class="block w-full bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold py-2 px-6 rounded-full shadow-md transition mx-auto mb-2">
                    Ver más
                </button>

                <button v-if="bntAdd" @click="$emit('add')"
                    class="block w-full bg-white hover:bg-orange-600 hover:text-white text-orange-600 border-orange-400 border-[1px] text-sm font-semibold py-2 px-6 rounded-full shadow-md transition mx-auto">
                    Agregar
                </button>
            </div>
        </div>

        <div class="absolute inset-x-0 bottom-0 h-1 bg-gradient-to-r from-orange-500 to-red-500"></div>
    </div>
</template>