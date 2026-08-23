<?php

namespace App\Users\Dto;

use App\Users\Entities\UserEntity;

class UpdateUserDto {
    public function __construct(
        public ?string $email = null,
        public ?string $password = null,
    ) {
        if ($this->email !== null) UserEntity::validateEmail($this->email);
        if ($this->password !== null) UserEntity::validatePassword($this->password);
    }

    public static function fromArray(array $data): self {
        return new self(
            $data['email'] ?? null,
            $data['password'] ?? null,
        );
    }

    public function hasChanges(): bool {
        return $this->email !== null || $this->password !== null;
    }
}