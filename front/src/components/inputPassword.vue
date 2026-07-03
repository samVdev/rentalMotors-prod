<script lang="ts" setup>
import { ref, watch } from 'vue';

const showPassword = ref(false)

const props = defineProps<{
    modelValue: string
    name: string,
    placeholder: string,
}>()

const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void;
}>();

watch(() => props.modelValue, (newValue) => {
    emit('update:modelValue', newValue);
});

</script>

<template>
    <div class="relative">
        <input :type="showPassword ? 'Text' : 'password'"
        :value="props.modelValue" @input="(event: any) => emit('update:modelValue', event.target.value)"
        :placeholder="placeholder" :name="name" :id="name" required>

        <button type="button" class="absolute right-[2%] top-[50%] translate-y-[-50%]" @click="showPassword = !showPassword">
          <font-awesome-icon icon="eye" />
        </button>
    </div>
</template>