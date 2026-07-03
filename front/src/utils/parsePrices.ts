export const parsePrices = (number: number | undefined | null) => {
    if (number === undefined || number === null) return '$ 0';
    return number.toLocaleString('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 0 });
}