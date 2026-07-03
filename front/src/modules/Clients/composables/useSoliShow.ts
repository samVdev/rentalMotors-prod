import { reactive, onMounted, ref } from "vue"
import useTableGrid from "@/composables/useTableGrid"
import useHttp from "@/composables/useHttp"
import ApplyService from '@/modules/Application/services'
import type{ applyInterface } from "@/modules/Application/types/applyType"

type Params = string | string[][] | Record<string, string> | URLSearchParams | undefined

export default () => {

    const data = reactive({
        rows: <applyInterface[]> [],
        links: [],
        page: "1",
        search: "",
        sort: "",
        direction: "",
        offset: 0
    })

    const loaded = ref(true)
    const {
        errors,
    } = useHttp()

    const url = import.meta.env.VITE_APP_API_URL

    const getScroll = () => ApplyService.get(`user=${route.params.id}&offset=${data.offset}&${new URLSearchParams(route.query as Params).toString()}`)

    const {
        route,
        router,
        setSearch,
        setSort,
        loadScroll,
    } = useTableGrid(data, getScroll)

    const getData = async (routeQuery: string) => {
        loaded.value = true
        const response = await ApplyService.get(`user=${route.params.id}&offset=0&${routeQuery}`)
        errors.value = {}
        data.rows = response.data.rows.map(e => {
            return {
                ...e,
                document: `${url}/public/${e.document}`,
                pay_one: e.pay_one ? `${url}/public/${e.pay_one}` : '',
                vehicle_image: `${url}/${e.vehicle_image}`
            }
        })
        data.search = response.data.search
        data.sort = response.data.sort
        data.direction = response.data.direction
        data.offset = 10
        loaded.value = false
    }

    onMounted(() => {
        getData(
            new URLSearchParams(route.query as Params).toString(),
        )
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
    }
}

