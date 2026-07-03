import Http from "@/utils/Http";

export default {
  getClient(clientId: string, query: string) {
    return Http.get(`/api/clients/${clientId}/?${query}`);
  },      
  getclients(query) {  
    return Http.get(`/api/clients/?${query}`);
  },  
  insertClient(form) {
    return Http.post(`/api/clients`, form);
  },
  updateClient(clientId, form, query: string) {
    return Http.post(`/api/clients/${clientId}/?${query}`, form);
  },
  deleteClient(clientId) {
    return Http.delete(`/api/clients/${clientId}`);
  },
};
