<?php

namespace App\Questions;

use App\Auth\Guards\JwtGuard;
use App\Constants\Status;
use App\Exceptions\Domain\BadRequestException;
use App\Exceptions\Domain\NotFoundException;
use App\Questions\Dto\CreateQuestionDto;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class QuestionsController
{
    private QuestionsService $questionsService;
    private JwtGuard $jwtGuard;

    public function __construct(QuestionsService $questionsService, JwtGuard $jwtGuard) {
        $this->questionsService = $questionsService;
        $this->jwtGuard = $jwtGuard;
    }

    /**
     * @throws NotFoundException
     */
    public function getQuestion(int $id): JsonResponse
    {
        $question = $this->questionsService->findById($id);

        $response = $question->toArray();

        unset($response['correctAnswerIndex']);

        return new JsonResponse($response);
    }

    /**
     * @throws NotFoundException
     * @throws BadRequestException
     * @throws Exception
     */
    public function createQuestion($request): JsonResponse
    {
        $this->jwtGuard->validate($request);
        $data = json_decode($request->getContent(), true);
        $question = $this->questionsService->create($data);

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
        $this->jwtGuard->validate($request);
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
        $this->jwtGuard->validate($request);
        $deletedQuestion = $this->questionsService->delete($id);

        return new JsonResponse($deletedQuestion->toArray());
    }

    /**
     * @throws NotFoundException
     */
    public function checkAnswer(Request $request, int $questionId): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $isCorrect = $this->questionsService->checkAnswerIndex($questionId, $data);

        return new JsonResponse($isCorrect);
    }
}