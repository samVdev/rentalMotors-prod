import admin from "@/middleware/admin"
import auth from "@/middleware/auth"

export default [
    {
        path: "/vehicles/bikes",
        name: "vehicles-bikes",
        meta: { middleware: [auth, admin], layout: "default" },
        component: () => import("@/modules/Vehicles/views/index.vue").then(m => m.default),
        children: [
            {
                path: "create",
                name: "bikesCreate",
                meta: { middleware: [auth, admin], layout: "default" },
                component: () => import("@/modules/Vehicles/views/CreateOrEdit.vue").then(m => m.default),
                props: true
            }, {
                path: "edit/:id",
                name: "bikesEdit",
                meta: { middleware: [auth, admin], layout: "default" },
                component: () => import("@/modules/Vehicles/views/CreateOrEdit.vue").then(m => m.default),
                props: true
            }
        ]
    },
    {
        path: "/vehicles/cars",
        name: "vehicles-cars",
        meta: { middleware: [auth, admin], layout: "default" },
        component: () => import("@/modules/Vehicles/views/indexCars.vue").then(m => m.default),
        children: [
            {
                path: "create",
                name: "carsCreate",
                meta: { middleware: [auth, admin], layout: "default" },
                component: () => import("@/modules/Vehicles/views/CreateOrEdit.vue").then(m => m.default),
                props: true
            }, {
                path: "edit/:id",
                name: "carsEdit",
                meta: { middleware: [auth, admin], layout: "default" },
                component: () => import("@/modules/Vehicles/views/CreateOrEdit.vue").then(m => m.default),
                props: true
            }
        ]
    }
]
