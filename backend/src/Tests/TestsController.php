<?php

namespace App\Tests;

use App\Auth\Guards\JwtGuard;
use App\Constants\Status;
use App\Tests\Guards\TestOwnerGuard;
use Exception;
use App\Exceptions\Domain\NotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class TestsController
{
    private TestsService $testsService;
    private TestsFacade $testsFacade;

    private JwtGuard $jwtGuard;
    private TestOwnerGuard $testOwnerGuard;


    public function __construct(TestsService $testsService,
                                TestsFacade $testsFacade,
                                JwtGuard $jwtGuard,
                                TestOwnerGuard $testOwnerGuard)
    {
        $this->testsService = $testsService;
        $this->testsFacade = $testsFacade;
        $this->jwtGuard = $jwtGuard;
        $this->testOwnerGuard = $testOwnerGuard;
    }

    /**
     * @throws NotFoundException
     * @throws Exception
     */
    public function createTest(Request $request): JsonResponse
    {
        $user = $this->jwtGuard->validate($request);
        $data = json_decode($request->getContent(), true);
        $result = $this->testsFacade->createTestWithQuestions($data, $user['id']);

        return new JsonResponse($result, Status::CREATED);
    }

    /**
     * @throws Exception
     */
    public function updateTest(Request $request, int $id): JsonResponse
    {
        $user = $this->jwtGuard->validate($request);
        $this->testOwnerGuard->validateTest($user['id'], $id);
        $data = json_decode($request->getContent(), true);
        $result = $this->testsService->update($id, $data);

        return new JsonResponse($result->toArray());
    }

    /**
     * @throws Exception
     */
    public  function getTests(): JsonResponse
    {
        $tests = $this->testsService->getTests();

        $testsArray = array_map(function($test) {
            return $test->toArray();
        }, $tests);

        return new JsonResponse($testsArray);
    }

    /**
     * @throws Exception
     */
    public function deleteTest(Request $request, int $id): JsonResponse
    {
        $user = $this->jwtGuard->validate($request);
        $this->testOwnerGuard->validateTest($user['id'], $id);
        $result = $this->testsService->delete($id);

        return new JsonResponse($result->toArray());
    }
}