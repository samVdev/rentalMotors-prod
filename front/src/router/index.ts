// @ts-nocheck

import { createRouter, createWebHistory } from 'vue-router'
import type { RouteRecordRaw } from 'vue-router'
import { computed } from "vue"
import { useAuthStore } from '@/modules/Auth/stores'
import middlewarePipeline from "@/router/middlewarePipeline"

// routes
import AuthRoutes from "@/modules/Auth/routes"
import AuthorizationRoutes from "@/modules/Authorization/routes"
import UserRoutes from "@/modules/User/routes"
import GuestRoutes from "@/modules/guest/routes"
import ClientsRoutes from "@/modules/Clients/routes"
import ApplyRoutes from "@/modules/Application/routes"
import VehiclesRoutes from "@/modules/Vehicles/routes"
import FinanciamientoRoutes from "@/modules/Financiacion/routes"
import PaymentsRoutes from "@/modules/Payments/routes"
import MaintenanceRoutes from "@/modules/Maintenance/routes"
import LotesRoutes from "@/modules/Lotes/routes"
import AccountMethodsRoutes from "@/modules/AccountMethods/routes"
import CobrosRoutes from "@/modules/Cobros/routes"

const storeAuth = computed(() => useAuthStore())

const routes: Array<RouteRecordRaw> = [
  ...AuthRoutes.map(route => route),
  ...AuthorizationRoutes.map(route => route),
  ...UserRoutes.map(route => route),
  ...ApplyRoutes.map(route => route),
  ...GuestRoutes.map(route => route),
  ...ClientsRoutes.map(route => route),
  ...VehiclesRoutes.map(route => route),
  ...FinanciamientoRoutes.map(route => route),
  ...PaymentsRoutes.map(route => route),
  ...MaintenanceRoutes.map(route => route),
  ...LotesRoutes.map(route => route),
  ...AccountMethodsRoutes.map(route => route),
  ...CobrosRoutes.map(route => route),
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
});

router.beforeEach((to, from, next) => {
  const middleware = to.meta.middleware;
  const context = { to, from, next, storeAuth };

  if (!middleware) {
    return next();
  }

  middleware[0]({
    ...context,
    next: middlewarePipeline(context, middleware, 1),
  });
});

export default router