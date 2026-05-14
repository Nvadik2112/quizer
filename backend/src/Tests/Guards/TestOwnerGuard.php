<?php

namespace App\Tests\Guards;

use App\Exceptions\Domain\BadRequestException;
use App\Exceptions\Domain\ForbiddenException;
use PDO;

class TestOwnerGuard
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Проверяет владельца теста
     * @throws BadRequestException
     * @throws ForbiddenException
     */
    public function validateTest(int $userId, int $testId): void
    {
        $sql = "SELECT user_id FROM tests WHERE id = :test_id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['test_id' => $testId]);

        $ownerId = $stmt->fetchColumn();

        if ($ownerId === false) {
            throw new BadRequestException("Test with id {$testId} not found");
        }

        if ($userId !== (int)$ownerId) {
            throw new ForbiddenException('You do not have permission to modify this test');
        }
    }

    /**
     * @throws ForbiddenException
     * @throws BadRequestException
     */
    public function validateQuestion(int $userId, int $questionId): void
    {
        $sql = "
            SELECT t.user_id 
            FROM questions q
            JOIN tests t ON t.id = q.test_id
            WHERE q.id = :question_id
        ";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['question_id' => $questionId]);

        $ownerId = $stmt->fetchColumn();

        if ($ownerId === false) {
            throw new BadRequestException("Question with id {$questionId} not found");
        }

        if ($userId !== (int)$ownerId) {
            throw new ForbiddenException('You do not have permission to modify this question');
        }
    }
}