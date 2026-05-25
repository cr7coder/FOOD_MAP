<?php

namespace App\DTOs;

use Illuminate\Http\Request;

class GenerateAITourDTO
{
    public function __construct(
        public readonly int $budgetLimit,
        public readonly string $mood
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            budgetLimit: (int)$request->input('budget', 300000),
            mood: $request->input('mood', 'chill')
        );
    }
}
