import Http from "@/utils/Http";

export default {
  getHomeService() {
    return Http.get(`/api/home-client/data`);
  },

  storePayService(id: string, payload: any) {
    return Http.post(`/api/home-client/payment/${id}`, payload);
  },

  mantenimientService(id: string) {
    return Http.post(`/api/home-client/mantenimient/${id}`);
  },

  getActiveAccountMethods() {
    return Http.get('/api/home-client/account-methods');
  }
};
