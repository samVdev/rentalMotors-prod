import Http from "@/utils/Http";

export default {
    get(query?: string) {
        return Http.get(`/api/apply/?${query}`)
    },

    put(id: string, value: boolean) {
        return Http.put(`/api/apply/action/${id}`, { value })
    },

    anexarDoc(id: string, payload: any) {
        return Http.post(`/api/apply/doc/${id}`, payload)
    },

    async downloadInvoice(id: number, codigo?: string) {
        const response = await Http.getFile(`/api/apply/invoice/${id}`);
        const url = window.URL.createObjectURL(new Blob([response.data], { type: 'application/pdf' }));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `factura_${codigo ?? id}.pdf`);
        document.body.appendChild(link);
        link.click();
        link.remove();
        window.URL.revokeObjectURL(url);
    },
    async updateRequirements(id: string | number, payload: FormData) {
        return Http.post(`/api/apply/requirements/${id}`, payload)
    }
};
