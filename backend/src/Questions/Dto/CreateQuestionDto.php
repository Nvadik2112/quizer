<?php

namespace  App\Questions\Dto;

use App\Questions\QuestionsEntity;
use InvalidArgumentException;

class CreateQuestionDto
{
    public function __construct(
        public string $title,
        public array $answers,
        public int $position,
        public int $testId,
        public int $correctAnswerIndex,
    ){
        $this->validate();
    }

    public static function fromArray(array $data): self {
        $correctAnswerIndex = $data['correctAnswerIndex'];
        $testId = $data['testId'];
        $position = $data['position'];

        return new self(
            $data['title'] ?? '',
            $data['answers'] ?? [],
                (int)$position ?? 0,
                (int)$testId,
            (int)$correctAnswerIndex
        );
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'answers' => $this->answers,
            'position' => $this->position,
            'testId' => $this->testId,
            'correctAnswerIndex' => $this->correctAnswerIndex,
        ];
    }

    public function validate(): void {
        QuestionsEntity::validateAll(
            [$this->title, $this->answers, $this->correctAnswerIndex]
        );
    }
}
