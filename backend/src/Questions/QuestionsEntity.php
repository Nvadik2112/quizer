<?php

namespace App\Questions;

class QuestionsEntity
{
    private ?int $id = null;
    private \DateTime $createdAt;
    private \DateTime $updatedAt;
    private string $title;
    private array $answers;
    private int $correctAnswerIndex;

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
            throw new \InvalidArgumentException('Title must be between 2 and 150 characters');
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

    private function updateAnswers(array $answers): void
    {
        $this->setAnswers($answers);
        $this->updatedAt = new \DateTime();
    }

    public function getAnswers(): array {
        return $this->answers;
    }

    public static function validateAnswerIndex(int $index): void {
        if ($index < 1 || $index >= 4) {
            throw new \InvalidArgumentException('Correct answer index must be between 1 and 4');
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

    public static function fromArray(array $data): self {
        $question = new self(
            $data['title'],
            $data['answers'],
            $data['correctAnswerIndex']
        );

        if (isset($data['id'])) {
            $question->setId((int)$data['id']);
        }

        if (isset($data['createdAt']) && is_string($data['createdAt'])) {
            $question->createdAt = \DateTime::createFromFormat('Y-m-d\TH:i:s.u\Z', $data['createdAt']);
        }

        if (isset($data['updatedAt']) && is_string($data['updatedAt'])) {
            $question->updatedAt = \DateTime::createFromFormat('Y-m-d\TH:i:s.u\Z', $data['updatedAt']);
        }

        return $question;
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'answers' => $this->answers,
        ];
    }
}