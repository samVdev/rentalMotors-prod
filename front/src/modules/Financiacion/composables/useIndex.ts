import { reactive, onMounted, ref } from "vue"
import { onBeforeRouteUpdate, useRoute } from "vue-router"
import useTableGrid from "@/composables/useTableGrid"
import useHttp from "@/composables/useHttp"
import FinancingServices from '../services'
import { questionSweet } from "@/utils/question"
import { alertWithToast } from "@/utils/toast"


type Params = string | string[][] | Record<string, string> | URLSearchParams | undefined

export default () => {

    const data = reactive({
        rows: [],
        links: [],
        page: "1",
        search: "",
        sort: "",
        direction: "",
        offset: 0
    })

    const route = useRoute()
    const loaded = ref(true)
    const {
        errors,
    } = useHttp()

    const getScroll = () => FinancingServices.get(`&offset=${data.offset}&${new URLSearchParams(route.query as Params).toString()}`)

    const {
        router,
        setSearch,
        setSort,
        loadScroll,
    } = useTableGrid(data, getScroll)

    const getData = async (routeQuery: string) => {
        loaded.value = true
        const response = await FinancingServices.get(`&offset=0&${routeQuery}`)
        errors.value = {}
        data.rows = response.data.rows
        data.search = response.data.search
        data.sort = response.data.sort
        data.direction = response.data.direction
        data.offset = 10
        loaded.value = false
    }


    const deleteItem = async (id: number) => {
        try {
            const confirm = await questionSweet(
                '¿Estás seguro?',
                `Vas a eliminar esta financiación.`,
                'warning',
            )

            if (!confirm) return

            loaded.value = true

            const response = await FinancingServices.delete(id)

            if (response && response.status == 200) {
                alertWithToast(
                    `El financiación se ha eliminado correctamente.`,
                    'success',
                )
            } else {
                alertWithToast(
                    response.data.message || 'No se pudo actualizar el estado. Intenta de nuevo.',
                    'error',
                )
            }

            await getData(new URLSearchParams(route.query as Params).toString())

        } catch (error: any) {
            alertWithToast(
                error?.message || 'Ocurrió un error al cambiar el estado',
                'error',
            )
        } finally {
            loaded.value = false
        }
    }


    onBeforeRouteUpdate(async (to, from) => {
        if (to.query !== from.query) {
            await getData(new URLSearchParams(to.query as Params).toString())
        }
    })

    onMounted(() => {
        getData(new URLSearchParams(route.query as Params).toString())
    })

    return {
        errors,
        data,
        route,
        router,
        loaded,
        setSearch,
        setSort,
        loadScroll,
        deleteItem,
    }
}

