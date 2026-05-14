<?php

namespace App\Tests;

use Exception;
use InvalidArgumentException;

class TestsEntity
{
    private ?int $id = null;
    private \DateTime $createdAt;
    private \DateTime $updatedAt;
    private string $title;
    private ?int $userId = null;

    public function __construct(
        string $title,
    )
    {
        $this->setTitle($title);
        $this->setCreatedAt();
        $this->setUpdatedAt();
    }

    public static function validateTitle(string $title): void
    {
        if (strlen($title) < 2 || strlen($title) > 30) {
            throw new InvalidArgumentException('Title must be between 2 and 30 characters');
        }
    }

    public function setTitle(string $title): void
    {
        self::validateTitle($title);
        $this->title = $title;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    private function setCreatedAt(): void
    {
        $this->createdAt = new \DateTime();
    }

    private function setUpdatedAt(): void
    {
        $this->updatedAt = new \DateTime();
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * @throws Exception
     */
    public static function fromArray(array $data): self
    {
        $test = new self(
            $data['title'] ?? '',
        );

        if (isset($data['id'])) {
            $test->setId($data['id']);
        }

        if (isset($data['user_id'])) {
            $test->setUserId($data['user_id']);
        }

        if (isset($data['created_at']) && is_string($data['created_at'])) {
            $test->createdAt = new \DateTime($data['created_at']);
        }

        if (isset($data['updated_at']) && is_string($data['updated_at'])) {
            $test->updatedAt = new \DateTime($data['updated_at']);
        }

        return $test;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
        ];
    }
}
