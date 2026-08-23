<?php

namespace App\Users\Dto;

use App\Users\Entities\UserEntity;

class CreateUserDto 
{
    public function __construct(
        public string $email,
        public string $password,
    ) {
        $this->validate();
    }

    public static function fromArray(array $data): self 
    {
        return new self(
            $data['email'] ?? '',
            $data['password'] ?? '',
        );
    }

    private function validate(): void
    {
        UserEntity::validateEmail($this->email);
        UserEntity::validatePassword($this->password);
    }

    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'password' => $this->password,
        ];
    }

    public function getUserData(): array
    {
        return [
            'email' => $this->email,
        ];
    }
}