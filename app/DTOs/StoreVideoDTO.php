<?php

namespace App\DTOs;

use Illuminate\Http\Request;

class StoreVideoDTO
{
    public function __construct(
        public readonly string $title,
        public readonly int $eateryId,
        public readonly mixed $videoFile,
        public readonly ?string $videoUrl
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            title: $request->input('title'),
            eateryId: (int)$request->input('eatery_id'),
            videoFile: $request->file('video_file'),
            videoUrl: $request->input('video_url')
        );
    }
}
