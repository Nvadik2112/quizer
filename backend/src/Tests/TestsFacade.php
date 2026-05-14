<?php

namespace App\Tests;

use App\Exceptions\Domain\BadRequestException;
use App\Exceptions\Domain\NotFoundException;
use App\Questions\QuestionsEntity;
use App\Questions\QuestionsService;
use App\Tests\Dto\CreateTestWithQuestionsDto;
use Exception;
use PDO;

class TestsFacade
{
    private PDO $connection;
    private TestsService $testsService;
    private QuestionsService $questionsService;
    public function __construct(
        PDO $connection,
        TestsService $testsService,
        QuestionsService $questionsService)
    {
        $this->connection = $connection;
        $this->testsService = $testsService;
        $this->questionsService = $questionsService;
    }

    /**
     * @param array $data
     * @param int $userId
     * @return array{test: TestsEntity, questions: QuestionsEntity[]}
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function createTestWithQuestions(array $data, int $userId): array
    {
        try {
            $this->connection->beginTransaction();
            $dto = CreateTestWithQuestionsDto::fromArray($data);
            $test = $this->testsService->create($data['test'], $userId);
            $testId = $test->getId();

            $questions = [];
            foreach ($dto->getQuestions()  as $questionData) {
                $questions[] = $this->questionsService->create($questionData, $testId);
            }

            $this->connection->commit();

            return [
                'test' => $test->toArray(),
                'questions' => array_map(function($question) {
                    return $question->toArray();
                }, $questions)
            ];

        } catch (BadRequestException $e) {
            $this->connection->rollBack();
            throw $e;
        }
    }
}