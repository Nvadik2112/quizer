<?php

namespace App\Users;

use App\Exceptions\Domain\BadRequestException;
use App\Exceptions\Domain\ForbiddenException;
use App\Exceptions\Domain\NotFoundException;
use App\Hash\HashService;
use App\Users\Dto\CreateUserDto;
use App\Users\Dto\UpdateUserDto;
use App\Users\Entities\UserEntity;
use PDO;

class UsersService
{
    private HashService $hashService;
    private PDO $connection;

    public function __construct(PDO $connection, HashService $hashService)
    {
        $this->connection = $connection;
        $this->hashService = $hashService;
    }

    /**
     * @throws NotFoundException
     * @throws ForbiddenException
     * @throws BadRequestException
     */
    public function create($data): UserEntity
    {
        $dto = CreateUserDto::fromArray($data);
        $this->checkDuplicate($dto->email, $dto->username);
        $hashedPassword = $this->hashService->hashPassword($dto->password);

        $sql = "INSERT INTO users (username, email, password, about, avatar, created_at, updated_at) 
            VALUES (:username, :email, :password, :about, :avatar, NOW(), NOW())";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            'username' => $dto->username,
            'email' => $dto->email,
            'password' => $hashedPassword,
            'about' => $dto->about,
            'avatar' => $dto->avatar,
        ]);

        $userId = (int)$this->connection->lastInsertId();

        if ($userId === 0) {
            throw new BadRequestException('Не удалось создать пользователя');
        }

        return $this->findById($userId);
    }

    /**
     * @throws NotFoundException
     */
    public function findById(int $id): UserEntity
    {
        $sql = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['id' => $id]);
        
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$data) {
            throw new NotFoundException('Пользователь не найден');
        }

        return UserEntity::fromArray($data);
    }

    public function findByEmail(string $email): ?UserEntity
    {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['email' => $email]);
        
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $data ? UserEntity::fromArray($data) : null;
    }

    /**
     * @throws NotFoundException
     * @throws BadRequestException
     */
    public function search(string $query): array
    {
        if (empty($query)) {
            throw new BadRequestException('Параметр поиска не должен быть пустым');
        }

        $sql = "SELECT * FROM users WHERE username LIKE :query OR email LIKE :query";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['query' => "%{$query}%"]);

        $usersData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($usersData)) {
            throw new NotFoundException('Пользователи не найдены');
        }

        return array_map(fn($data) => UserEntity::fromArray($data), $usersData);
    }

    public function findByEmailOrUsername(string $identifier): ?UserEntity
    {
        $sql = "SELECT * FROM users WHERE email = :identifier OR username = :identifier LIMIT 1";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['identifier' => $identifier]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? UserEntity::fromArray($data) : null;
    }

    /**
     * @throws NotFoundException
     * @throws ForbiddenException
     */
    public function update(int $userId, array $data): UserEntity
    {
        $dto = UpdateUserDto::fromArray($data);

        if (!$dto->hasChanges()) {
            throw new \InvalidArgumentException('Нет данных для обновления', 400);
        }

        if ($dto->email !== null || $dto->username !== null) {
            $currentUser = $this->findById($userId);
            $email = $dto->email ?? $currentUser->getEmail();
            $username = $dto->username ?? $currentUser->getUsername();
            $this->checkDuplicate($email, $username, $userId);
        }

        $updateData = array_filter([
            'username' => $dto->username,
            'email' => $dto->email,
            'password' => $dto->password ? $this->hashService->hashPassword($dto->password) : null,
            'about' => $dto->about,
            'avatar' => $dto->avatar,
        ], fn($value) => $value !== null);

        $setFields = array_map(fn($key) => "{$key} = :{$key}", array_keys($updateData));
        $setFields[] = "updated_at = NOW()";
        $setClause = implode(', ', $setFields);

        $sql = "UPDATE users SET {$setClause} WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(array_merge($updateData, ['id' => $userId]));

        $user = $this->findById($userId);
        $user->clearPassword();

        return $user;
    }

    /*public function findWishesByUser(int $userId): array
    {
        $sql = "SELECT w.* FROM wishes w
                WHERE w.user_id = :userId";
        
       $stmt = $this->connection->prepare($sql);
        $stmt->execute(['userId' => $userId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }*/

    /**
     * @throws ForbiddenException
     */
    private function checkDuplicate(string $email, string $username, ?int $excludeUserId = null): void
    {
        $sql = "SELECT COUNT(*) as count FROM users 
                WHERE (email = :email OR username = :username)";
        
        $params = [
            'email' => $email,
            'username' => $username
        ];

        if ($excludeUserId) {
            $sql .= " AND id != :excludeId";
            $params['excludeId'] = $excludeUserId;
        }

        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result && $result['count'] > 0) {
            throw new ForbiddenException('Email или username с таким именем существует');
        }
    }
}