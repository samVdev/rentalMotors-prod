import Http from "@/utils/Http";

export const getInfoService = () => {
    return Http.get("api/config/");
}

export const editNewAccountService = (payload: any) => {
    return Http.put("api/config/", payload);
}

export const getDolar = () => {
    return Http.get("api/config/dolar");
}

export const getNewDolarService = () => {
    return Http.post("api/config/update-dollar", {});
}