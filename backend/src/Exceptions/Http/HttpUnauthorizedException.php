<?php

namespace App\Exceptions\Http;
use App\Constants\Status;
use App\Exceptions\HttpException;

class HttpUnauthorizedException extends HttpException
{
    public function __construct(string $message = "Unauthorized")
    {
        parent::__construct($message, Status::UNAUTHORIZED);
    }
}