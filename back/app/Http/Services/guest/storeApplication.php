<?php

namespace App\Http\Services\guest;

use App\Http\Requests\guest\ApplicationRequest;
use App\Models\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use FPDF;
use Illuminate\Support\Facades\Crypt;

class storeApplication
{
    public static function store(ApplicationRequest $request): JsonResponse
    {
        $data = $request->validated();
        $docs = [];
        $storedFiles = [];

        try {
            if (!in_array($data['type'], ['financiacion', 'contado'])) {
                throw new \InvalidArgumentException("Tipo de aplicación inválido: {$data['type']}");
            }

            $type = $data['type'] === 'financiacion' ? 'financing' : 'cash';

            foreach ($data['documents'] as $key => $file) {
                if ($file && $file instanceof \Illuminate\Http\UploadedFile) {
                    $filename = $data['selectedVehicleId'] . '_' . $data['telefono'] . '_' . time() . '_' . $key . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs("applications/{$data['selectedVehicleId']}", $filename, 'private');
                    $docs[$key] = $path;
                    $storedFiles[] = $path;
                }
            }

            $pdf = new \FPDF();
            $pdf->SetTitle('Documentos');

            foreach ($docs as $key => $path) {
                $pdf->AddPage();
                $pdf->SetFont('Arial', 'B', 14);

                $p = str_replace('_', ' ', $key);

                $p = stripos($p, 'Pep') ? 'P.P.T' : $p;

                $pdf->Cell(0, 10, ucfirst($p), 0, 1, 'C');

                $imagePath = Storage::disk('private')->path($path);

                list($width, $height) = getimagesize($imagePath);
                $maxWidth = 190;
                $maxHeight = 250;

                $ratio = min($maxWidth / $width, $maxHeight / $height);
                $newWidth = $width * $ratio;
                $newHeight = $height * $ratio;
                $x = (210 - $newWidth) / 2;
                $y = 30;

                $pdf->Image($imagePath, $x, $y, $newWidth, $newHeight);
            }

            $uuid = $data['selectedVehicleId'] . '_' . $data['telefono'] . '_' . time();

            $pdfName = 'file.pdf';
            $pdfPath = "applications/{$data['selectedVehicleId']}/$uuid/" . $pdfName;
            $pdfFullPath = Storage::disk('private')->path($pdfPath);

            Storage::disk('private')->makeDirectory("applications/{$data['selectedVehicleId']}/$uuid");

            $pdf->Output($pdfFullPath, 'F');


            $pathHash = Crypt::encryptString($pdfPath);

            Application::create([
                'full_name'    => $data['fullName'],
                'type'         => $type,
                'plan'         => $data['cuotas'],
                'cedula'       => $data['tipoDocumento'] . '-' . $data['identity'],
                'phone'        => $data['telefono'],
                'direccion'        => $data['direccion'],
                'vehicle_id'   => $data['selectedVehicleId'],
                'document_pdf' => $pathHash,
                'status'       => 'pending',
                'folder'       => $uuid,
            ]);

            foreach ($storedFiles as $filePath) {
                Storage::disk('private')->delete($filePath);
            }

            return response()->json([
                'message' => 'Application submitted successfully'
            ], 201);

        } catch (\Exception $e) {

            foreach ($storedFiles as $filePath) {
                Storage::disk('private')->delete($filePath);
            }

            return response()->json([
                "message" => "Error saving application",
                "error"   => $e->getMessage()
            ], 500);
        }
    }
}