import Http from "@/utils/Http";

export default {
    get(query?: string) {
        return Http.get(`/api/maintenance${query ? `/?${query}` : ''}`);
    },

    show(id: number | string) {
        return Http.get(`/api/maintenance/${id}`);
    },

    getApplyOrFinancing(id: number, cedula: string) {
        return Http.get(`/api/maintenance/check-type/${id}/${cedula}`);
    },

    store(payload: any) {
        return Http.post(`/api/maintenance`, payload);
    },

    update(id: number | string, payload: any) {
        return Http.put(`/api/maintenance/edit/${id}`, payload);
    },

    toggleStatus (id: number | string, payload?: any) {
        return Http.patch(`/api/maintenance/toggle-status/${id}`, payload);
    },
    
    destroy(id: number | string) {
        return Http.delete(`/api/maintenance/${id}`);
    }
};
