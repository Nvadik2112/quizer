<?php

namespace App\Tests;

use App\Exceptions\Domain\BadRequestException;
use App\Tests\Dto\CreateTestDto;
use App\Tests\Dto\UpdateTestDto;
use Exception;
use PDO;

class TestsService
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @throws BadRequestException
     */
    public function create($data, $userId): TestsEntity
    {
        $dto = CreateTestDto::fromArray($data);

        $sql = "INSERT INTO tests (title, created_at, updated_at, user_id)
        VALUES (:title, NOW(), NOW(), :user_id)";

        $stmt = $this->connection->prepare($sql);

        $stmt->execute([
            'title' => $dto->title,
            'user_id' => $userId,
        ]);

        $testId = (int)$this->connection->lastInsertId();

        if ($testId === 0) {
            throw new BadRequestException('Не удалось создать тест');
        }

        $test = $this->findById($testId);

        // ВАЖНО: установите userId в объект
        $test->setUserId($userId);

        return $test;
    }

    /**
     * @throws BadRequestException
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

    /**
     * @throws Exception
     */
    public function update(int $testId, $dto): TestsEntity {
        $dto = UpdateTestDto::fromArray($dto);

        if (!$dto->hasChanges()) {
            throw new \InvalidArgumentException('Нет данных для обновления', 400);
        }

        $updateData = [
            'title' => $dto->title,
        ];

        $setFields = array_map(fn($key) => "{$key} = :{$key}", array_keys($updateData));
        $setFields[] = "updated_at = NOW()";
        $setClause = implode(', ', $setFields);

        $sql = "UPDATE tests SET {$setClause} WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(array_merge($updateData, ['id' => $testId]));

        return $this->findById($testId);
    }

    /**
     * @throws BadRequestException
     * @throws Exception
     */
    public function getTests(): array
    {
        $sql = "SELECT * FROM tests";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$data) {
            throw new BadRequestException("Тесты не найдены");
        }

        $tests = [];

        foreach ($data as $test) {
            $tests[] = TestsEntity::fromArray($test);
        }

        return $tests;
    }

    /**
     * @throws Exception
     */
    public function delete(int $testId): TestsEntity
    {
        $test = $this->findById($testId);

        $sql = "DELETE FROM tests WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['id' => $testId]);

        return $test;
    }

    /**
     * @throws BadRequestException
     */
    public function getTestUserId(int $testId)
    {
        $test = $this->findById($testId);

        return $test['user_id'];
    }
}