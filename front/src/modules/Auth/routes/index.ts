import auth from "@/middleware/auth"
import guest from "@/middleware/guest"
import admin from "@/middleware/admin"

export default [
    {
        path: "/login",
        name: "Login",
        meta: { middleware: [guest], },
        component: () => import("@/modules/Auth/views/Login.vue").then(m => m.default)
    },
    {
        path: "/profile",
        name: "profile",
        meta: { middleware: [auth], layout: "default" },
        component: () => import("@/modules/Auth/views/Profile.vue").then(m => m.default),
    }, 
    {
        path: "/dashboard",
        name: "dashboard",
        meta: { middleware: [auth, admin], layout: "default" },
        component: () => import("@/modules/Auth/views/Dashboard.vue").then(m => m.default),
    },

    {
        path: "/:catchAll(.*)",
        name: "NotFound",
        meta: { middleware: [guest] },
        component: () => import("@/modules/Auth/components/NotFound.vue").then(m => m.default),
    }
]
