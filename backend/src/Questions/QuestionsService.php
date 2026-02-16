<?php

namespace App\Questions;

use App\Exceptions\Domain\BadRequestException;
use App\Exceptions\Domain\NotFoundException;
use App\Questions\Dto\CreateQuestionDto;
use App\Questions\Dto\UpdateQuestionDto;

use Exception;
use PDO;

class QuestionsService
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @throws NotFoundException
     * @throws BadRequestException
     */
    public function create($data): QuestionsEntity
    {
        $dto = CreateQuestionDto::fromArray($data);

        $dto->validate();

        $answersArray = '{' . implode(',', array_map(function($answer) {
                $answer = str_replace('"', '\\"', $answer);
                $answer = str_replace('\\', '\\\\', $answer);
                return '"' . $answer . '"';
            }, $dto->answers)) . '}';

        $sql = "INSERT INTO questions (title, answers, correct_answer_index, created_at, updated_at)
            VALUES (:title, :answers::text[], :correct_answer_index, NOW(), NOW())";

        $stmt = $this->connection->prepare($sql);

        $stmt->execute([
            'title' => $dto->title,
            'answers' => $answersArray,
            'correct_answer_index' => $dto->correctAnswerIndex,
        ]);

        $questionId = (int)$this->connection->lastInsertId();

        if ($questionId === 0) {
            throw new BadRequestException('Не удалось создать вопрос');
        }

        return $this->findById($questionId);
    }

    /**
     * @throws NotFoundException
     * @throws Exception
     */
    public function findById(int $id): QuestionsEntity
    {
        $sql = "SELECT * FROM questions WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['id' => $id]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            throw new NotFoundException("Вопрос ${$id} не найден");
        }

        return QuestionsEntity::fromArray($data);
    }

    /**
     * @throws NotFoundException
     */
    public function update(int $questionId, $data): QuestionsEntity
    {
        $dto = UpdateQuestionDto::fromArray($data);

        if (!$dto->hasChanges()) {
            throw new \InvalidArgumentException('Нет данных для обновления', 400);
        }

        $updateData = array_filter([
            'title' => $dto->title,
            'answers' => $dto->answers,
            'correctAnswerIndex' => $dto->correctAnswerIndex,
        ], fn($value) => $value !== null);

        $setFields = array_map(fn($key) => "{$key} = :{$key}", array_keys($updateData));
        $setFields[] = "updated_at = NOW()";
        $setClause = implode(', ', $setFields);

        $sql = "UPDATE questions SET {$setClause} WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(array_merge($updateData, ['id' => $questionId]));

        return $this->findById($questionId);
    }

    /**
     * @throws NotFoundException
     */
    public function delete(int $questionId): QuestionsEntity
    {
        $question = $this->findById($questionId);

        $sql = "DELETE FROM questions WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['id' => $questionId]);

        return $question;
    }

    /**
     * @throws NotFoundException
     */
    public function checkAnswerIndex(int $questionId, int $index): bool
    {
        $question = $this->findById($questionId);
        QuestionsEntity::validateAnswerIndex($index);

        if ($question['correctAnswerIndex'] === $index) {
            return true;
        }

        return false;
    }
}