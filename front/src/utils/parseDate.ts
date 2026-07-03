export function parseDate(fechaISO: string) {
    const fecha = new Date(fechaISO);
  
    return fecha.toLocaleString('es-VE', {
      timeZone: 'America/Caracas',
      weekday: 'long',
      year: 'numeric',
      month: 'long',   
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
      second: '2-digit',
      hour12: true    
    });
  }

  