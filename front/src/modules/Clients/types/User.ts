export default interface User {
  id?: string;
  name: string | null;
  usuario: string | null;
  phone: string | null;
  email: string | null;
  cedula: string | null;
  dateN: string | null;
  dir: string | null;
  earnings: string | null;
  imageFile?: string | null;
  image?: File | null;
  imageApp?: string | null;
}

