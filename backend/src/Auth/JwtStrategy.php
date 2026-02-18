<?php

namespace App\Auth;

use App\Exceptions\Domain\NotFoundException;
use App\Exceptions\Domain\UnauthorizedException;
use App\Users\UsersService;
use App\Config\ConfigService;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtStrategy {
    private UsersService $usersService;
    private string $secretKey;

    public function __construct(UsersService $usersService) {
        $this->usersService = $usersService;
        $configService = new ConfigService();
        $this->secretKey = $configService->get('JWT_KEY',  'default_secret_key');
    }

    /**
     * @throws Exception
     */
    public function validate($request): array
    {
        $token = $this->extractJwtFromRequest($request);

        if (!$token) {
            throw new UnauthorizedException('Токен не предоставлен');
        }

        try {
            $jwtPayload = $this->verifyToken($token);
            $user = $this->usersService->findById($jwtPayload->sub);

            return $user->toArray();

        } catch (NotFoundException $e) {
            throw new UnauthorizedException('Пользователь не найден');
        } catch (Exception $e) {
            throw new UnauthorizedException($e->getMessage());
        }
    }

    private function extractJwtFromRequest($request): ?string
    {
        $authHeader = $request->headers->get('Authorization');

        if (!$authHeader) {
            return null;
        }

        if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * @throws Exception
     */
    private function verifyToken($token): \stdClass
    {
        try {
            return JWT::decode($token, new Key($this->secretKey, 'HS256'));
        } catch (Exception $e) {
            throw new UnauthorizedException($e->getMessage());
        }
    }

    /**
     * @throws UnauthorizedException
     */
    public function handle($request): array
    {
        try {
            $user = $this->validate($request);

            if (is_object($request)) {
                $request->attributes->set('user', $user);
            } else {
                $request['user'] = $user;
            }

            return $request;

        } catch (Exception $e) {
            throw new UnauthorizedException($e->getMessage());
        }
    }
}