<?php

namespace App;

use App\Auth\AuthModule;
use App\Auth\Exceptions\ValidationException;
use App\Database\DataBaseModule;
use App\Exceptions\Domain\BadRequestException;
use App\Exceptions\Domain\ForbiddenException;
use App\Exceptions\Domain\NotFoundException;
use App\Exceptions\Domain\UnauthorizedException;
use App\Questions\QuestionsModule;
use App\Tests\TestsModule;
use App\Users\UsersModule;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AppModule
{
    private AuthModule $authModule;
    private UsersModule $usersModule;
    private QuestionsModule $questionsModule;
    private TestsModule $testsModule;

    public function __construct()
    {
        DataBaseModule::getInstance();

        $this->authModule = AuthModule::getInstance();
        $this->usersModule = UsersModule::getInstance();
        $this->questionsModule = QuestionsModule::getInstance();
        $this->testsModule = TestsModule::getInstance();
    }

    /**
     * @throws NotFoundException
     * @throws UnauthorizedException
     * @throws ForbiddenException
     * @throws BadRequestException
     * @throws ValidationException
     */
    public function handle(?Request $request = null): Response
    {
        $request = $request ?? Request::createFromGlobals();
        $path = $request->getPathInfo();
        $method = $request->getMethod();

        return $this->route($request, $path, $method);
    }

    /**
     * @throws NotFoundException
     * @throws UnauthorizedException
     * @throws ForbiddenException
     * @throws BadRequestException
     * @throws ValidationException
     * @throws \Exception
     */
    private function route(Request $request, string $path, string $method): JsonResponse
    {
        $authController = $this->authModule->getAuthController();
        $usersController = $this->usersModule->getUsersController();
        $questionsController = $this->questionsModule->getQuestionsController();
        $testsController = $this->testsModule->getTestsController();

        if ($method === 'GET') {
            if ($path === '/users/me') {
                return $usersController->getProfile($request);
            }

            if (preg_match('#^/users/(\d+)$#', $path, $matches)) {
                return $usersController->getUser((int)$matches[1]);
            }

            if ($path === '/questions') {
                return $questionsController->getQuestionsByTestId($request);
            }

            if (preg_match('#^/questions/(\d+)$#', $path, $matches)) {
                return $questionsController->getQuestion((int)$matches[1]);
            }

            if (preg_match('#^/questions/(\d+)/check$#', $path, $matches)) {
                return $questionsController->checkAnswer($request, (int)$matches[1]);
            }

            if ($path === '/tests') {
                return $testsController->getTests($request);
            }
        }

        if ($method === 'POST') {
            if ($path === '/signin') {
                return $authController->signin($request);
            }

            if ($path === '/signup') {
                return $authController->signup($request);
            }

            if ($path === '/questions') {
                return $questionsController->createQuestion($request);
            }

            if ($path === '/tests') {
                return $testsController->createTest($request);
            }
        }

        if ($method === 'PATCH') {
            if ($path === '/users/me') {
                return $usersController->updateMyProfile($request);
            }

            if (preg_match('#^/questions/(\d+)$#', $path, $matches)) {
                return $questionsController->updateQuestion($request, (int)$matches[1]);
            }

            if (preg_match('#^/tests/(\d+)$#', $path, $matches)) {
                return $testsController->updateTest($request, (int)$matches[1]);
            }
        }

        if ($method === 'DELETE') {
            if (preg_match('#^/users/(\d+)$#', $path, $matches)) {
                return $usersController->deleteUser($request, (int)$matches[1]);
            }

            if (preg_match('#^/questions/(\d+)$#', $path, $matches)) {
                return $questionsController->deleteQuestion($request, (int)$matches[1]);
            }

            if (preg_match('#^/tests/(\d+)$#', $path, $matches)) {
                return $testsController->deleteTest($request, (int)$matches[1]);
            }
        }

        return new JsonResponse(['error' => 'Not Found'], 404);
    }
}
