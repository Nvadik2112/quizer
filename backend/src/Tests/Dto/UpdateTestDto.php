<?php

namespace App\Tests\Dto;

use App\Questions\QuestionsEntity;
use App\Tests\TestsEntity;

class UpdateTestDto {
    public function __construct(
        public ?string $title,
    )
    {
        if ($this->title !== null) {
            TestsEntity::validateTitle($this->title);
        }
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['title'] ?? null,
        );
    }

    public function hasChanges(): bool
    {
        return $this->title !== null;
    }
}