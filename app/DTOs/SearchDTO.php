<?php

namespace App\DTOs;

use Illuminate\Http\Request;

class SearchDTO
{
    public function __construct(
        public readonly ?string $keyword,
        public readonly ?int $categoryId,
        public readonly ?int $communeId,
        public readonly ?string $ajax,
        public readonly bool $expectsJson
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            keyword: $request->query('q'),
            categoryId: $request->query('category_id') ? (int)$request->query('category_id') : null,
            communeId: $request->query('commune_id') ? (int)$request->query('commune_id') : null,
            ajax: $request->query('ajax'),
            expectsJson: $request->expectsJson() || $request->query('json') === '1'
        );
    }
}
