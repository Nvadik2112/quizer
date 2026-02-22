<?php

namespace App\Tests;

use App\Exceptions\Domain\BadRequestException;
use App\Exceptions\Domain\NotFoundException;
use App\Tests\Dto\CreateTestDto;
use Exception;
use PDO;

class TestsService
{
    private PDO $connection;

    private function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function create($data, $userId): TestsEntity
    {
        $dto = CreateTestDto::fromArray($data);

        $sql = "INSERT INTO tests (title, created_at, updated_at, user_id)
        VALUES (:title, :created_at, NOW(), NOW())";

        $stmt = $this->connection->prepare($sql);

        $stmt->execute([
            'title' => $dto->title,
            'user_id' => $userId,
        ]);

        $testId = (int)$this->connection->lastInsertId();

        if ($testId === 0) {
            throw new BadRequestException('Не удалось создать тест');
        }

        return $this->findById($testId);
    }

    /**
     * @throws Exception
     */
    public function findById(int $id): TestsEntity
    {
        $sql = "SELECT * FROM tests WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['id' => $id]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            throw new BadRequestException("Тест id=${id} не найден");
        }

        return TestsEntity::fromArray($data);
    }
}