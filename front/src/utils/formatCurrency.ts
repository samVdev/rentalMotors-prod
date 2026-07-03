const FormatCurrency = (value: string | number) => {
    if (value === null || value === undefined || value === '') return ''

    let strValue = String(value);

    let raw = strValue.replace(/\./g, '').replace(/[^\d,]/g, '');

    const parts = raw.split(',');
    const integerPart = parts[0];
    const decimalPart = parts.length > 1 ? ',' + parts[1] : '';

    if (integerPart === '' && parts.length === 1) return '';

    const parsedInt = parseInt(integerPart || '0', 10);
    if (isNaN(parsedInt)) return decimalPart;

    const formattedInteger = new Intl.NumberFormat("es-CO").format(parsedInt);

    return formattedInteger + decimalPart;
}


const unFormatCurrency = (value: string | number) => {
    if (value === null || value === undefined || value === '') return ''

    let strValue = String(value);

    let raw = strValue.replace(/\./g, '').replace(/,/g, '.');

    return raw;
}

export { FormatCurrency, unFormatCurrency }