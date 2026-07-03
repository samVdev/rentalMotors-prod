import admin from "@/middleware/admin"
import auth from "@/middleware/auth"

export default [
    {
        path: "/maintenance",
        name: "maintenance-index",
        meta: { middleware: [auth, admin], layout: "default" },
        component: () => import("@/modules/Maintenance/views/index.vue").then(m => m.default),
        children: [
            {
                path: "form/:id?/:cedula?",
                name: "maintenance-form",
                meta: { middleware: [auth, admin], layout: "default" },
                component: () => import("@/modules/Maintenance/views/CreateOrEdit.vue").then(m => m.default),
                props: true
            }
        ]
    }
]
