export interface PagoInterface {
  financing_id?: string,
  id?: string,
  monto: string | number
  mora: any
  is_mora?: boolean
  mora_percentage?: number | null
  mora_index?: number | null
  fecha: string
  cuota: string
  codigo: string
  cedula: string
  cliente: string
  file: string
  lote: string
  cuenta_destino?: string
  status: 'pending' | 'approved' | 'rejected',
}