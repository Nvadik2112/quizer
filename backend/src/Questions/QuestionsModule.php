<?php

namespace App\Questions;

use App\Auth\Guards\JwtGuard;
use App\Database\DataBaseModule as DataBase;

class QuestionsModule
{
    private static ?QuestionsModule $instance = null;

    private array $services = [];

    private function __construct() {
        $this->initialize();
    }

    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function initialize(): void {
        $this->services['pdo'] = DataBase::getInstance();
        $this->services['questionsService'] = new QuestionsService(
            $this->services['pdo'],
        );

        $this->services['jwtGuard'] = new JwtGuard();

        $this->services['questionsController'] = new QuestionsController(
            $this->services['questionsService'],
            $this->services['jwtGuard']
        );
    }

    public function getQuestionsService(): QuestionsService
    {
        return $this->services['questionsService'];
    }

    public function getQuestionsController(): QuestionsController
    {
        return $this->services['questionsController'];
    }
}