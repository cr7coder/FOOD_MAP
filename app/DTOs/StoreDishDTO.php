<?php

namespace App\DTOs;

use Illuminate\Http\Request;

class StoreDishDTO
{
    public function __construct(
        public readonly int $eateryId,
        public readonly string $name,
        public readonly float $price,
        public readonly ?string $description,
        public readonly mixed $imageFile,
        public readonly ?string $imageUrl,
        public readonly bool $isSignature
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            eateryId: (int)$request->input('eatery_id'),
            name: $request->input('dish_name'),
            price: (float)$request->input('dish_price'),
            description: $request->input('dish_description'),
            imageFile: $request->file('dish_image'),
            imageUrl: $request->input('dish_image_url'),
            isSignature: $request->boolean('is_signature')
        );
    }
}
