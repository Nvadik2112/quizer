<?php

namespace App\Tests\Dto;

use App\Tests\TestsEntity;

class CreateTestDto
{
    public function __construct(
        public string $title,
    ){
        $this->validate();
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['title'] ?? '',
        );
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
        ];
    }

    public function validate(): void
    {
        TestsEntity::validateTitle($this->title);
    }
}