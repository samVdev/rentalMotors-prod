import Http from "@/utils/Http";

export default {
  get() {
    return Http.get(`/api/lotes`);
  },
  getMin() {
    return Http.get(`/api/lotes/check`);
  },
  insert(form) {
    return Http.post(`/api/lotes`, form);
  },
  delete(id) {
    return Http.delete(`/api/lotes/${id}`);
  },
};
