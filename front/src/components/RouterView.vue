<script lang="ts" setup>

const props = defineProps<{
    conditionForView?: boolean
}>()


</script>

<template>
    <router-view v-slot="{ Component }">
        <Transition name="expandModal">
            <div v-if="conditionForView" class="overlay bg-[#05050563] md:grid md:place-items-center">
                
                <div class="w-full relative mx-auto bg-white md:rounded-3xl flex flex-col overflow-hidden"
                    :class="[
                        $route.name == 'loteCreate' ? 'md:w-[40%] md:h-[60vh]' : 'md:w-[80%] md:h-[90vh]',
                        'h-screen' 
                    ]">
                    
                    <button @click="$router.back()" class="absolute left-8 top-10 text-xl text-[#FF7539] font-bold z-10">
                        <font-awesome-icon icon="fa-solid fa-angle-left" />
                        volver
                    </button>

                    <div class="flex-1 w-full overflow-y-auto min-h-0 py-24 px-16 pt-28">
                        <component :is="Component" :key="$route.path" />
                    </div>

                </div>
            </div>
        </Transition>
    </router-view>
</template>