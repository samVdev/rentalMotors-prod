import { reactive, onMounted, ref } from "vue"
import { onBeforeRouteUpdate } from "vue-router"
import useTableGrid from "@/composables/useTableGrid"
import useHttp from "@/composables/useHttp"
import PaymentsService from '../services'
import type { PagoInterface } from "../types/PagoType"
import { alertWithToast } from "@/utils/toast"

type Params = string | string[][] | Record<string, string> | URLSearchParams | undefined

export default () => {

    const data = reactive({
        rows: <PagoInterface[]> [],
        links: [],
        page: "1",
        search: "",
        sort: "",
        direction: "",
        offset: 0
    })

    const anex_id = ref(0)
    const loaded = ref(true)
    const {
        errors,
    } = useHttp()

    const getScroll = () => PaymentsService.get(`offset=${data.offset}&${new URLSearchParams(route.query as Params).toString()}`)

    const {
        route,
        router,
        setSearch,
        setSort,
        loadScroll,
    } = useTableGrid(data, getScroll)

    const getData = async (routeQuery: string) => {
        loaded.value = true
        const response = await PaymentsService.get(`offset=0&${routeQuery}`)
        errors.value = {}
        data.rows = response.data.rows
        data.search = response.data.search
        data.sort = response.data.sort
        data.direction = response.data.direction
        data.offset = 10
        loaded.value = false
    }

    const reload = async (status: boolean) => {
        router.push({ name: 'payments', query: { type: status ? 'approved' : 'rejected' } }).then(() => {
            alertWithToast(
              `El pago fue ${status ? 'aceptado' : 'rechazado'} correctamente.`,
              'success',
            )
          })
    }

    onBeforeRouteUpdate(async (to, from) => {
        if (to.query !== from.query) {
            await getData(
                new URLSearchParams(to.query as Params).toString()
            )
        }
    })

    onMounted(() => {
        if (!route.query?.type) router.push({ name: 'payments', query: { type: 'pending' } })
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
        setSearch,
        setSort,
        loadScroll,
        reload
    }
}

