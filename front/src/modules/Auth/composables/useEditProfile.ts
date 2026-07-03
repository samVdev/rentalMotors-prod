import { ref } from "vue";
import { useAuthStore } from "../stores"
import { useRouter } from "vue-router";
import { showUser, updatePassword, updateProfile } from "../services";
import { alertWithToast } from "@/utils/toast";
import { questionSweet } from "@/utils/question";

export default () => {

    const store = useAuthStore()
    const router = useRouter()

    const userInfo = ref({
        id: '',
        name: '',
        email: '',
        tel: '',
    })
    
    const passwords = ref({
        current_password: '',
        password: '',
        password_confirmation: '',
    })

    const getUser = async () => {
        try {
            const response = await showUser()
            userInfo.value = response.data
        } catch (error) {
            await questionSweet('Info', 'Ocurrio un error al obtener la información', 'info')
            router.back()
        }
    }

    const submitInfoUser = async () => {
        const result = await questionSweet('Info', '¿Estás seguro del cambio?', 'info')
        if(!result) return
        try {
            const response = await updateProfile(userInfo.value, userInfo.value.id)
            await store.getAuthUser()
            router.back()
            setTimeout(() => {
                alertWithToast(response.data.message, 'success');
            }, 100); 
        } catch (error) {
            let message = error.response ? error.response.data.message : 'Ha ocurrido un error inesperado'
            message = message.split('. (')[0]
            alertWithToast(message, 'error')
        }
    }

    const submitPasswordUser = async () => {
        const result = await questionSweet('Info', '¿Estás seguro del cambio?', 'info')
        if(!result) return
        try {
            const response = await updatePassword(passwords.value)
            router.back()
            setTimeout(() => {
                alertWithToast('Se ha actualizado correctamente', 'success');
            }, 100); 
        } catch (error) {
            console.log(error)
            let message = error.response ? error.response.data.message : 'Ha ocurrido un error inesperado'
            message = message.split('. (')[0]
            alertWithToast(message, 'error')
        }
    }

    return {
        submitInfoUser,
        submitPasswordUser,
        getUser,
        userInfo,
        passwords
    }
}