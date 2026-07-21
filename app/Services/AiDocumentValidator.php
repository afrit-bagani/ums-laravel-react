<?php

namespace App\Services;

use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;

class AiDocumentValidator
{
    /**
     * Validates a passport photo against the Python AI microservice.
     * Returns an array with validation status, errors, and document hash.
     */
    public function validatePassport(UploadedFile $photo): array
    {
        try {
            $response = Http::attach(
                'file',
                file_get_contents($photo->getPathname()),
                $photo->getClientOriginalName()
            )->post(env('AI_MICROSERVICE_URL').'/api/verify-passport');

            if ($response->successful()) {
                return [
                    'is_valid' => $response->json('is_valid'),
                    'errors' => $response->json('errors'),
                    'hash' => $response->json('document_hash'),
                ];
            }

            return [
                'is_valid' => false,
                'errors' => ['AI verification service returned an error.'],
                'hash' => null,
            ];
        } catch (Exception $e) {
            return [
                'is_valid' => false,
                'errors' => ['Failed to connect to the AI verification service.'],
                'hash' => null,
            ];
        }
    }

    /**
     * Validates a signature against the Python AI microservice.
     * Returns an array with validation status, errors, and document hash.
     */
    public function validateSignature(UploadedFile $signature): array
    {
        try {
            $response = Http::attach(
                'file',
                file_get_contents($signature->getPathname()),
                $signature->getClientOriginalName()
            )->post(env('AI_MICROSERVICE_URL').'/api/verify-signature');

            if ($response->successful()) {
                return [
                    'is_valid' => $response->json('is_valid'),
                    'errors' => $response->json('errors'),
                    'hash' => $response->json('document_hash'),
                ];
            }

            return [
                'is_valid' => false,
                'errors' => ['AI verification service returned an error.'],
                'hash' => null,
            ];
        } catch (Exception $e) {
            return [
                'is_valid' => false,
                'errors' => ['Failed to connect to the AI verification service.'],
                'hash' => null,
            ];
        }
    }
}
