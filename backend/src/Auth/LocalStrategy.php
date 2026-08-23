<?php

namespace App\Auth;

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Exceptions\Domain\UnauthorizedException;
use App\Users\Dto\CreateUserDto;

class LocalStrategy {
    private AuthService $authService;

    public function __construct($authService) {
        $this->authService = $authService;
    }

    /**
     * @throws UnauthorizedException
     */
    public function validate($email, $password): array
    {
        $credentials = new CreateUserDto($email, $password);

        $user = $this->authService->validatePassword(
            $credentials->email,
            $credentials->password
        );

        if (!$user) {
            throw new UnauthorizedException('Неверный email или пароль');
        }

        return $user;
    }
}