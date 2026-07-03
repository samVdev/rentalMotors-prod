import admin from "@/middleware/admin"
import auth from "@/middleware/auth"

export default [
    {
        path: "/payments",
        name: "payments",
        meta: { middleware: [auth, admin], layout: "default" },
        component: () => import("@/modules/Payments/views/index.vue").then(m => m.default),
    }
]
