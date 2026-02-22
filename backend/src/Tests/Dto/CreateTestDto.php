<?php

namespace App\Tests\Dto;


use App\Questions\QuestionsEntity;
use App\Tests\TestsEntity;
use InvalidArgumentException;

class CreateTestDto
{
    public function __construct(
        public int $title,
        public array $questions
    ){
        $this->validate();
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['title'] ?? '',
            $data['questions'] ?? []
        );
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'questions' => $this->questions
        ];
    }

    public function validate(): void
    {
        if (count($this->questions) === 0) {
            throw new InvalidArgumentException('Вопросы к тесту должны быть заполнены');
        }

        TestsEntity::validateTitle($this->title);

        foreach ($this->questions as $question) {
            QuestionsEntity::validateAll($question);
        }
    }
}