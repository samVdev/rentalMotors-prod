import { reactive, onMounted, ref } from "vue"
import { onBeforeRouteUpdate } from "vue-router"
import useTableGrid from "@/composables/useTableGrid"
import useHttp from "@/composables/useHttp"
import UserService from "../services"
import { alertWithToast } from "@/utils/toast"
import { questionSweet } from "@/utils/question"

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

  const loaded = ref(true)
  const {
    errors,
  } = useHttp()


  const getUSersScroll = () => UserService.getUsers(`offset=${data.offset}&${new URLSearchParams(route.query as Params).toString()}`)

  const {
    route,
    router,
    setSearch,
    setSort,
    loadScroll,
  } = useTableGrid(data, getUSersScroll)

  const getUsers = async (routeQuery: string) => {
    loaded.value = true
    const response = await UserService.getUsers(`offset=0&${routeQuery}`)
    errors.value = {}
    data.rows = response.data.rows
    data.search = response.data.search
    data.sort = response.data.sort
    data.direction = response.data.direction
    data.offset = 10
    loaded.value = false
    
  }

  const deleteRow = async (rowId?: string) => {
    if (rowId === undefined) return
  
    const persona = data.rows.find(e => e.uuid == rowId)

    const confirm = await questionSweet('Info', `¿Estás seguro que desea eliminar a <strong>${persona.nombre}</strong>? Es un <strong>${persona.rol}</strong>`, 'question')

    if(!confirm) return

    try {
      await UserService.deleteUser(rowId)
      alertWithToast('Eliminado Correctamente', 'success')
      getUsers(new URLSearchParams(route.query as Params).toString())
    } catch (error) {
      let message = error.response ? error.response.data.message : 'Ha ocurrido un error inesperado'
      message = message.split('. (')[0]
      alertWithToast(message, 'error')
    }
  }

  onBeforeRouteUpdate(async (to, from) => {
    if (to.query !== from.query ) {
      await getUsers(
        new URLSearchParams(to.query as Params).toString()
      )
    }
  })

  onMounted(() => {
    getUsers(
      new URLSearchParams(route.query as Params).toString(),
    )
  })

  return {
    errors,
    data,
    router,
    loaded,
    deleteRow,
    setSearch,
    setSort,
    loadScroll
  }
}

