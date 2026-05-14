<?php

namespace App\Questions;

use App\Auth\Guards\JwtGuard;
use App\Constants\Status;
use App\Exceptions\Domain\BadRequestException;
use App\Exceptions\Domain\NotFoundException;
use App\Tests\Guards\TestOwnerGuard;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class QuestionsController
{
    private QuestionsService $questionsService;
    private JwtGuard $jwtGuard;
    private TestOwnerGuard $testOwnerGuard;

    public function __construct(QuestionsService $questionsService,
                                JwtGuard $jwtGuard,
                                TestOwnerGuard $testOwnerGuard) {
        $this->questionsService = $questionsService;
        $this->jwtGuard = $jwtGuard;
        $this->testOwnerGuard = $testOwnerGuard;
    }

    /**
     * @throws NotFoundException
     */
    public function getQuestion(int $id): JsonResponse
    {
        $question = $this->questionsService->findById($id);
        $response = $question->toArray();

        return new JsonResponse($response);
    }

    /**
     * @throws BadRequestException
     */
    public function getQuestionsByTestId(Request $request): JsonResponse
    {
        $testId = $request->get('testId');
        $response = $this->questionsService->getQuestionsByTestId($testId);
        $questions = array_map(function($question) {
            return $question->toArray();
        }, $response);

        return new JsonResponse($questions);
    }

    /**
     * @throws NotFoundException
     * @throws BadRequestException
     * @throws Exception
     */
    public function createQuestion($request): JsonResponse
    {
        $user = $this->jwtGuard->validate($request);
        $testId = $request->get('testId');

        if (!$testId) {
            throw new BadRequestException('testId is required');
        }

        $this->testOwnerGuard->validateTest($user['id'], $testId);

        $data = json_decode($request->getContent(), true);
        $question = $this->questionsService->create($data, $testId);

        $responseData = $question->toArray();
        $responseData['correctAnswerIndex'] = $question->getCorrectAnswerIndex();

        return new JsonResponse($responseData, Status::CREATED);
    }

    /**
     * @throws NotFoundException
     * @throws Exception
     */
    public function updateQuestion(Request $request, int $id): JsonResponse
    {
        $user = $this->jwtGuard->validate($request);
        $this->testOwnerGuard->validateQuestion($user['id'], $id);

        $data = json_decode($request->getContent(), true) ?: [];
        $updateQuestion = $this->questionsService->update($id, $data);

        return new JsonResponse($updateQuestion->toArray());
    }

    /**
     * @throws NotFoundException
     * @throws Exception
     */
    public function deleteQuestion(Request $request, int $id): JsonResponse
    {
        $user = $this->jwtGuard->validate($request);
        $this->testOwnerGuard->validateQuestion($user['id'], $id);

        $deletedQuestion = $this->questionsService->delete($id);

        return new JsonResponse($deletedQuestion->toArray());
    }

    /**
     * @throws NotFoundException
     * @throws BadRequestException
     */
    public function checkAnswer(Request $request, int $questionId): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?: [];

        if (!isset($data['correctAnswerIndex'])) {
            throw new BadRequestException('correctAnswerIndex is required');
        }

        $isCorrect = $this->questionsService->checkAnswerIndex($questionId, $data['correctAnswerIndex']);

        return new JsonResponse($isCorrect);
    }
}