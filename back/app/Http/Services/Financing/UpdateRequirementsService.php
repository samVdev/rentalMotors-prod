<?php

namespace App\Http\Services\Financing;

use App\Models\Financing;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use FPDF;

class UpdateRequirementsService
{
    public static function execute(Request $request, int $id): JsonResponse
    {
        try {
            $application = Application::findOrFail($id);

            if (!$application) {
                return response()->json(['message' => 'Solicitud no encontrada para esta financiación.'], 404);
            }

            $documents = $request->file('documents');
            if (!$documents || !is_array($documents)) {
                return response()->json(['message' => 'No se subieron documentos válidos.'], 400);
            }

            $storedFiles = [];
            $docs = [];

            foreach ($documents as $key => $file) {
                if ($file && $file instanceof \Illuminate\Http\UploadedFile) {
                    $filename = $application->vehicle_id . '_' . $application->phone . '_' . time() . '_' . $key . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs("applications/{$application->vehicle_id}", $filename, 'private');
                    $docs[$key] = $path;
                    $storedFiles[] = $path;
                }
            }

            if (empty($docs)) {
                return response()->json(['message' => 'Debe subir al menos un documento.'], 400);
            }

            // PDF Generation
            $pdf = new \FPDF();
            $pdf->SetTitle('Documentos Actualizados');

            foreach ($docs as $key => $path) {
                $pdf->AddPage();
                $pdf->SetFont('Arial', 'B', 14);

                $label = str_replace('_', ' ', $key);
                $label = stripos($label, 'Pep') !== false ? 'P.P.T' : $label;

                $pdf->Cell(0, 10, ucfirst($label), 0, 1, 'C');

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

            $uuid = $application->folder ?? ($application->vehicle_id . '_' . $application->phone . '_' . time());
            $pdfName = 'file_updated_' . time() . '.pdf';
            $pdfPath = "applications/{$application->vehicle_id}/$uuid/" . $pdfName;
            $pdfFullPath = Storage::disk('private')->path($pdfPath);

            Storage::disk('private')->makeDirectory("applications/{$application->vehicle_id}/$uuid");
            $pdf->Output($pdfFullPath, 'F');

            // Delete old PDF if exists
            if ($application->document_pdf) {
                try {
                    $oldPath = Crypt::decryptString($application->document_pdf);
                    if (Storage::disk('private')->exists($oldPath)) {
                        Storage::disk('private')->delete($oldPath);
                    }
                } catch (\Exception $e) {
                    // Ignore if decryption or deletion fails
                }
            }

            // Update Application
            $pathHash = Crypt::encryptString($pdfPath);
            $application->update([
                'document_pdf' => $pathHash,
                'folder' => $uuid,
            ]);

            // Cleanup temporary images
            foreach ($storedFiles as $filePath) {
                Storage::disk('private')->delete($filePath);
            }

            return response()->json([
                'message' => 'Recaudos actualizados correctamente.',
                'document' => $pdfPath
            ], 200);

        } catch (\Exception $e) {
            // Cleanup on error
            if (isset($storedFiles)) {
                foreach ($storedFiles as $filePath) {
                    Storage::disk('private')->delete($filePath);
                }
            }

            return response()->json([
                'message' => 'Error al actualizar recaudos.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
