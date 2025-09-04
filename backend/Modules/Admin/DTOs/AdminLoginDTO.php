<?php

namespace Modules\Admin\DTOs;

class AdminLoginDTO
{
    public function __construct(
        public readonly string $email,
        public readonly string $password,
        public readonly bool $remember = false,
        public readonly ?string $ip = null,
        public readonly ?string $userAgent = null
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            email: $data['email'],
            password: $data['password'],
            remember: $data['remember'] ?? false,
            ip: request()->ip(),
            userAgent: request()->userAgent()
        );
    }

    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'password' => $this->password,
            'remember' => $this->remember,
            'ip' => $this->ip,
            'user_agent' => $this->userAgent
        ];
    }
}