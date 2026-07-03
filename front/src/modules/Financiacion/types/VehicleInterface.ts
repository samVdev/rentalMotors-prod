import type { PaymentInterface } from "./PaymentInterface"

export interface FinancingDetailsInterface {
  id: number | null
  application_id: number | null
  codigo: string
  lote: string
  recaudos_pdf: string | null
  plan: null | 'Diario' | 'Semanal' | 'Quincenal' | 'Mensual'

  vehiculo_marca: string
  vehiculo_model: string
  vehiculo_placa: string

  cliente: string | null
  cliente_ci: string | null
  cliente_contact: string | null
  cliente_archivo: string | null

  image_vehiculo: string,
  vehicle: string,
  tipo: string
  cuotas?: number | null
  meses: number | null
  fecha_inicio: string
  precio_final?: number
  faltante?: number
  precio_mora?: number
  pagado?: number
  observacion?: string
  estado: 'pendiente' | 'aprobado' | 'rechazado' | 'activa' | 'finalizada'
  payments: PaymentInterface[],
  mora_index: any,
  precio: number,
  inicial: number,
  cost_inicial: number,
  intereses: number,

  mensual: number,
  quincenal: number,
  semanal: number,
  diario: number,
  interes_porcent?: number,

  moraEstatus?: boolean
  services?: Array<{
    id: number
    name: string
    is_included: boolean
    price: number
  }>

  deudaAdquirida: number,
  deudaPagar: number,

}
