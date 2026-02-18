<?php

namespace App\Questions;

use InvalidArgumentException;

class QuestionsEntity
{
    private ?int $id = null;
    private \DateTime $createdAt;
    private \DateTime $updatedAt;
    private string $title;
    private array $answers;
    public int $correctAnswerIndex;

    public function __construct(
        string $title,
        array $answers,
        int $correctAnswerIndex
    ) {
        $this->setTitle($title);
        $this->setAnswers($answers);
        $this->setCorrectAnswerIndex($correctAnswerIndex);

        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public static function validateTitle(string $title): void {
        if (strlen($title) < 2 || strlen($title) > 150) {
            throw new InvalidArgumentException('Title must be between 2 and 150 characters');
        }
    }

    public function setTitle(string $title): void {
        self::validateTitle($title);
        $this->title = $title;
    }

    public function updateTitle(string $title): void
    {
        $this->setTitle($title);
        $this->updatedAt = new \DateTime();
    }

    public function getTitle(): string {
        return $this->title;
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
        $intIndex = filter_var($index, FILTER_VALIDATE_INT);

        if ($intIndex === false) {
            throw new \InvalidArgumentException('correctAnswerIndex must be a valid integer');
        }

        if ($intIndex < 0 || $intIndex > 3) {
            throw new \InvalidArgumentException('Correct answer index must be between 0 and 3');
        }
    }

    private function setCorrectAnswerIndex(int $index): void {
        QuestionsEntity::validateAnswerIndex($index);
        $this->correctAnswerIndex = $index;
    }

    private function updateCorrectAnswerIndex(int $index): void {
        $this->setCorrectAnswerIndex($index);
        $this->updatedAt = new \DateTime();
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

    public function getCreatedAt(): \DateTime {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTime {
        return $this->updatedAt;
    }

    /**
     * @throws \Exception
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

    public function toArray(): array {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'answers' => $this->answers,
            'correctAnswerIndex' => $this->correctAnswerIndex,
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