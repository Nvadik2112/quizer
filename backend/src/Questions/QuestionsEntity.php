<?php

namespace App\Questions;

use App\Exceptions\Domain\BadRequestException;
use DateTime;
use Exception;

class QuestionsEntity
{
    private ?int $id = null;
    private DateTime $createdAt;
    private DateTime $updatedAt;
    private string $title;
    private array $answers;
    private int $correctAnswerIndex;

    private int $testId;

    private int $position;

    /**
     * @throws BadRequestException
     */
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

    /**
     * @throws BadRequestException
     */
    public static function validateTitle(string $title): void
    {
        if (strlen($title) < 2 || strlen($title) > 150) {
            throw new BadRequestException('Title must be between 2 and 150 characters');
        }
    }

    /**
     * @throws BadRequestException
     */
    public function setTitle(string $title): void
    {
        self::validateTitle($title);
        $this->title = $title;
    }

    /**
     * @throws BadRequestException
     */
    public static function validateAnswer(string $answer): void {
        if (strlen($answer) < 1 || strlen($answer) > 20) {
           throw new BadRequestException('Answer must be between 1 and 20 characters');
        }
    }

    /**
     * @throws BadRequestException
     */
    public static function validateAnswers(array $answers): void {
        if (count($answers) !== 4) {
            throw new BadRequestException('There must be exactly 4 answers');
        }
    }

    /**
     * @throws BadRequestException
     */
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

    /**
     * @throws BadRequestException
     */
    public static function validateAnswerIndex(int $index): void {
        if (!is_numeric($index)) {
            throw new BadRequestException('correctAnswerIndex must be a valid integer');
        }

        if ($index < 0 || $index > 3) {
            throw new BadRequestException('correctAnswerIndex must be between 0 and 3');
        }
    }

    /**
     * @throws BadRequestException
     */
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

    /**
     * @throws BadRequestException
     */
    static function validateTestId(int $testId): void
    {
        if (!is_numeric($testId)) {
            throw new BadRequestException('testId must be a valid integer');
        }
    }

    /**
     * @throws BadRequestException
     */
    public function setTestId(int $testId): void
    {
        self::validateTestId($testId);
        $this->testId = $testId;
    }

    /**
     * @throws BadRequestException
     */
    static function validatePosition(int $position): void
    {
        if (!is_numeric($position)) {
            throw new BadRequestException('position must be a valid integer');
        }
    }

    /**
     * @throws BadRequestException
     */
    public function setPosition(int $position): void
    {
        self::validatePosition($position);
        $this->position = $position;
    }

    private function setCreatedAt(): void {
        $this->createdAt = new DateTime();
    }

    private function setUpdatedAt(): void {
        $this->updatedAt = new DateTime();
    }

    /**
     * @throws BadRequestException
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
            $question->createdAt = new DateTime($data['created_at']);
        }

        if (isset($data['updated_at']) && is_string($data['updated_at'])) {
            $question->updatedAt = new DateTime($data['updated_at']);
        }

        return $question;
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'answers' => $this->answers,
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