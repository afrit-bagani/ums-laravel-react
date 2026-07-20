<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class AiDocumentValidator
{
    /**
     * Validates a passport photo against the Python AI microservice.
     * Throws a ValidationException if the AI rejects it.
     */
    public function validatePassport(UploadedFile $photo): void
    {
        $response = Http::attach(
            'file',
            file_get_contents($photo->getPathname()),
            $photo->getClientOriginalName()
        )->post(env('AI_MICROSERVICE_URL').'/api/verify-passport');

        if ($response->failed() || ! $response->json('is_valid')) {
            $errorMessage = $response->json('message') ?? 'AI verification failed for the photo.';

            // We use the strict array format for the exception messages
            throw ValidationException::withMessages([
                'photo' => [$errorMessage],
            ]);
        }
    }

    /**
     * Validates a signature against the Python AI microservice.
     * Throws a ValidationException if the AI rejects it.
     */
    public function validateSignature(UploadedFile $signature): void
    {
        $response = Http::attach(
            'file',
            file_get_contents($signature->getPathname()),
            $signature->getClientOriginalName()
        )->post(env('AI_MICROSERVICE_URL').'/api/verify-signature');

        if ($response->failed() || ! $response->json('is_valid')) {
            $errorMessage = $response->json('message') ?? 'AI verification failed for the signature.';

            throw ValidationException::withMessages([
                'signature' => [$errorMessage],
            ]);
        }
    }
}
