<?php

namespace  App\Questions\Dto;

use App\Questions\QuestionsEntity;

class CreateQuestionDto
{
    public function __construct(
        public string $title,
        public array $answers,
        public int $position,
        public int $correctAnswerIndex,
    ){
        $this->validate();
    }

    public static function fromArray(array $data): self {
        $correctAnswerIndex = $data['correctAnswerIndex'];
        $position = $data['position'];

        return new self(
            $data['title'] ?? '',
            $data['answers'] ?? [],
                (int)$position ?? 0,
                (int)$correctAnswerIndex ?? 0
        );
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'answers' => $this->answers,
            'position' => $this->position,
            'correctAnswerIndex' => $this->correctAnswerIndex,
        ];
    }

    public function validate(): void {
        QuestionsEntity::validateTitle($this->title);
        QuestionsEntity::validateAnswers($this->answers);

        foreach ($this->answers as $answer) {
            QuestionsEntity::validateAnswer($answer);
        }

        QuestionsEntity::validateAnswerIndex($this->correctAnswerIndex);
        QuestionsEntity::validatePosition($this->position);
    }
}
