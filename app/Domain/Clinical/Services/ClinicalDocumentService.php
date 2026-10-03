<?php

namespace App\Domain\Clinical\Services;

use App\Domain\Clinical\Enums\DocumentType;
use App\Models\ClinicalDocument;
use App\Models\ClinicalRecord;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ClinicalDocumentService
{
    public function __construct(
        protected ClinicalAuditService $auditService
    ) {}

    /**
     * Store a clinical document in private medical records storage.
     */
    public function uploadDocument(
        ClinicalRecord $record,
        UploadedFile $file,
        string $documentType,
        ?int $encounterId,
        ?string $description,
        bool $isSensitive,
        int $userId
    ): ClinicalDocument {
        // Validate MIME type
        $allowedMimes = [
            'application/pdf',
            'image/jpeg',
            'image/png',
            'image/webp',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/zip',
        ];

        if (!in_array($file->getMimeType(), $allowedMimes, true)) {
            throw new \InvalidArgumentException("El formato del archivo ({$file->getClientOriginalExtension()}) no está permitido para documentos clínicos.");
        }

        $folder = "patient_{$record->patient_id}";
        $path = $file->store($folder, 'medical_records');

        return DB::transaction(function () use ($record, $file, $path, $documentType, $encounterId, $description, $isSensitive, $userId) {
            $document = $record->documents()->create([
                'encounter_id' => $encounterId,
                'document_type' => $documentType,
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'description' => $description,
                'is_sensitive' => $isSensitive,
                'uploaded_by' => $userId,
            ]);

            $this->auditService->log('DocumentUploaded', $document, [
                'document_id' => $document->id,
                'patient_id' => $record->patient_id,
                'document_type' => $documentType,
                'file_name' => $document->file_name,
            ]);

            return $document;
        });
    }

    /**
     * Download or stream a clinical document with audit logging.
     */
    public function streamDownload(ClinicalDocument $document): StreamedResponse
    {
        $disk = Storage::disk('medical_records');
        if (!$disk->exists($document->file_path)) {
            abort(404, "El archivo clínico solicitado no se encuentra en el almacenamiento.");
        }

        $this->auditService->log('DocumentDownloaded', $document, [
            'document_id' => $document->id,
            'file_name' => $document->file_name,
        ]);

        return $disk->download($document->file_path, $document->file_name);
    }

    /**
     * Soft delete document.
     */
    public function deleteDocument(ClinicalDocument $document): void
    {
        $document->delete();

        $this->auditService->log('DocumentDeleted', $document, [
            'document_id' => $document->id,
        ]);
    }
}
