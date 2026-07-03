import { meses } from '@/utils/constantes/months';
import type { AxiosResponse } from 'axios';
import { ref } from 'vue';
import { useRouter, useRoute } from 'vue-router'
import type { RouteLocationNormalizedLoaded, Router } from 'vue-router'

interface Data {
  rows: any[];
  links: string[];
  search: string;
  sort: string;
  direction: string;
  offset: number,
  month?: number,
  year?: string
}

interface TableGrid {
  route: RouteLocationNormalizedLoaded;
  router: Router;
  setSearch: (e: Event) => void;
  setSort: (s: string) => void;
}

export default (data: Data, getItemsScroll: () => Promise<AxiosResponse<any, any>>) => {
  const router = useRouter()
  const route = useRoute()

  const setSearch = (e: Event): void => {
    load({ search: (e.target as HTMLInputElement).value })
  }

  const setMonth = (e): void => {
    const month = e.month as string
    if(!e.month) return
    load({ month })
   /*const findedMonth = meses.find(e => e.name.toLowerCase() == month.toLowerCase())
    if(!findedMonth) return
    load({ month: findedMonth.number })*/
  }

  const setYear = (e): void => {
    const year = e.year as string
    if(!year) return
    load({ year })
  }

  // sort
  const setSort = (s: string): void => { // "s" is abbreviation of "sort"
    // reverse direction if clicked twice on column
    let d = "asc";         // "d" is abbreviation of "direction"
    if (data.sort == s) {
      d = data.direction == "asc" ? "desc" : "asc";
    }
    load({ direction: d, sort: s })
  };

  // setLoad
  const load = (newParams: object): void => {
    const params = {
      search: data.search || "",
      sort: data.sort || "",
      direction: data.direction || "",
      //offset: data.offset || "0",
      month: data.month || '',
      ...newParams,
    }

    router.push({
      path: route.path,
      query: {
        ...route.query,
        ...params,
      }
    })
  }


  const isFetching = ref(false);
  const moreScroll = ref(true);

  const loadScroll = async (e: Event) => {

    const target = e.currentTarget as HTMLElement;

    if (isFetching.value || !moreScroll.value) return;
    isFetching.value = true;

    const scrollTopBefore = target.scrollTop;

    target.style.pointerEvents = "none";
    target.style.scrollBehavior = "auto";

    const { scrollTop, clientHeight, scrollHeight } = target;

    if (scrollTop + clientHeight >= scrollHeight - 5) {

      const response = await getItemsScroll();
      if (response.data.rows.length > 0) {
        data.rows.push(...response.data.rows)
        moreScroll.value = true
        data.offset += 10
      } else {
        moreScroll.value = false
        setTimeout(() => {
          moreScroll.value = true;
        }, 10000);
      }

      requestAnimationFrame(() => {
        target.scrollTop = scrollTopBefore;
        target.style.pointerEvents = "auto";
        isFetching.value = false;
      });

    } else {
      isFetching.value = false;
      target.style.pointerEvents = "auto";
    }
  };

  return {
    route,
    router,
    setSearch,
    setYear,
    setSort,
    loadScroll,
    load,
    setMonth
  }
}

