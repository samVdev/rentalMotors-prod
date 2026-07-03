import Http from "@/utils/Http";

export default {
    get(query?: string) {
        return Http.get(`/api/payments/?${query}`)
    },

    resume() {
        return Http.get(`/api/payments/resume`)
    },

    put(id: string, value: boolean) {
        return Http.put(`/api/payments/action/${id}`, { value })
    },
};
