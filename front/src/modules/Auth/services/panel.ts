import Http from "@/utils/Http";

export default {
  getCountedDataService () {
    return Http.get(`/api/statics/admin/counted`);
  },

  getPayments () {
    return Http.get(`/api/statics/admin/payments`);
  },

  getFinancing () {
    return Http.get(`/api/statics/admin/financings`);
  }
}