import axios from "axios";
import type { AxiosInstance, AxiosResponse, AxiosError } from "axios";
import type { Paiload } from "@/utils/Types"
import type { Init } from "./Http";
import init from "./init";

export class Http {
  private service: AxiosInstance;
  constructor(init: Init) {
    this.defaultInit()
    let service = axios.create({
      headers: init.customHeaders,
      params: init.customParams,
      baseURL: init.baseURL,
      withCredentials: init.withCredentials,
    });
    service.interceptors.response.use(init.handleSuccess, init.handleError);

    service.interceptors.request.use(async (req) => {
      if(req.method === 'get') return req

      let xsrfToken = this.getXsrfToken();
      
      if (!xsrfToken) {
        await this.get('/sanctum/csrf-cookie')
        xsrfToken = this.getXsrfToken();
      } 

      req.headers['X-XSRF-TOKEN'] = xsrfToken;
      return req; 
    });

    this.service = service;
  }

  private getXsrfToken(): string | null {
    const xsrfCookie = document.cookie
      .split(';')
      .find(cookie => cookie.trim().startsWith('XSRF-TOKEN='));

    return xsrfCookie ? decodeURIComponent(xsrfCookie.split('=')[1]) : null;
  }

  defaultInit() {
    init.customHeaders = init.customHeaders !== undefined ? init.customHeaders : {}
    init.customParams = init.customParams !== undefined ? init.customParams : {}
    init.baseURL = init.baseURL !== undefined ? init.baseURL : "http://localhost"
    init.withCredentials = init.withCredentials !== undefined ? init.withCredentials : false
    init.handleSuccess = init.handleSuccess !== undefined ? init.handleSuccess : this.defaultHandleSuccess
    init.handleError = init.handleError !== undefined ? init.handleError : this.defaultHandleError
  }

  defaultHandleSuccess(response: AxiosResponse) { return Promise.resolve(response); }

  defaultHandleError(error: AxiosError) { return Promise.reject(error); }

  get(path: string) {
    return this.service.request({
      method: "GET",
      url: path,
      responseType: "json"
    });
  }

  getFile(path: string) {
    return this.service.request({
      method: "GET",
      url: path,
       responseType: "blob"
    });
  }

  patch(path: string, payload: Paiload) {
    return this.service.request({
      method: "PATCH",
      url: path,
      responseType: "json",
      data: payload
    });
  }

  post(path: string, payload?: Paiload) {
    return this.service.request({
      method: "POST",
      url: path,
      responseType: "json",
      data: payload
    });
  }

  put(path: string, payload: Paiload) {
    return this.service.request({
      method: "PUT",
      url: path,
      responseType: "json",
      data: payload
    });
  }

  delete(path: string) {
    return this.service.request({
      method: "DELETE",
      url: path,
      responseType: "json",
    });
  }
}

export default new Http(init);
