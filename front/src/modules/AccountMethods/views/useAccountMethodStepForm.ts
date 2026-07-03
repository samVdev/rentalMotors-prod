import { ref, computed } from "vue"
import type { AccountMethod } from "./useAccountMethod"

export default (form: AccountMethod) => {
    const currentStep = ref(1)
    const totalSteps = 3

    const isStep1Valid = computed(() => {
        return form.provider_name.trim() !== '' && form.identifier.trim() !== '' && (form.type as string) !== '';
    })

    const isStep2Valid = computed(() => {
        return form.holder_name.trim() !== '';
    })

    const nextStep = () => {
        if (currentStep.value === 1 && !isStep1Valid.value) return;
        if (currentStep.value === 2 && !isStep2Valid.value) return;

        if (currentStep.value < totalSteps) {
            currentStep.value++
        }
    }

    const prevStep = () => {
        if (currentStep.value > 1) {
            currentStep.value--
        }
    }

    return {
        currentStep,
        totalSteps,
        isStep1Valid,
        isStep2Valid,
        nextStep,
        prevStep
    }
}
