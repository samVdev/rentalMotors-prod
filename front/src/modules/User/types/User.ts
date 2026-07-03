export default interface User {
  id?: string;
  name: string | null;
  phone: string | null;
  email: string | null;
  password: string | null;
  role_id: string | null;
  cedula: string | null;
  dateN: string | null;
  dir: string | null;
  usuario: string | null;
  lotes?: Array<any>;
}

