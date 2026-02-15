<?php

namespace  App\Questions\Dto;

class CreateQuestionDto
{
    public function __construct(
        public string $title,
        public array $answers,
        public int $correctAnswerIndex,
    ){

    }

    public static function fromArray(array $data): self {
        return new self(
            $data['title'] ?? '',
            $data['answers'] ?? [],
            $data['correctAnswerIndex'] ?? ''
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
}
