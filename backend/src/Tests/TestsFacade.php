<?php

namespace App\Tests;

use App\Exceptions\Domain\BadRequestException;
use App\Exceptions\Domain\NotFoundException;
use App\Questions\QuestionsEntity;
use App\Questions\QuestionsService;
use App\Tests\Dto\CreateTestDto;
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
    public function createTestWithQuestion(array $data, int $userId): array
    {
        try {
            $this->connection->beginTransaction();
            $test = $this->testsService->create($data, $userId);
            $dto = CreateTestDto::fromArray($data);
            $testId = $test->getId();

            $questions = [];
            foreach ($dto->questions as $questionData) {
                $questions[] = $this->questionsService->create($questionData, $testId);
            }

            $this->connection->commit();

            return [
                'test' => $test,
                'questions' => $questions
            ];
        } catch (Exception $e) {
            $this->connection->rollBack();
            throw $e;
        }
    }
}