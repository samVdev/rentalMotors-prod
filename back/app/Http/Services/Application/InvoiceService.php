<?php

namespace App\Http\Services\Application;

use App\Models\Application;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Response;

class InvoiceService
{
    public static function generate(int $id): Response
    {
        $financing = \App\Models\Financing::with(['vehicle', 'user.persona', 'services', 'payments'])->findOrFail($id);
        $vehicle   = $financing->vehicle;
        $persona   = $financing->user?->persona;

        $html = self::buildHtml($persona, $vehicle, $financing);

        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'factura_' . ($financing?->code ?? $id) . '.pdf';

        return response(
            $dompdf->output(),
            200,
            [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]
        );
    }

    private static function buildHtml($persona, $vehicle, $financing): string
    {
        $logoPath = storage_path('app/logo.png');
        $logoData = base64_encode(file_get_contents($logoPath));
        $logoSrc = 'data:image/png;base64,' . $logoData;

        $fecha          = $financing->created_at->format('d/m/Y');
        $nombre         = $persona?->fullName  ?? 'N/A';
        $cedula         = $persona?->cedula    ?? 'N/A';
        $telefono       = $persona?->phone     ?? 'N/A';
        $direccion      = $persona?->direction ?? 'N/A';
        
        $tipoMap = [
            'vehicle'    => 'Vehículo',
            'tax'        => 'Impuesto',
            'mantenence' => 'Mantenimiento',
        ];
        $tipo = $tipoMap[$financing->type] ?? ucfirst($financing->type ?? 'N/A');

        $plan           = $financing?->plan      ?? 'N/A';
        $codigo         = $financing?->code       ?? 'Sin código';
        $marca          = $vehicle?->brand        ?? 'N/A';
        $modelo         = $vehicle?->model        ?? 'N/A';

        // Numeric values
        $costPriceNum      = (float) ($financing?->cost_price      ?? 0);
        $financingPriceNum = (float) ($financing?->financing_price ?? 0);
        $interesPriceNum   = (float) ($financing?->interes_price   ?? 0);
        $finalPriceNum     = (float) ($financing?->final_price     ?? 0);

        $fmt = fn($v) => number_format($v, 2, '.', ',');

        $interesPct   = $financing ? ($financing->interes_porcent ?? '0') . '%' : '0%';
        $startDate    = $financing && $financing->start_date
                          ? \Carbon\Carbon::parse($financing->start_date)->format('d/m/Y')
                          : 'N/A';
        $months       = $financing?->months       ?? 'N/A';
        $installments = $financing?->installments ?? 'N/A';
        
        $initialPayment   = $financing->payments->where('installment_number', 0)->first();
        $payInitialAmount = $initialPayment ? $initialPayment->total : 0;
        $payInitial       = '$ ' . $fmt((float)$payInitialAmount);
        
        $moraStatus   = $financing ? ($financing->moraStatus ? 'Mora activa' : 'Sin mora') : 'N/A';
        $observation  = $financing?->observation  ?? '';

        $rowBg = ['#ffffff', '#f5f7fb'];
        $i = 0;

        $tipoNombre = $tipo == 'Vehículo' ? $marca . ' ' . $modelo : $tipo;
        // Vehicle main row (2 columns)
        $vehicleRow = '<tr style="background:#ffffff;">'
            . '<td style="padding:10px 14px;border-bottom:1px solid #e8ecf1;"><strong>' . $tipoNombre . '</strong>'
            . '<br><span style="font-size:10px;color:#777;">' . $tipo . '</span></td>'
            . '<td style="padding:10px 14px;border-bottom:1px solid #e8ecf1;text-align:right;">$ ' . $fmt($costPriceNum) . '</td>'
            . '</tr>';

        // Services rows (2 columns)
        $servicesRows = '';
        $totalServicesPrice = 0;
        if ($financing && $financing->services) {
            foreach ($financing->services as $service) {
                $totalServicesPrice += (float) ($service->pivot->price ?? 0);
                $servicesRows .= '<tr style="background:#ffffff;">'
                    . '<td style="padding:10px 14px;border-bottom:1px solid #e8ecf1;">' . $service->name . '</td>'
                    . '<td style="padding:10px 14px;border-bottom:1px solid #e8ecf1;text-align:right;">$ ' . $fmt($service->pivot->price) . '</td>'
                    . '</tr>';
            }
        }

        // Instalment rows (2 columns)
        $itemRows = '';
        $cuotas = [
            'Diaria'    => $financing?->price_diario    ?? 0,
            'Semanal'   => $financing?->price_semanal   ?? 0,
            'Quincenal' => $financing?->price_quincenal ?? 0,
            'Mensual'   => $financing?->price_mensual   ?? 0,
        ];
        foreach ($cuotas as $label => $val) {
            if ((float) $val > 0) {
                $bg = $rowBg[$i % 2];
                $itemRows .= '<tr style="background:' . $bg . ';">'
                    . '<td style="padding:10px 14px;border-bottom:1px solid #e8ecf1;">Cuota ' . $label . '</td>'
                    . '<td style="padding:10px 14px;border-bottom:1px solid #e8ecf1;text-align:right;">$ ' . $fmt($val) . '</td>'
                    . '</tr>';
                $i++;
            }
        }

        // Observation section
        $observationSection = '';
        if (trim($observation)) {
            $observationSection = '<p style="font-size:11px;font-weight:bold;text-transform:uppercase;color:#555;letter-spacing:.8px;margin:28px 0 6px;">Observaciones</p>'
                . '<p style="font-size:12px;color:#444;line-height:1.6;">' . htmlspecialchars($observation) . '</p>';
        }

        return <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Factura</title>
<style>
  * { margin:0; padding:0; box-sizing:border-box; }
  body { font-family: Arial, Helvetica, sans-serif; font-size:12px; color:#222; background:#fff; }
  .page { padding:40px 50px 30px; }

  /* HEADER */
  .hdr { display:table; width:100%; margin-bottom:4px; }
  .hdr-logo { display:table-cell; vertical-align:middle; text-align:left; }
  .hdr-title { display:table-cell; vertical-align:middle; text-align:right; }
  .hdr-title h1 { font-size:26px; font-weight:900; color:#111; }
  .hdr-title h1 span { color:#e8660a; }
  .hdr-meta { font-size:11px; color:#666; margin-top:8px; line-height:1.9; }
  .hdr-meta strong { display:inline-block; width:165px; text-align:right; padding-right:10px; color:#444; }
  .hdr-meta em { color:#111; font-style:normal; font-weight:bold; }
  .divider { border:0; border-top:2px solid #e8660a; margin:16px 0 22px; }

  /* FROM / TO */
  .parties { display:table; width:100%; margin-bottom:24px; }
  .party { display:table-cell; width:50%; vertical-align:top; }
  .party.right { text-align:right; }
  .party-lbl  { font-size:10px; color:#888; text-transform:uppercase; letter-spacing:.8px; margin-bottom:4px; }
  .party-name { font-size:15px; font-weight:bold; color:#111; margin-bottom:4px; }
  .party-detail { font-size:11px; color:#555; line-height:1.7; }

  /* ITEM TABLE */
  table.items { width:100%; border-collapse:collapse; margin-bottom:24px; }
  table.items thead tr { background:#e8660a; }
  table.items thead th { padding:9px 14px; color:#fff; font-size:11px; text-transform:uppercase; letter-spacing:.6px; font-weight:bold; text-align:left; }
  table.items thead th.c { text-align:center; }
  table.items thead th.r { text-align:right; }
  table.items tbody td { font-size:12px; color:#333; }

  /* BOTTOM */
  .bottom { display:table; width:100%; margin-top:6px; }
  .bottom-left  { display:table-cell; width:55%; vertical-align:top; padding-right:30px; }
  .bottom-right { display:table-cell; width:45%; vertical-align:top; }

  /* TOTALS */
  .totals { width:100%; border-collapse:collapse; }
  .totals td { padding:5px 10px; font-size:12px; color:#444; }
  .totals td.r { text-align:right; }
  .totals .sep td { border-top:2px solid #e8660a; font-weight:bold; color:#111; font-size:13px; padding-top:8px; }
  .totals .hl td { background:#e8660a; color:#fff; font-weight:bold; font-size:13px; padding:8px 10px; }

  /* FINANCING GRID */
  .fin-grid { width:100%; border-collapse:collapse; margin-bottom:12px; }
  .fin-grid td { padding:5px 6px; font-size:11px; color:#444; }
  .fin-grid td.lbl { color:#888; font-size:10px; text-transform:uppercase; letter-spacing:.5px; }
  .fin-grid tr:nth-child(even) { background:#f5f7fb; }

  /* SIGNATURES */
  .sigs { display:table; width:100%; margin-top:90px; }
  .sig  { display:table-cell; width:50%; text-align:center; padding:0 40px; }
  .sig-line { border-top:1px solid #333; margin-bottom:6px; }
  .sig-role { font-size:10px; color:#888; text-transform:uppercase; letter-spacing:.6px; }
  .sig-who  { font-size:12px; font-weight:bold; color:#222; margin-top:2px; }

  /* FOOTER */
  .foot { margin-top:22px; border-top:1px solid #ddd; padding-top:10px; text-align:center; font-size:10px; color:#aaa; }
</style>
</head>
<body>
<div class="page">

  <!-- HEADER -->
  <div class="hdr">
    <div class="hdr-logo">
      <img src="{$logoSrc}" style="width: 120px; margin-bottom: 4px;">
      <div style="font-size: 11px; font-weight: bold; color: #111;">RENTAL MOTORS S.A.S</div>
      <div style="font-size: 10px; color: #666;">NIT 901843215-2</div>
    </div>
    <div class="hdr-title">
      <h1>Factura</h1>
      <div class="hdr-meta">
        <strong>Número de contrato:</strong> <em>{$codigo}</em><br>
        <strong>Plan de pagos:</strong> <em>{$plan}</em><br>
        <strong>Fecha de emisión:</strong> <em>{$fecha}</em><br>
        <strong>Fecha de inicio:</strong> <em>{$startDate}</em>
      </div>
    </div>
  </div>
  <hr class="divider">

  <!-- DE / COBRAR A -->
  <div class="parties">
    <div class="party">
      <div class="party-lbl">De</div>
      <div class="party-name">Financiación Vehicular</div>
      <div class="party-detail">
        Sistema de Gestión de Financiamientos<br>
      </div>
    </div>
    <div class="party right">
      <div class="party-lbl">Cobrar a</div>
      <div class="party-name">{$nombre}</div>
      <div class="party-detail">
        DNI: $cedula<br>
        Teléfono: $telefono<br>
        Dirección: $direccion
      </div>
    </div>
  </div>

  <!-- ITEM TABLE -->
  <table class="items">
    <thead>
      <tr>
        <th>Descripción</th>
        <th class="r">Monto / Precio</th>
      </tr>
    </thead>
    <tbody>
      {$vehicleRow}
      {$servicesRows}
      {$itemRows}
    </tbody>
  </table>

  <!-- BOTTOM -->
  <div class="bottom">

    <div class="bottom-left">
      <p style="font-size:11px;font-weight:bold;text-transform:uppercase;color:#555;letter-spacing:.8px;margin-bottom:8px;">Detalles de financiamiento</p>
      <table class="fin-grid">
        <tr><td class="lbl">Tipo de operación</td><td>{$tipo}</td><td class="lbl">Estado de mora</td><td>{$moraStatus} ({$financing->mora}%)</td></tr>
        <tr><td class="lbl">Duración</td><td>{$months} meses</td><td class="lbl">Pago inicial</td><td>{$payInitial}</td></tr>
        <tr><td class="lbl">N.º de cuotas</td><td>{$installments}</td><td class="lbl">Plan seleccionado</td><td>{$plan}</td></tr>
      </table>
      {$observationSection}
    </div>

    <div class="bottom-right">
      <table class="totals">
        <tr><td>Precio del vehículo:</td><td class="r">$ {$fmt($costPriceNum)}</td></tr>
        <tr><td>Precio financiado:</td><td class="r">$ {$fmt($financingPriceNum)}</td></tr>
        <tr><td>Interés ({$interesPct}):</td><td class="r">$ {$fmt($interesPriceNum)}</td></tr>
        <tr><td>Otros Servicios:</td><td class="r">$ {$fmt($totalServicesPrice)}</td></tr>
        <tr class="sep"><td>Total a cancelar:</td><td class="r">$ {$fmt($finalPriceNum)}</td></tr>
        <tr class="hl"><td>Precio final:</td><td class="r">$ {$fmt($finalPriceNum)}</td></tr>
      </table>
    </div>

  </div>

  <!-- SIGNATURES -->
  <div class="sigs">
    <div class="sig">
      <div class="sig-line"></div>
      <div class="sig-role">Firma del responsable</div>
      <div class="sig-who">Asesor / Empresa</div>
    </div>
    <div class="sig">
      <div class="sig-line"></div>
      <div class="sig-role">Firma del cliente</div>
      <div class="sig-who">{$nombre} &mdash; {$cedula}</div>
    </div>
  </div>

  <div class="foot">
    Documento generado automáticamente · {$fecha} · Código: {$codigo}
  </div>

</div>
</body>
</html>
HTML;
    }
}