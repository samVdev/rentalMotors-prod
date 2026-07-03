import admin from "@/middleware/admin"
import auth from "@/middleware/auth"

export default [
    {
        path: "/financing",
        name: "financing",
        meta: { middleware: [auth, admin], layout: "default" },
        component: () => import("@/modules/Financiacion/views/index.vue").then(m => m.default),
        children: [
            {
                path: "form/:id?",
                name: "financing-form",
                meta: { middleware: [auth, admin], layout: "default" },
                component: () => import("@/modules/Financiacion/views/CreateOrEdit.vue").then(m => m.default),
                props: true
            }
        ]
    },
]
