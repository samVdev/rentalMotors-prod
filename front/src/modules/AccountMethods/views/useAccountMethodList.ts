import { reactive, ref } from "vue";
import type { RouteLocationNormalizedLoaded, Router } from 'vue-router';
import * as AccountMethodService from "@/modules/AccountMethods/services/AccountMethodService";
import useTableGrid from "@/composables/useTableGrid";
import useAccountMethod from "./useAccountMethod";
import { questionSweet } from "@/utils/question";
import { alertWithToast } from "@/utils/toast";

export default () => {
    const { deleteAccountMethod } = useAccountMethod()

    const data = reactive({
        rows: [],
        links: [],
        search: "",
        sort: "",
        direction: "",
        offset: 0
    });

    const loaded = ref(true)

    const getScroll = () => AccountMethodService.getAccountMethods(`offset=${data.offset}&${new URLSearchParams(route.query as any).toString()}`)

    const {
        route,
        router,
        setSearch,
        setSort,
        loadScroll
    } = useTableGrid(data, getScroll as any)

    const getAccountMethods = async (routeQuery: string) => {
        loaded.value = true
        const response = await AccountMethodService.getAccountMethods(`offset=0&${routeQuery}`)
        data.rows = response.data.rows.data ? response.data.rows.data : response.data.rows;
        data.links = response.data.rows.links || [];
        data.search = response.data.search;
        data.sort = response.data.sort;
        data.direction = response.data.direction;
        data.offset = 10;
        loaded.value = false
    };

    const deleteRow = async (rowId?: string | number) => {
        if (rowId === undefined) return;

        const confirm = await questionSweet(
            '¿Estás seguro?',
            `Vas a eliminar este método de cuenta.`,
            'warning',
        )

        if (!confirm) return

        deleteAccountMethod(rowId).then(() => {
            getAccountMethods(new URLSearchParams(route.query as any).toString());
        })
    };

    const toggleRow = async (row: any) => {
        const confirm = await questionSweet(
            '¿Estás seguro?',
            `Vas a cambiar el estado de la cuenta ${row.provider_name}.`,
            'warning',
        )

        if (!confirm) return

        try {
            await AccountMethodService.toggleAccountMethodStatus(row.id);
            row.is_active = !row.is_active;
            alertWithToast('Estado de la cuenta actualizado', 'success');
        } catch(e) {
            console.error(e)
            alertWithToast('Error al actualizar el estado', 'error');
        }
    }

    return {
        data,
        loaded,
        route,
        router,
        setSearch,
        setSort,
        loadScroll,
        getAccountMethods,
        deleteRow,
        toggleRow
    }
}
