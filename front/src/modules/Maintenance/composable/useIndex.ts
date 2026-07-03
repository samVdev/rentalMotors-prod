import { reactive, onMounted, ref } from "vue"
import { onBeforeRouteUpdate } from "vue-router"
import useTableGrid from "@/composables/useTableGrid"
import useHttp from "@/composables/useHttp"
import MaintenanceService from '../services'
import { questionSweet } from "@/utils/question"
import { alertWithToast } from "@/utils/toast"
import type MaintenanceInterface from "../types/MaintenanceInterface"

type Params = string | string[][] | Record<string, string> | URLSearchParams | undefined

export default () => {

    const data = reactive({
        rows: <MaintenanceInterface[]>[],
        links: [],
        page: "1",
        search: "",
        sort: "",
        direction: "",
        offset: 0
    })

    const loaded = ref(true)
    const mantenenceToAccept = ref('0')

    const {
        errors,
    } = useHttp()

    const getScroll = () => MaintenanceService.get(`offset=${data.offset}&${new URLSearchParams(route.query as Params).toString()}`)

    const {
        route,
        router,
        setSearch,
        setSort,
        loadScroll,
    } = useTableGrid(data, getScroll)   

    const getData = async (routeQuery: string) => {
        loaded.value = true
        const response = await MaintenanceService.get(`offset=0&${routeQuery}`)
        errors.value = {}
        data.rows = response.data.rows
        data.search = response.data.search
        data.sort = response.data.sort
        data.direction = response.data.direction
        data.offset = 10
        loaded.value = false
    }

    const deleteMantenience = async (id: string) => {

        const confirm = await questionSweet(
            '¿Estás seguro?',
            `Vas a eliminar este mantenimiento".`,
            'warning',
        )

        if (!confirm) return

        try {
            const response = await MaintenanceService.destroy(id);
            alertWithToast(response.data.message || "Correcto", "success");
            data.rows = data.rows.filter((e: MaintenanceInterface) => e.id != id)

        } catch (error) {
            alertWithToast(error?.message || "Ocurrió un error al anexar el documento", "error");
        }
    }

    const changeStatus = async (id: string, status: string) => {

        if (status !== 'completed') return mantenenceToAccept.value = id;

        await manteneceChnageStatus(id);
    }

    const acceptMantenence = async (data: any) => {
        await manteneceChnageStatus(mantenenceToAccept.value, data);
        mantenenceToAccept.value = '0';
    }


    const manteneceChnageStatus = async (id: string, payload?: any) => {

        const confirm = await questionSweet(
            '',
            `¿Seguro que quieres cambiar el estado de este mantenimiento?`,
            'warning',
        )

        if (!confirm) return

        try {
            const response = await MaintenanceService.toggleStatus(id, payload);
            alertWithToast(response.data.message || "Correcto", "success");

            getData(
                new URLSearchParams(route.query as Params).toString(),
            )
        } catch (error) {
            alertWithToast(error?.message || "Ocurrió un error al cambiar el estado", "error");
        }
    }



    onBeforeRouteUpdate(async (to, from) => {
        if (to.query !== from.query && to.name === 'maintenance-index') {
            await getData(
                new URLSearchParams(to.query as Params).toString()
            )
        }
    })

    onMounted(() => {
        if (!route.query?.status) router.push({ name: 'maintenance-index', query: { status: 'pending' } })
        else {
            getData(
                new URLSearchParams(route.query as Params).toString(),
            )
        }
    })

    return {
        errors,
        data,
        route,
        router,
        loaded,
        mantenenceToAccept,
        acceptMantenence,
        setSearch,
        setSort,
        loadScroll,
        deleteMantenience,
        changeStatus
    }
}

