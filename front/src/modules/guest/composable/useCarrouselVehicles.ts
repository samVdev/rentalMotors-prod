import type { vehicleMinType } from "../types/vehicleMinType"
import Http from "@/utils/Http"
import { ref, computed } from "vue";
import type { Carousel } from "vue3-carousel";

export default () => {
    const vehicles = ref<vehicleMinType[]>([])
    const search = ref("")
    const type = ref<'bikes' | 'cars'>("bikes")
    const carousel = ref<InstanceType<typeof Carousel> | null>(null);

    const goNext = () => {
        //@ts-ignore
        carousel.value?.next();
    };

    const goPrev = () => {
        //@ts-ignore
        carousel.value?.prev();
    };

    const config = {
        itemsToShow: 1,
        itemsToScroll: 3,
        autoplay: 0,
        wrapAround: false,
        transition: 500,
        breakpoints: {
            768: { itemsToShow: 2 },
            1024: { itemsToShow: 3 },
        },
    };

    const showArrows = computed(() => {
        const maxVisible = 3;
        return vehicles.value.length > maxVisible;
    });

    const getVehicles = async (_type: 'bikes' | 'cars', _search?: string) => {
        const response = await Http.get(`api/guest/vehicles/${_type}/?search=${_search || ''}`)
        vehicles.value = response.data
        type.value = _type
    }

    const searchVehicles = async () => {
        try {
          const term = search.value.trim()
          if (!term) {
            getVehicles(type.value)
            return
          }
          getVehicles(type.value, term)
        } catch (error) {
          console.error("Error al buscar vehículos:", error)
        }
      }

    return {
        config,
        carousel,
        vehicles,
        showArrows,
        search,
        getVehicles,
        goPrev,
        goNext,
        searchVehicles
    }
}