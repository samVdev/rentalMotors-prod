import Http from "@/utils/Http";

const path = "/api/account-methods"

export const getAccountMethods = (query: string) => {
    return Http.get(`${path}/?${query}`);
}

export const getAccountMethod = (id: string | number) => {
    return Http.get(`${path}/${id}`);
}

export const insertAccountMethod = (form: any) => {
    return Http.post(path, form);
}

export const updateAccountMethod = (id: string | number, form: any) => {
    return Http.put(`${path}/${id}`, form);
}

export const deleteAccountMethod = (id: string | number) => {
    return Http.delete(`${path}/${id}`);
}

export const toggleAccountMethodStatus = (id: string | number) => {
    return Http.patch(`${path}/${id}/toggle-status`, {});
}
