<?php

use App\Auth\Guards\JwtGuard;
use App\Constants\Status;
use App\Exceptions\Domain\NotFoundException;
use App\Questions\Dto\CreateQuestionDto;
use App\Questions\QuestionsService;
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

        return new JsonResponse($question->toArray());
    }

    /**
     * @throws NotFoundException
     * @throws \App\Exceptions\Domain\BadRequestException
     */
    public function createQuestion(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $createQuestionDto = CreateQuestionDto::fromArray($data);
        $question = $this->questionsService->create($createQuestionDto);

        return new JsonResponse($question, Status::CREATED);
    }

    /**
     * @throws NotFoundException
     */
    public function updateQuestion(Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?: [];

        $updateQuestion = $this->questionsService->update($id, $data);

        return new JsonResponse($updateQuestion->toArray());
    }

    /**
     * @throws NotFoundException
     */
    public function deleteQuestion(Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?: [];

        $deletedQuestion = $this->questionsService->delete($id);

        return new JsonResponse($deletedQuestion->toArray());
    }
}