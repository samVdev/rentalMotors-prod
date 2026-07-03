import { toast} from "vue3-toastify"

export declare type ToastType = 'info' | 'success' | 'error' | 'warning' | 'loading' | 'default';


export const alertWithToast = (message: string, type: ToastType) => {
    toast(message, {
        type,
    })
}