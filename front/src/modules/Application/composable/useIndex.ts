import { reactive, onMounted, ref } from "vue"
import { onBeforeRouteUpdate } from "vue-router"
import useTableGrid from "@/composables/useTableGrid"
import useHttp from "@/composables/useHttp"
import ApplyService from '../services'
import { questionSweet } from "@/utils/question"
import { alertWithToast } from "@/utils/toast"
import type { applyInterface } from "../types/applyType"

type Params = string | string[][] | Record<string, string> | URLSearchParams | undefined

export default () => {

    const data = reactive({
        rows: <applyInterface[]>[],
        links: [],
        page: "1",
        search: "",
        sort: "",
        direction: "",
        offset: 0
    })

    const anex_id = ref(0)
    const requirements_id = ref(0)
    const docFile = ref('')
    const loaded = ref(true)
    const {
        errors,
    } = useHttp()

    const url = import.meta.env.VITE_APP_API_URL

    const getScroll = () => ApplyService.get(`offset=${data.offset}&${new URLSearchParams(route.query as Params).toString()}`)

    const {
        route,
        router,
        setSearch,
        setSort,
        loadScroll,
    } = useTableGrid(data, getScroll)

    const getData = async (routeQuery: string) => {
        loaded.value = true
        const response = await ApplyService.get(`offset=0&${routeQuery}`)
        errors.value = {}
        data.rows = response.data.rows.map(e => {
            return {
                ...e,
                //document: `${url}/public/${e.document}`,
                //pay_one: e.pay_one ? `${url}/public/${e.pay_one}` : '',
                vehicle_image: `${url}/${e.vehicle_image}`
            }
        })
        data.search = response.data.search
        data.sort = response.data.sort
        data.direction = response.data.direction
        data.offset = 10
        loaded.value = false
    }


    const changeStatus = async (id: string, status: boolean) => {
        try {
            const confirm = await questionSweet(
                '¿Estás seguro?',
                `Vas a marcar esta solicitud como "${status ? 'Aceptada' : 'Rechazada'}".`,
                'warning',
            )

            if (!confirm) return

            loaded.value = true

            const response = await ApplyService.put(id, status)

            if (response && response.status == 200) {
                router.push({ name: 'view-apply', query: { type: status ? 'accept' : 'reject' } }).then(() => {
                    alertWithToast(
                        `La solicitud fue ${status ? 'aceptada' : 'rechazada'} correctamente.`,
                        'success',
                    )
                })
            } else {
                alertWithToast(
                    response.data.message || 'No se pudo actualizar el estado. Intenta de nuevo.',
                    'error',
                )
            }
        } catch (error: any) {
            const status = error?.response?.status

            if (status === 409) {
                alertWithToast(
                    error.response.data.message,
                    'warning'
                )
            } else {
                alertWithToast(
                    error?.response?.data?.message || 'Ocurrió un error al cambiar el estado',
                    'error'
                )
            }
        } finally {
            loaded.value = false
        }
    }

    const submit = async (form: FormData) => {
        try {
            const response = await ApplyService.anexarDoc(anex_id.value.toString(), form);
            alertWithToast(response.data.message || "Correcto", "success");

            await getData(
                new URLSearchParams(route.query as Params).toString()
            )

            anex_id.value = 0
        } catch (error) {
            alertWithToast(error?.response?.data?.message || "Ocurrió un error al anexar el documento", "error");
        }
    }

    const submitRequirements = async (form: FormData) => {
        try {
            const response = await ApplyService.updateRequirements(requirements_id.value.toString(), form);
            alertWithToast(response.data.message || "Correcto", "success");

            await getData(
                new URLSearchParams(route.query as Params).toString()
            )

            requirements_id.value = 0
        } catch (error: any) {
            alertWithToast(error?.response?.data?.message || "Ocurrió un error al actualizar los recaudos", "error");
        }
    }

    onBeforeRouteUpdate(async (to, from) => {
        if (to.query !== from.query) {
            await getData(
                new URLSearchParams(to.query as Params).toString()
            )
        }
    })

    onMounted(() => {
        if (!route.query?.type) router.push({ name: 'view-apply', query: { type: 'pending' } })
        else {
            getData(
                new URLSearchParams(route.query as Params).toString(),
            )
        }
    })

    return {
        errors,
        data,
        anex_id,
        route,
        router,
        loaded,
        docFile,
        requirements_id,
        setSearch,
        setSort,
        loadScroll,
        changeStatus,
        submit,
        submitRequirements,
    }
}

