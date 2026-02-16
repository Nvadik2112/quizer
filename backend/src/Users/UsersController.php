<?php

namespace App\Users;

use App\Auth\Guards\JwtGuard;
use App\Exceptions\Domain\ForbiddenException;
use App\Exceptions\Domain\NotFoundException;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UsersController 
{
    private UsersService $usersService;
    private JwtGuard $jwtGuard;

    public function __construct(
        UsersService $usersService,
        JwtGuard $jwtGuard
    ) {
        $this->usersService = $usersService;
        $this->jwtGuard = $jwtGuard;
    }

    /**
     * @throws NotFoundException
     * @throws Exception
     */
    public function getProfile(Request $request): JsonResponse
    {
        $user = $this->jwtGuard->validate($request);
        $userData = $this->usersService->findById($user['id']);

        return new JsonResponse($userData->toArray());
    }

    /**
     * @throws NotFoundException
     */
    public function getUser(int $id): JsonResponse
    {
        $user = $this->usersService->findById($id);

        return new JsonResponse($user->toArray());
    }

    /**
     * @throws NotFoundException
     * @throws ForbiddenException
     * @throws Exception
     */
    public function updateMyProfile(Request $request): JsonResponse
    {
        $user = $this->jwtGuard->validate($request);
        $data = json_decode($request->getContent(), true) ?: [];

        $updatedUser = $this->usersService->update(
            $user['id'],
            $data
        );

        return new JsonResponse($updatedUser->toArray());
    }

    /**
     * @throws NotFoundException
     * @throws Exception
     */
    public function deleteUser(Request $request, int $id): JsonResponse
    {
        $this->jwtGuard->validate($request);
        $deletedUser = $this->usersService->delete($id);

        return new JsonResponse($deletedUser->toArray());
    }
}