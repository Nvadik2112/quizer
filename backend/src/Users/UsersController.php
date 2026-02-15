<?php

namespace App\Users;

use App\Auth\Guards\JwtGuard;
use App\Exceptions\Domain\BadRequestException;
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

    /*#[Route('/users/me/wishes', methods: ['GET'])]
    public function getProfileWishes(Request $request): JsonResponse
    {
        try {
            $user = $this->jwtGuard->validate($request);
            $wishes = $this->usersService->findWishesByUser($user->getId());

            return new JsonResponse($wishes);
       } catch (\Exception $e) {
            return new JsonResponse([
                'error' => $e->getMessage()
            ], $e->getCode() ?: 401);
        }
    }*/

    /**
     * @throws NotFoundException
     */
    public function getUser(int $id): JsonResponse
    {
        $user = $this->usersService->findById($id);

        return new JsonResponse($user->toArray());
    }
   /*#[Route('/users/{id}/wishes', methods: ['GET'])]
     public function getUserWishes(int $id): JsonResponse
     {
         try {
             $wishes = $this->usersService->findWishesByUser($id);

             return new JsonResponse($wishes);
         } catch (\Exception $e) {
             return new JsonResponse([
                 'error' => $e->getMessage()
             ], $e->getCode() ?: Status::NOT_FOUND);
         }
     }*/

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
     * @throws BadRequestException
     * @throws Exception
     */
    public function searchUsers(Request $request): JsonResponse
    {
        $jwtGuard = new JwtGuard();
        $jwtGuard->validate($request);

        $query = $request->query->get('query', '');
        $users = $this->usersService->search($query);
        $usersArray = array_map(function($user) {
                return $user->toArray();
            }, $users);

        return new JsonResponse($usersArray);
    }
}