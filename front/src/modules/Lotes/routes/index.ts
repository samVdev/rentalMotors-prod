import auth from "@/middleware/auth"
import admin from "@/middleware/admin"

export default [{
    path: "/lotes",
    name: "lotes",
    meta: { middleware: [auth, admin], layout: "default" },
    component: () => import("@/modules/Lotes/views/Index.vue").then(m => m.default),
    children: [
        {
            path: "create",
            name: "loteCreate",
            meta: { middleware: [auth, admin], layout: "default" },
            component: () => import("@/modules/Lotes/views/CreateOrEdit.vue").then(m => m.default),
            props: true
        },
    ]
}]
