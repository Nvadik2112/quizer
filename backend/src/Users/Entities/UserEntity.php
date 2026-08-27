<?php

namespace App\Users\Entities;
use App\Exceptions\Domain\BadRequestException;
use App\Hash\HashService;

class UserEntity {
    private ?int $id = null;
    private \DateTime $createdAt;
    private \DateTime $updatedAt;
    private string $email;
    private string $password;

    /**
     * @throws BadRequestException
     */
    public function __construct(
        string $email,
        string $password,
    ) {
        $this->setEmail($email);
        $this->setPassword($password);

        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     * @throws BadRequestException
     */
    public static function validateEmail(string $email): void {
        if (!$email) {
            throw new BadRequestException('Email обязателен');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new BadRequestException('Неверный формат email');
        }
    }

    /**
     * @throws BadRequestException
     */
    public static function validatePassword(string $password): void {
        if (!$password) {
            throw new BadRequestException('Пароль обязателен');
        }

        if (strlen($password) < 6) {
            throw new BadRequestException('Пароль должен содержать не менее 6 символов');
        }
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getEmail(): string {
        return $this->email;
    }

    /**
     * @throws BadRequestException
     */
    public function setEmail(string $email): void {
        self::validateEmail($email);
        $this->email = $email;
        $this->updatedAt = new \DateTime();
    }

    /**
     * @throws BadRequestException
     */
    public function setPassword(string $password): void {
        self::validatePassword($password);
        $this->password = $password;
        $this->updatedAt = new \DateTime();
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

    public static function fromArray(array $data): self {
        $user = new self(
            $data['email'],
            $data['password'],
        );

        if (isset($data['id'])) {
            $user->setId((int)$data['id']);
        }

        if (isset($data['createdAt']) && is_string($data['createdAt'])) {
            $user->createdAt = \DateTime::createFromFormat('Y-m-d\TH:i:s.u\Z', $data['createdAt']);
        }

        if (isset($data['updatedAt']) && is_string($data['updatedAt'])) {
            $user->updatedAt = \DateTime::createFromFormat('Y-m-d\TH:i:s.u\Z', $data['updatedAt']);
        }

        return $user;
    }
   
    public function toArray(): array {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'createdAt' => $this->createdAt->format('Y-m-d\TH:i:s.u\Z'),
            'updatedAt' => $this->updatedAt->format('Y-m-d\TH:i:s.u\Z'),
        ];
    }

    public function clearPassword(): void {
        $this->password = '';
    }

    public function verifyPassword(string $password, HashService $hashService): bool
    {
        return $hashService->comparePassword($password, $this->password);
    }
}
