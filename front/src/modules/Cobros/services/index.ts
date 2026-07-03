import Http from "@/utils/Http";

export default {
    getSummary() {
        return Http.get('/api/cobros/summary')
    },

    getPending() {
        return Http.get('/api/cobros/pending')
    },

    getCompleted() {
        return Http.get('/api/cobros/completed')
    },

    toggleGPS(id: string | number, status: boolean) {
        return Http.post(`/api/cobros/gps/${id}`, { status })
    },

    toggleMoto(id: string | number, status: boolean) {
        return Http.post(`/api/cobros/moto/${id}`, { status })
    },

    notifyWhatsApp(id: number, type: 'warning' | 'success') {
        return Http.post('/api/cobros/notify-whatsapp', { id, type })
    },

    createMora(id: number) {
        return Http.post('/api/cobros/mora', { financing_id: id })
    }
};
