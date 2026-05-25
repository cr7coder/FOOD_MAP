<?php

namespace App\DTOs;

use Illuminate\Http\Request;

class StoreDailyFoodLogDTO
{
    public function __construct(
        public readonly int $eateryId,
        public readonly string $logDate,
        public readonly string $ingredientsOrigin,
        public readonly string $storageCondition,
        public readonly string $checkerName
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            eateryId: (int)$request->input('eatery_id'),
            logDate: $request->input('log_date'),
            ingredientsOrigin: $request->input('ingredients_origin'),
            storageCondition: $request->input('storage_condition'),
            checkerName: $request->input('checker_name')
        );
    }
}
