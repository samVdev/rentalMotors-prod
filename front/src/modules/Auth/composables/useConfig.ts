import { alertWithToast } from "@/utils/toast";
import {  editNewAccountService, getInfoService, getNewDolarService } from "../services/configService";
import { ref } from "vue";
import { questionSweet } from "@/utils/question";

export default () => {
    const dolar = ref(0)
    const account = ref({
        cuenta: '',
        cedu: '',
        titu: '',
        banco: '',
        cel: ''
    })

    const actualizando = ref(false)

    const getInfo = async () => {
        try {
            const response = await getInfoService()
            const {dolarBcv, ...accountData} = response.data
            account.value = accountData
            dolar.value = dolarBcv
        } catch (error) {
            let message = 'Error inesperado';
            if (error.response) {
                message = error.response.data.errors.msg;
            }
            alertWithToast(message, 'error')
        }
    }

    const getDolar = async () => {
        if(actualizando.value) return
        try {
            actualizando.value = true
            const response = await getNewDolarService()
            dolar.value = response.data.newMount
            alertWithToast('Actualizado', 'success')
        } catch (error) {
            let message = 'Error inesperado';
            if (error.response) {
                message = error.response.data.errors.msg;
            }
            alertWithToast(message, 'error')
        } finally{
            actualizando.value = false
        }
    }

    const submit = async () => {
        const result = await questionSweet('Info', '¿Estás seguro del cambio?', 'info')
        if(!result) return
        try {
            const response = await editNewAccountService(account.value)
            let message = response.data.message;
            alertWithToast(message, 'success')
            getInfo()
        } catch (error) {
            let message = error.response ? error.response.data.message : 'Ha ocurrido un error inesperado'
            message = message.split('. (')[0]
            alertWithToast(message, 'error')
        }
    }


    return {
        account,
        dolar,
        actualizando,
        getDolar,
        getInfo,
        submit
    }
}