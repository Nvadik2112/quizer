<?php

namespace App\Questions\Dto;

use App\Questions\QuestionsEntity;

class UpdateQuestionDto {
    public function __construct(
        public ?string $title = null,
        public ?array $answers = null,
        public ?int $correctAnswerIndex = null
    ) {
        if ($this->title !== null) {
            QuestionsEntity::validateTitle($this->title);
        }

        if ($this->answers !== null) {
            QuestionsEntity::validateAnswers($this->answers);

            foreach ($this->answers as $answer) {
                QuestionsEntity::validateAnswer($answer);
            }
        }

        if ($this->correctAnswerIndex !== null) {

            if (!is_numeric($correctAnswerIndex)) {
                throw new \InvalidArgumentException('correctAnswerIndex must be a valid integer');
            }

            QuestionsEntity::validateAnswerIndex($this->correctAnswerIndex);
        }
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['title'] ?? null,
            $data['answers'] ?? null,
                $data['correctAnswerIndex'] ?? null
        );
    }

    public function hasChanges(): bool
    {
        return $this->title !== null ||
            $this->answers !== null ||
            $this->correctAnswerIndex !== null;
    }
}