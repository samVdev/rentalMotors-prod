import Http from "@/utils/Http";

export default {
    getAll() {
        return Http.get('/api/services')
    }
};
