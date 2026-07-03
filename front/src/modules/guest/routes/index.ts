import auth from "@/middleware/auth"
import guest from "@/middleware/guest"

export default [
    {
        path: "/",
        name: "index",
        component: () => import("@/modules/guest/views/landing.vue").then(m => m.default),
    },
    {
        path: "/apply",
        name: "apply",
        component: () => import("@/modules/guest/views/AplicationForm.vue").then(m => m.default),
    },

    {
        path: "/privacy",
        name: "privacy",
        component: () => import("@/modules/guest/views/privacy.vue").then(m => m.default),
    },
    {
        path: "/home",
        name: "home",
        meta: { middleware: [auth], layout: "default" },
        component: () => import("@/modules/guest/views/home.vue").then(m => m.default),
    },
    {
        path: "/home/payments",
        name: "guest-payments",
        meta: { middleware: [auth], layout: "default" },
        component: () => import("@/modules/guest/views/Payments.vue").then(m => m.default),
    }
]
