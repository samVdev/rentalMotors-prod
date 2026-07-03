import admin from "@/middleware/admin"
import auth from "@/middleware/auth"

export default [
    {
        path: "/cobros",
        name: "cobros",
        meta: { middleware: [auth, admin], layout: "default" },
        component: () => import("@/modules/Cobros/views/CobrosView.vue").then(m => m.default),
    },
]
