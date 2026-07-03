import { onMounted, reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { getError } from "@/utils/helpers"
import * as AccountMethodService from "@/modules/AccountMethods/services/AccountMethodService"
import type { Error } from "@/types/Error"

export interface AccountMethod {
    id?: number | string;
    provider_name: string;
    identifier: string;
    type: 'bank' | 'wallet' | 'crypto' | 'other';
    currency: string;
    holder_name: string;
    holder_dni: string | null;
    network_or_type: string | null;
    is_active: boolean;
    notes: string | null;
    _method?: string;
}

export default (accountMethodId?: string | number) => {
    const router = useRouter();

    const sending = ref(false);
    const loading = ref(false);
    const errors = ref<Error | undefined>()

    const method = reactive<AccountMethod>({
        provider_name: '',
        identifier: '',
        type: 'bank',
        currency: 'COP',
        holder_name: '',
        holder_dni: '',
        network_or_type: '',
        is_active: true,
        notes: ''
    })

    onMounted(() => {
        if (accountMethodId) {
            AccountMethodService.getAccountMethod(accountMethodId)
                .then(response => {
                    const data = response.data.data;
                    method.provider_name = data.provider_name
                    method.identifier = data.identifier
                    method.type = data.type
                    method.currency = data.currency
                    method.holder_name = data.holder_name
                    method.holder_dni = data.holder_dni || ''
                    method.network_or_type = data.network_or_type || ''
                    method.is_active = data.is_active == 1 ? true : false
                    method.notes = data.notes || ''
                })
        }
    });

    const insertAccountMethod = async (methodData: AccountMethod) => {
        sending.value = true
        try {
            const response = await AccountMethodService.insertAccountMethod(methodData)
            router.push({ path: '/account-methods' });
        } catch (err: any) {
            errors.value = getError(err)
        } finally {
            sending.value = false
        }
    };

    const updateAccountMethod = async (methodData: AccountMethod, id: string | number) => {
        sending.value = true
        methodData._method = 'PUT';
        try {
            const response = await AccountMethodService.updateAccountMethod(id, methodData)
            router.push({ path: '/account-methods' });
        } catch (err: any) {
            errors.value = getError(err)
        } finally {
            sending.value = false
        }
    };

    const submit = (data: AccountMethod, id?: string | number) => {
        !id ? insertAccountMethod(data) : updateAccountMethod(data, id)
    }

    const deleteAccountMethod = async (id?: string | number) => {
        if (id === undefined) return;
        sending.value = true
        try {
            const response = await AccountMethodService.deleteAccountMethod(id)
            router.push({ path: '/account-methods' });
        } catch (err: any) {
            errors.value = getError(err)
        } finally {
            sending.value = false
        }
    }

    return {
        method,
        sending,
        loading,
        errors,
        submit,
        deleteAccountMethod
    }

};
