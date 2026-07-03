import Swal from "sweetalert2";
import type { SweetAlertIcon } from "sweetalert2";

export const questionSweet = async (title: string, text: string, icon: SweetAlertIcon) => {
    const result = await Swal.fire({
        title,
        html:text,
        icon,
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar',
        showCancelButton: true,
        showCloseButton: true,
        confirmButtonColor: '#FF7539',
        cancelButtonColor: '#7F7F7F'
    });

    return result.value;
}