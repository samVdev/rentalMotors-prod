export interface VehicleType {
    id?: number
    marca: string
    modelo: string
    year: number | null
    cc: string
    color: string
    precio: string | null
    kilometraje: number | null
    image: string
    imageFile?: string | null
    type: 'bike' | 'car' | null
    show: boolean,
  }
  