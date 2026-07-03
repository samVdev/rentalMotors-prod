<?php

use App\Models\Financing;
use App\Models\Personas;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Payment;
use App\Models\Lote;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

// Bootstrap Laravel
require __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// 1. Ensure we have a Lote
$lote = Lote::firstOrCreate(['nombre' => 'POS 07']);

// Find or create admin responsible
$admin = User::where('is_admin', true)->first() ?? User::factory()->create(['is_admin' => true, 'username' => 'admin_test']);

if ($admin) {
    $admin->lotes()->syncWithoutDetaching([$lote->id]);
}

function parseAmount($value)
{
    if (!$value)
        return 0;
    // Eliminar símbolos de moneda y separadores de miles
    $clean = str_replace(['$', ',', ' '], '', $value);
    return (float) $clean;
}

function parseDate($value)
{
    if (!$value)
        return now();

    // Limpieza inicial: Convertir a string y quitar espacios raros (incluyendo Unicode)
    $cleanValue = trim(preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', (string) $value));

    if ($cleanValue === '' || $cleanValue === 'FECHA')
        return now();

    // Si tiene un $, probablemente es un monto que se coló como fecha
    if (strpos($cleanValue, '$') !== false) {
        // No logueamos para no saturar, pero devolvemos ahora para que no falle
        return now();
    }

    // 1. Si ya es un objeto de fecha
    if ($value instanceof \DateTimeInterface) {
        return Carbon::instance($value)->startOfDay();
    }

    // 2. Intentar formatos específicos (muy comunes en estos Excel)
    $formats = ['d/m/Y', 'Y-m-d', 'd-m-Y', 'j/n/y', 'j/n/Y'];
    foreach ($formats as $format) {
        try {
            $dt = Carbon::createFromFormat($format, $cleanValue);
            if ($dt)
                return $dt->startOfDay();
        } catch (\Exception $e) {
            continue;
        }
    }

    // 3. Si es un número (formato interno de Excel)
    if (is_numeric($cleanValue) && (float) $cleanValue > 30000) {
        try {
            return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($cleanValue))->startOfDay();
        } catch (\Exception $e) {
            return Carbon::create(1899, 12, 30)->addDays((int) $cleanValue)->startOfDay();
        }
    }

    // 4. Último recurso: Dejar que Carbon intente adivinar
    try {
        return Carbon::parse($cleanValue)->startOfDay();
    } catch (\Exception $e) {
        echo "  [DEBUG] Falló el parseo de fecha en todas las etapas para: '{$cleanValue}'\n";
        return now();
    }
}

$isDryRun = in_array('--dry-run', $argv);

if ($isDryRun) {
    //echo "MODO SIMULACIÓN (DRY-RUN) ACTIVADO. No se guardarán cambios en la base de datos.\n";
}

$masiveDir = __DIR__;
$files = glob($masiveDir . '/*.xlsx');

if (empty($files)) {
    die("No se encontraron archivos .xlsx en {$masiveDir}\n");
}

foreach ($files as $filePath) {
    echo "\nProcesando archivo: " . basename($filePath) . "\n";

    try {
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getSheet(0);
        $rows = $worksheet->toArray();

        // El bloque de datos principal empieza en la fila 4 (index 3)
        // Pero los datos reales estan en filas específicas

        $vData = $rows[1] ?? []; // Datos de vehículo (Fila 2)
        $fData = $rows[4] ?? []; // Datos de financiación (Fila 5)
        $pData = $rows[6] ?? []; // Datos personales (Fila 7)

        if (empty($vData) || empty($fData) || empty($pData)) {
            // echo "  [WARNING] El archivo no tiene el formato esperado (faltan filas clave). Saltando...\n";
            continue;
        }

        $brand = $vData[3] ?? 'N/A';
        $model = $vData[1] ?? 'N/A'; // RAIDER 125R en B2
        $year = $vData[2] ?? '2024'; // Año en C2
        $plate = $vData[4] ?? 'N/A';
        // Cargar Hoja 2 por adelantado para datos específicos
        $sheet2 = null;
        try {
            if ($spreadsheet->getSheetCount() > 1) {
                $sheet2 = $spreadsheet->getSheet(1);
            }
        } catch (\Exception $e) {
        }

        // Móvil desde Hoja 2 C5 (respaldo Hoja 1 index 5)
        $movil = 'N/A';
        if ($sheet2) {
            $movilValue = trim((string) $sheet2->getCell('C5')->getCalculatedValue());
            if ($movilValue && $movilValue !== '') {
                $movil = $movilValue;
            }
        }

        if ($movil === 'N/A') {
            $movil = $vData[5] ?? 'N/A';
        }
        $price = parseAmount($vData[6] ?? 0);
        $initial = parseAmount($vData[7] ?? 0);

        $finalPrice = parseAmount($fData[2]); // C5
        $deudaAdquirida = max(
            parseAmount($spreadsheet->getActiveSheet()->getCell('G10')->getCalculatedValue()),
            parseAmount($spreadsheet->getActiveSheet()->getCell('G11')->getCalculatedValue())
        );
        $finalPrice += $deudaAdquirida;

        $interes = parseAmount($fData[1]);
        $months = (int) ($fData[3] ?? 0); // D5
        $pMensual = parseAmount($fData[4]); // E5
        $pQuincenal = parseAmount($fData[5]); // F5
        $pSemanal = parseAmount($fData[6] ?? 0); // G5
        $pDiario = parseAmount($fData[7] ?? 0); // H5

        $firstName = $pData[0] ?? 'N/A';
        $lastName = $pData[1] ?? '';
        $fullName = trim($firstName . ' ' . $lastName);
        $cedula = $pData[2] ?? 'N/A'; // C7
        $phone = $pData[4] ?? '0000000000'; // E7 (NUMERO)

        // --- EXTRACCIÓN DE SERVICIOS ---
        // SOAT en F7/F8 (index 5), FIRMA en G7/G8 (index 6), GPS en H7/H8 (index 7)
        $pData7 = $rows[6] ?? [];
        $pData8 = $rows[7] ?? [];

        $soatPrice = max(parseAmount($pData7[5] ?? 0), parseAmount($pData8[5] ?? 0));
        $firmaPrice = max(parseAmount($pData7[6] ?? 0), parseAmount($pData8[6] ?? 0));
        //$gpsPrice = max(parseAmount($pData7[7] ?? 0), parseAmount($pData8[7] ?? 0));
        $deudaSemanaPrice = max(parseAmount($pData8[4] ?? 0), parseAmount($pData7[7] ?? 0));

        $servicesExtracted = [];
        if ($soatPrice > 0)
            $servicesExtracted[] = ['id' => 5, 'nombre' => 'SOAT', 'precio' => $soatPrice];
        if ($firmaPrice > 0)
            $servicesExtracted[] = ['id' => 4, 'nombre' => 'FIRMA', 'precio' => $firmaPrice];
        /*if ($gpsPrice > 0)
            $servicesExtracted[] = ['id' => 2, 'nombre' => 'GPS', 'precio' => $gpsPrice];*/
        if ($deudaSemanaPrice > 0)
            $servicesExtracted[] = ['id' => 6, 'nombre' => 'DEUDA DE SEMANA', 'precio' => $deudaSemanaPrice];

        // --- EXTRACCIÓN DE PAGOS ---
        $paymentsExtracted = [];

        // Extraer datos recorriendo todas las filas y detectando bloques de pagos dinámicamente
        $colPairs = [];
        for ($i = 12; $i < count($rows); $i++) {
            $row = $rows[$i];

            // 1. Detección de Cabeceras: Si la fila tiene "FECHA", redefine las columnas para las siguientes filas
            if (in_array('FECHA', $row)) {
                $colPairs = [];
                $currentDateCol = null;
                foreach ($row as $c => $cell) {
                    $hText = strtoupper(trim((string) $cell));
                    if ($hText === 'FECHA') {
                        $currentDateCol = $c;
                    } elseif ($hText === 'ABONO' && $currentDateCol !== null) {
                        $colPairs[] = ['date' => $currentDateCol, 'amount' => $c];
                    }
                }
                continue; // Saltamos la propia fila de cabecera
            }

            // 2. Extración si ya tenemos columnas detectadas
            if (!empty($colPairs)) {
                foreach ($colPairs as $pair) {
                    $dateVal = $row[$pair['date']] ?? null;
                    $amountVal = $row[$pair['amount']] ?? null;
                    $amount = parseAmount($amountVal);

                    // Filtro: Solo tomamos filas con monto y que NO sean cabeceras o filas de metadatos (Total, REF, etc.)
                    $originalStr = strtoupper(trim((string) $dateVal));
                    $isHeaderOrTotal = in_array($originalStr, ['FECHA', 'REF.', 'REF-.', 'TOTAL', 'REF', 'TOTAL:', '']);
                    $isTotalAmount = $originalStr === 'TOTAL' || strpos($originalStr, '$') !== false;

                    if ($dateVal && $amount > 100 && !$isHeaderOrTotal && !$isTotalAmount) {
                        $paymentsExtracted[] = [
                            'fecha' => $dateVal,
                            'monto' => $amount
                        ];
                    }
                }
            }
        }

        // --- RESUMEN JSON ---
        $summary = [
            'cliente' => [
                'nombre' => $fullName,
                'cedula' => $cedula,
                'telefono' => $phone
            ],
            'vehiculo' => [
                'marca' => $brand,
                'modelo' => $model,
                'precio_lista' => $price
            ],
            'financiacion' => [
                'codigo' => $movil,
                'meses' => $months,
                'interes' => $interes,
                'cuota_inicial' => $initial,
                'monto_financiado' => $price - $initial,
                'total_a_pagar' => $finalPrice,
                'placa' => $plate,
                'cuotas' => [
                    'diaria' => $pDiario,
                    'semanal' => $pSemanal,
                    'quincenal' => $pQuincenal,
                    'mensual' => $pMensual
                ]
            ],
            'servicios_adicionales' => $servicesExtracted,
            'pagos_registrados' => $paymentsExtracted
        ];

        /*echo "\n" . str_repeat("-", 30) . "\n";
        echo "JSON DE EXTRACCIÓN:\n";
        echo json_encode($summary, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
        echo str_repeat("-", 30) . "\n";

        if ($isDryRun) {
            echo "  [INFO] Modo Dry-Run: No se realizarán cambios.\n";
            continue;
        }

        echo "  [PROCESANDO] Guardando en Base de Datos...\n";*/
        DB::beginTransaction();

        // 1. Gestionar Persona
        $persona = Personas::firstOrCreate(
            ['cedula' => $cedula],
            [
                'fullName' => $fullName,
                'phone' => $phone,
                'earnings_month' => '0',
                'date' => '1992-05-19',
                'direction' => '',
            ]
        );

        // 2. Gestionar User (si no existe)
        $user = $persona->user;
        if (!$user) {
            $user = User::create([
                'username' => $cedula,
                'name' => $fullName,
                'email' => strtolower(str_replace(' ', '.', $fullName)) . "@rentalMotors.com",
                'password' => bcrypt($cedula),
                'persona_id' => $persona->id,
                'role_id' => 3,
            ]);
        }

        // 3. Gestionar Vehículo
        $vehicle = Vehicle::create([
            'brand' => $brand,
            'model' => $model,
            'price' => $price,
            'user_id' => $user->id,
            'show' => true,
            'cc' => 125, // RAIDER 125R
            'year' => $year,
            'color' => 'N/A',
            'mileage' => 0,
            'type' => 'bike',
            'image' => ''
        ]);

        // --- DETECCIÓN DE PLAN ---
        $possiblePlans = [
            'Mensual' => $pMensual,
            'Quincenal' => $pQuincenal,
            'Semanal' => $pSemanal,
            'Diario' => $pDiario
        ];

        // --- DETECCIÓN DE PLAN (Desde segunda hoja C10) ---
        $plan = 'Semanal';
        if ($sheet2) {
            try {
                $c10Value = trim((string) $sheet2->getCell('C10')->getCalculatedValue());
                // echo "---- [INFO] excel: {$c10Value}-------\n";

                if (stripos($c10Value, 'MENSUAL') !== false) {
                    $plan = 'Mensual';
                } elseif (stripos($c10Value, 'QUINCENAL') !== false) {
                    $plan = 'Quincenal';
                } elseif (stripos($c10Value, 'SEMANAL') !== false) {
                    $plan = 'Semanal';
                } elseif (stripos($c10Value, 'DIARIO') !== false) {
                    $plan = 'Diario';
                }
                //echo "  [INFO] Plan detectado en Hoja 2: {$plan}\n";
            } catch (\Exception $e) {
                //echo "  [ERROR] Al leer el plan: " . $e->getMessage() . ". Usando 'Semanal'.\n";
            }
        } else {
            //echo "  [WARNING] Solo se encontró una hoja. Usando plan 'Semanal' por defecto.\n";
        }

        $totalCuotas = match ($plan) {
            'Diario' => (int) ($months * 4 * 6),
            'Semanal' => (int) ($months * 4),
            'Quincenal' => (int) ($months * 2),
            'Mensual' => (int) $months,
            default => (int) $months,
        };

        // 4. Gestionar Financiación
        $financing = Financing::create([
            'code' => "MOVIL {$movil}",
            'plan' => $plan,
            'vehicle_id' => $vehicle->id,
            'user_id' => $user->id,
            'plate' => $plate,
            'status' => 'active',
            'type' => 'vehicle',
            'months' => $months,
            'interes_price' => $interes,
            'installments' => $totalCuotas,
            'start_date' => now(),
            'total_inicial' => $initial,
            'cost_price' => $price,
            'financing_price' => $price - $initial,
            'final_price' => $finalPrice,
            'price_diario' => $pDiario,
            'price_semanal' => $pSemanal,
            'price_quincenal' => $pQuincenal,
            'price_mensual' => $pMensual,
            'moraStatus' => true,
            'lote_id' => $lote->id,
            'deuda_adquirida' => $deudaAdquirida, 
            'observation' => "Móvil: {$movil}",
        ]);

        echo " deuda pa {$deudaAdquirida}";

        Payment::create([
            'financing_id' => $financing->id,
            'installment_number' => 0,
            'total' => $initial,
            'status' => 'approved',
            'description' => 'Pago inicial',
            'created_at' => now(),
        ]);

        // 4.1 Sincronizar Servicios
        $servicesToSync = [];
        foreach ($servicesExtracted as $s) {
            $servicesToSync[$s['id']] = ['price' => $s['precio']];
        }
        $financing->services()->sync($servicesToSync);

        // 5. Gestionar Pagos
        foreach ($paymentsExtracted as $idx => $p) {

            if ($p['monto'] > 100) {
                Payment::create([
                    'financing_id' => $financing->id,
                    'installment_number' => $idx + 1,
                    'total' => $p['monto'],
                    'status' => 'approved',
                    'description' => 'Pago importado desde Excel - Fecha original: ' . $p['fecha'],
                    'created_at' => parseDate($p['fecha']),
                ]);
            }

        }

        DB::commit();
        //echo "  [OK] Importación exitosa para ID: {$financing->id} (" . count($paymentsExtracted) . " pagos)\n";

    } catch (\Exception $e) {
        DB::rollBack();
         echo "  [ERROR] Al procesar: " . $e->getMessage() . "\n";
    }
}
