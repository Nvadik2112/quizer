<?php

namespace App\Auth\Guards;

require_once __DIR__ . '/../../../vendor/autoload.php';

use App\Auth\AuthService;
use App\Auth\LocalStrategy;
use App\Exceptions\Domain\UnauthorizedException;
use App\Hash\HashService;
use App\Users\UsersModule;
use Symfony\Component\HttpFoundation\Request;

class LocalGuard {
    private AuthService $authService;
    private LocalStrategy $localStrategy;

    public function __construct() {
        $hashService = new HashService();
        $usersModule = UsersModule::getInstance();
        $this->authService = new AuthService($hashService, $usersModule->getUserService());
        $this->localStrategy = new LocalStrategy($this->authService);
    }

    /**
     * @throws UnauthorizedException
     */
    public function validate(Request $request): ?array
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        return $this->localStrategy->validate($email, $password);
    }
}
