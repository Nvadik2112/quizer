<?php

namespace App\Tests\Dto;

use App\Questions\Dto\CreateQuestionDto;
use App\Tests\TestsEntity;
use InvalidArgumentException;

class CreateTestWithQuestionsDto
{

    public function __construct(
        public array $test,
        public array $questions,
    ) {
        $this->validate();
    }

    public static function fromArray(array $data): self
    {
        $test = $data['test'] ?? [];
        $questions = is_string($data['questions'] ?? null)
            ? json_decode($data['questions'], true)
            : ($data['questions'] ?? []);

        return new self($test, $questions);
    }

    public function toArray(): array
    {
        return [
            'test' => $this->test,
            'questions' => $this->questions,
        ];
    }

    public function validate(): void
    {
        TestsEntity::validateTitle($this->test['title']);

        if (empty($this->questions)) {
            throw new InvalidArgumentException('Вопросы к тесту должны быть заполнены');
        } else {
            foreach ($this->questions as $questionData) {
                CreateQuestionDto::fromArray($questionData);
            }
        }
    }

    public function getQuestions(): array
    {
        return $this->questions;
    }
}