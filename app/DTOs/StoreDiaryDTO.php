<?php

namespace App\DTOs;

use Illuminate\Http\Request;

class StoreDiaryDTO
{
    public function __construct(
        public readonly ?int $rating,
        public readonly ?string $comment,
        public readonly ?string $image,
        public readonly array $completedStops,
        public readonly array $stopReviews,
        public readonly bool $shareToCommunity
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            rating: $request->input('rating') ? (int)$request->input('rating') : null,
            comment: $request->input('comment'),
            image: $request->input('image'),
            completedStops: $request->input('completed_stops', []),
            stopReviews: $request->input('stop_reviews', []),
            shareToCommunity: $request->boolean('share_to_community')
        );
    }
}
