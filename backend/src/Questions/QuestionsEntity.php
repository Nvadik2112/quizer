<?php

namespace App\Questions;

use Exception;
use InvalidArgumentException;

class QuestionsEntity
{
    private ?int $id = null;
    private \DateTime $createdAt;
    private \DateTime $updatedAt;
    private string $title;
    private array $answers;
    private int $correctAnswerIndex;

    private int $testId;

    private int $position;

    public function __construct(
        string $title,
        array $answers,
        int $position,
        int $testId,
        int $correctAnswerIndex
    ) {
        $this->setTitle($title);
        $this->setAnswers($answers);
        $this->setCorrectAnswerIndex($correctAnswerIndex);
        $this->setTestId($testId);
        $this->setPosition($position);
        $this->setCreatedAt();
        $this->setUpdatedAt();
    }

    public static function validateTitle(string $title): void
    {
        if (strlen($title) < 2 || strlen($title) > 150) {
            throw new InvalidArgumentException('Title must be between 2 and 150 characters');
        }
    }

    public function setTitle(string $title): void
    {
        self::validateTitle($title);
        $this->title = $title;
    }

     public static function validateAnswer(string $answer): void {
        if (strlen($answer) < 1 || strlen($answer) > 20) {
           throw new \InvalidArgumentException('Answer must be between 1 and 20 characters');
        }
    }

    public static function validateAnswers(array $answers): void {
        if (count($answers) !== 4) {
            throw new \InvalidArgumentException('There must be exactly 4 answers');
        }
    }

    private function setAnswers(array $answers): void {
        self::validateAnswers($answers);

        foreach ($answers as $answer) {
            self::validateAnswer($answer);
        }

        $this->answers = $answers;
    }

     public function getAnswers(): array {
        return $this->answers;
    }

    public static function validateAnswerIndex(int $index): void {
        if (!is_numeric($index)) {
            throw new InvalidArgumentException('correctAnswerIndex must be a valid integer');
        }

        if ($index < 0 || $index > 3) {
            throw new InvalidArgumentException('correctAnswerIndex must be between 0 and 3');
        }
    }

    private function setCorrectAnswerIndex(int $index): void {
        QuestionsEntity::validateAnswerIndex($index);
        $this->correctAnswerIndex = $index;
    }

   public function getCorrectAnswerIndex(): int {
        return $this->correctAnswerIndex;
   }

    public function getId(): ?int {
        return $this->id;
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

    static function validateTestId(int $testId): void
    {
        if (!is_numeric($testId)) {
            throw new InvalidArgumentException('testId must be a valid integer');
        }
    }

    public function setTestId(int $testId): void
    {
        self::validateTestId($testId);
        $this->testId = $testId;
    }

    static function validatePosition(int $position): void
    {
        if (!is_numeric($position)) {
            throw new InvalidArgumentException('position must be a valid integer');
        }
    }

    public function setPosition(int $position): void
    {
        self::validatePosition($position);
        $this->position = $position;
    }

    private function setCreatedAt(): void {
        $this->createdAt = new \DateTime();
    }

    private function setUpdatedAt(): void {
        $this->updatedAt = new \DateTime();
    }

    /**
     * @throws Exception
     */
    public static function fromArray(array $data): self {
        $answers = $data['answers'];

        if (is_string($answers)) {
            $answersString = trim($answers, '{}');
            $answers = str_getcsv($answersString, ',', '"');
            $answers = array_map(function($item) {
                return stripslashes($item);
            }, $answers);
        }

        $question = new self(
            $data['title'],
            $answers,
            $data['position'],
            $data['test_id'],
            $data['correct_answer_index']
        );

        if (isset($data['id'])) {
            $question->setId((int)$data['id']);
        }

        if (isset($data['created_at']) && is_string($data['created_at'])) {
            $question->createdAt = new \DateTime($data['created_at']);
        }

        if (isset($data['updated_at']) && is_string($data['updated_at'])) {
            $question->updatedAt = new \DateTime($data['updated_at']);
        }

        return $question;
    }

    public static function validateAll(array $data): void
    {
        self::validateTestId($data['test_id']);
        self::validateTitle($data['title']);
        self::validateAnswers($data['answers']);
        self::validatePosition($data['position']);

        foreach ($data['answers'] ?? [] as $answer) {
            self::validateAnswer($answer);
        }

        self::validateAnswerIndex(
            $data['correct_answer_index'],
        );
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'answers' => $this->answers,
            'correctAnswerIndex' => $this->correctAnswerIndex,
            'position' => $this->position,
        ];
    }

    public static function answersToString(array | null $answers): ?string
    {
        if ($answers === null) {
            return null;
        }

        if (empty($answers)) {
            return '{}';
        }

        return  '{' . implode(',', array_map(function($answer) {
            $answer = str_replace('"', '\\"', $answer);
            $answer = str_replace('\\', '\\\\', $answer);

            return '"' . $answer . '"';
            }, $answers)) . '}';

    }
}