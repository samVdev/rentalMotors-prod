import Http from "@/utils/Http";

export default {
    get(query?: string) {
        return Http.get(`/api/vehicles/?${query}`)
    },

    getMin() {
        return Http.get(`/api/vehicles/min`)
    },

    getVehicle(id: string) {
        return Http.get(`/api/vehicles/${id}`)
    },

    post(payload: any) {
        return Http.post(`/api/vehicles`, payload)
    },

    put(id: string, payload: any) {
        return Http.post(`/api/vehicles/edit/${id}`, payload)
    },

    delete(id: number) {
        return Http.delete(`/api/vehicles/${id}`)
    }
};
