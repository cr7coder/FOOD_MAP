<?php

namespace App\DTOs;

use Illuminate\Http\Request;

class StoreUserDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $password,
        public readonly string $role,
        public readonly ?string $phone,
        public readonly ?string $avatar,
        public readonly ?string $status
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            name: $request->input('name'),
            email: $request->input('email'),
            password: $request->input('password'),
            role: $request->input('role'),
            phone: $request->input('phone'),
            avatar: $request->input('avatar'),
            status: $request->input('status', 'active')
        );
    }
}
