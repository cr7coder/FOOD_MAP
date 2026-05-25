<?php

namespace App\DTOs;

use Illuminate\Http\Request;

class StoreCertificateDTO
{
    public function __construct(
        public readonly int $eateryId,
        public readonly string $certificateNumber,
        public readonly string $issuedBy,
        public readonly string $issuedAt,
        public readonly string $expiredAt,
        public readonly mixed $imageFile,
        public readonly ?string $imageUrl
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            eateryId: (int)$request->input('eatery_id'),
            certificateNumber: $request->input('certificate_number'),
            issuedBy: $request->input('issued_by'),
            issuedAt: $request->input('issued_at'),
            expiredAt: $request->input('expired_at'),
            imageFile: $request->file('image'),
            imageUrl: $request->input('image_url')
        );
    }
}
