import admin from "@/middleware/admin"
import auth from "@/middleware/auth"

export default [
    {
        path: "/view-apply",
        name: "view-apply",
        meta: { middleware: [auth, admin], layout: "default" },
        component: () => import("@/modules/Application/views/index.vue").then(m => m.default),
    }
]
