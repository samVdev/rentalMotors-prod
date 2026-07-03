import { defineAsyncComponent } from 'vue'
import app from '@/plugins/app'

app
  .component('DefaultLayout', defineAsyncComponent(() => import('@/layouts/DashboardLayout.vue')))
  .component('EmptyLayout', defineAsyncComponent(() => import('@/layouts/EmptyLayout.vue')))
