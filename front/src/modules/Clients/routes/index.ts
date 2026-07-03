import auth from "@/middleware/auth"
import admin from "@/middleware/admin"

export default [{
    path: "/clients",
    name: "clients",
    meta: { middleware: [auth, admin], layout: "default" },
    component: () => import("@/modules/Clients/views/Index.vue").then(m => m.default),
    children: [
        {
            path: "create",
            name: "clientCreate",
            meta: { middleware: [auth, admin], layout: "default" },
            component: () => import("@/modules/Clients/views/CreateOrEdit.vue").then(m => m.default),
            props: true
        }, {
            path: "edit/:uuid",
            name: "clientEdit",
            meta: { middleware: [auth, admin], layout: "default" },
            component: () => import("@/modules/Clients/views/CreateOrEdit.vue").then(m => m.default),
            props: true
        }
    ]
},
{
    path: "/clients/show/:type/:id",
    name: "client-show",
    meta: { middleware: [auth, admin] },
    component: () => import("@/modules/Clients/views/showClient.vue").then(m => m.default),

}

]
