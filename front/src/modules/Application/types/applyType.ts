export interface applyInterface {
  id: number;
  date: string;
  cedula: string;
  phone: string;
  full_name: string;
  financiacion_type: string;
  plan: string;
  document?: string | null;
  vehicle_image?: string | null;
  vehicle_brand: string;
  vehicle_model: string;
  precio: number;
  pay_one?: string;
  estatus?: boolean;
  client_id?: string;
  codigo?: string
}
