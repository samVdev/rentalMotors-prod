import auth from "@/middleware/auth"
import admin from "@/middleware/admin"

export default [{
    path: "/account-methods",
    name: "accountMethods",
    meta: { middleware: [auth, admin], layout: "default" },
    component: () => import("@/modules/AccountMethods/views/Index.vue").then(m => m.default),

    children: [{
        path: "form",
        name: "accountMethodCreate",
        meta: { middleware: [auth, admin], layout: "default" },
        component: () => import("@/modules/AccountMethods/views/CreateOrEdit.vue").then(m => m.default),
        props: true
    }, {
        path: "form/:id",
        name: "accountMethodEdit",
        meta: { middleware: [auth, admin], layout: "default" },
        component: () => import("@/modules/AccountMethods/views/CreateOrEdit.vue").then(m => m.default),
        props: true
    }]
}]
