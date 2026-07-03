export interface PaymentInterface {
  nro_cuota: number;
  total: number;
  mora_total: number;
  descripcion: string;
  status: string;
  archivo: string | null;
  mora: string,
  date: string,
  mora_index: any;
  total_capital?: number;
  total_interes?: number;
  interes_porcent?: number;
}
