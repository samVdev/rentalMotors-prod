import { reactive, onMounted, ref } from "vue"
import { onBeforeRouteUpdate } from "vue-router"
import useTableGrid from "@/composables/useTableGrid"
import useHttp from "@/composables/useHttp"
import LotesService from "../services"
import { alertWithToast } from "@/utils/toast"
import { questionSweet } from "@/utils/question"

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

  const getLotesScroll = () => LotesService.get()

  const {
    router,
    setSearch,
    setSort,
    loadScroll,
  } = useTableGrid(data, getLotesScroll)

  const getLotes = async () => {
    loaded.value = true
    const response = await LotesService.get()
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
  
    const lote = data.rows.find(e => e.id == rowId)

    const confirm = await questionSweet('Info', `¿Estás seguro que desea eliminar a <strong>${lote.nombre}</strong>?`, 'question')

    if(!confirm) return

    try {
      await LotesService.delete(rowId)
      alertWithToast('Eliminado Correctamente', 'success')
      getLotes()
    } catch (error) {
      let message = error.response ? error.response.data.message : 'Ha ocurrido un error inesperado'
      message = message.split('. (')[0]
      alertWithToast(message, 'error')
    }
  }

  onBeforeRouteUpdate(async (to, from) => {
    if (to.query !== from.query ) {
      await getLotes()
    }
  })

  onMounted(() => {
    getLotes()
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

