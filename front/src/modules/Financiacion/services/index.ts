import Http from "@/utils/Http";

export default {
    get(query?: string) {
        return Http.get(`/api/financing/?${query}`)
    },

    resume() {
        return Http.get(`/api/financing/resume/`)
    },

    getOne(id: string) {
        return Http.get(`/api/financing/one/${id}`)
    },

    show(id: string) {
        return Http.get(`/api/financing/${id}`)
    },

    post(payload: any) {
        return Http.post(`/api/financing`, payload)
    },

    put(id: string, payload: any) {
        return Http.post(`/api/financing/edit/${id}`, payload)
    },

    updatePlaca(id: string, payload: any) {
        return Http.put(`/api/financing/placa/${id}`, payload)
    },

    updateFinanceDetails(id: string, payload: any) {
        return Http.put(`/api/financing/finance-details/${id}`, payload)
    },

    moraEstatus(id: any) {
        return Http.post(`/api/financing/mora/${id}`)
    },

    delete(id: number) {
        return Http.delete(`/api/financing/${id}`)
    },

    async downloadInvoice(id: number | string, code: string) {
        try {
            const response = await Http.getFile(`/api/financing/invoice/${id}`)
            const url = window.URL.createObjectURL(new Blob([response.data], { type: 'application/pdf' }))
            const link = document.createElement('a')
            link.href = url
            link.setAttribute('download', `factura_${code}.pdf`)
            document.body.appendChild(link)
            link.click()
            link.remove()
            window.URL.revokeObjectURL(url)
        } catch (error) {
            console.error('Error downloading invoice:', error)
            throw error
        }
    },
    async updateRequirements(id: string | number, payload: FormData) {
        return Http.post(`/api/financing/requirements/${id}`, payload)
    }
};
