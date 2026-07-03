import Swal from "sweetalert2";
import type { SweetAlertIcon } from "sweetalert2";

export const alerta = (title: string, text: string, icon: SweetAlertIcon) => {
    Swal.fire({
        title,
        text,
        icon,
    });
}