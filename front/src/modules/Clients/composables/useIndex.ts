import { reactive, onMounted, ref } from "vue"
import { onBeforeRouteUpdate } from "vue-router"
import useTableGrid from "@/composables/useTableGrid"
import useHttp from "@/composables/useHttp"
import clientservice from "../services"
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


  const getclientsScroll = () => clientservice.getclients(`offset=${data.offset}&${new URLSearchParams(route.query as Params).toString()}`)

  const {
    route,
    router,
    setSearch,
    setSort,
    loadScroll,
  } = useTableGrid(data, getclientsScroll)

  const getclients = async (routeQuery: string) => {
    loaded.value = true
    const response = await clientservice.getclients(`offset=0&${routeQuery}`)
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

    const confirm = await questionSweet('Info', `¿Estás seguro que desea eliminar a <strong>${persona.nombre}</strong>?`, 'question')

    if(!confirm) return

    try {
      await clientservice.deleteClient(rowId)
      alertWithToast('Eliminado Correctamente', 'success')
      getclients(new URLSearchParams(route.query as Params).toString())
    } catch (error) {
      let message = error.response ? error.response.data.message : 'Ha ocurrido un error inesperado'
      message = message.split('. (')[0]
      alertWithToast(message, 'error')
    }
  }

  onBeforeRouteUpdate(async (to, from) => {
    if (to.query !== from.query ) {
      await getclients(
        new URLSearchParams(to.query as Params).toString()
      )
    }
  })

  onMounted(() => {
    getclients(
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

