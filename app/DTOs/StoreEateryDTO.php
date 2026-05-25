<?php

namespace App\DTOs;

use Illuminate\Http\Request;

class StoreEateryDTO
{
    public function __construct(
        public readonly string $name,
        public readonly int $categoryId,
        public readonly int $communeId,
        public readonly string $address,
        public readonly ?string $phone,
        public readonly ?string $openingHours,
        public readonly float $latitude,
        public readonly float $longitude,
        public readonly ?string $priceRange,
        public readonly mixed $imageFile,
        public readonly ?string $imageUrl,
        public readonly ?string $description,
        public readonly bool $isFeatured
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            name: $request->input('name'),
            categoryId: (int)$request->input('category_id'),
            communeId: (int)$request->input('commune_id'),
            address: $request->input('address'),
            phone: $request->input('phone'),
            openingHours: $request->input('opening_hours'),
            latitude: (float)$request->input('latitude'),
            longitude: (float)$request->input('longitude'),
            priceRange: $request->input('price_range'),
            imageFile: $request->file('image'),
            imageUrl: $request->input('image_url'),
            description: $request->input('description'),
            isFeatured: $request->boolean('is_featured')
        );
    }
}
