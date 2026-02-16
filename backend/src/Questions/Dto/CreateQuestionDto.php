<?php

namespace  App\Questions\Dto;

use App\Exceptions\Domain\BadRequestException;
use App\Questions\QuestionsEntity;
use InvalidArgumentException;

class CreateQuestionDto
{
    public function __construct(
        public string $title,
        public array $answers,
        public int $correctAnswerIndex,
    ){
        $this->validate();
    }

    public static function fromArray(array $data): self {
        $correctAnswerIndex = $data['correctAnswerIndex'];

        if (!is_numeric($correctAnswerIndex)) {
            throw new \InvalidArgumentException('correctAnswerIndex must be a valid integer');
        }

        return new self(
            $data['title'] ?? '',
            $data['answers'] ?? [],
            (int)$correctAnswerIndex
        );
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'answers' => $this->answers,
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
    }
}
