<?php

namespace App\DTOs;

use Illuminate\Http\Request;

class StoreReviewDTO
{
    public function __construct(
        public readonly string $userName,
        public readonly ?int $rating,
        public readonly ?string $comment,
        public readonly array $mediaFiles
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            userName: $request->input('user_name'),
            rating: $request->input('rating') ? (int)$request->input('rating') : null,
            comment: $request->input('comment'),
            mediaFiles: $request->file('media') ?: []
        );
    }
}
