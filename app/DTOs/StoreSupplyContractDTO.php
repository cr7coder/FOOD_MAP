<?php

namespace App\DTOs;

use Illuminate\Http\Request;

class StoreSupplyContractDTO
{
    public function __construct(
        public readonly int $eateryId,
        public readonly string $supplierName,
        public readonly string $itemsSupplied,
        public readonly string $signedAt,
        public readonly string $expiredAt,
        public readonly mixed $imageFile,
        public readonly ?string $imageUrl
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            eateryId: (int)$request->input('eatery_id'),
            supplierName: $request->input('supplier_name'),
            itemsSupplied: $request->input('items_supplied'),
            signedAt: $request->input('signed_at'),
            expiredAt: $request->input('expired_at'),
            imageFile: $request->file('image'),
            imageUrl: $request->input('image_url')
        );
    }
}
