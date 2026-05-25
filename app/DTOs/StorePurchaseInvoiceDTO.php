<?php

namespace App\DTOs;

use Illuminate\Http\Request;

class StorePurchaseInvoiceDTO
{
    public function __construct(
        public readonly int $eateryId,
        public readonly string $supplierName,
        public readonly string $itemsSummary,
        public readonly string $invoiceDate,
        public readonly mixed $imageFile,
        public readonly ?string $imageUrl
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            eateryId: (int)$request->input('eatery_id'),
            supplierName: $request->input('supplier_name'),
            itemsSummary: $request->input('items_summary'),
            invoiceDate: $request->input('invoice_date'),
            imageFile: $request->file('image'),
            imageUrl: $request->input('image_url')
        );
    }
}
