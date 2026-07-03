export default interface MaintenanceForm {
    id?: number,
    type: number,
    id_for_mant: number,
    total: number,
    fecha: string,
    descripcion: string,
    persona_id: number,
    cedula: string
}