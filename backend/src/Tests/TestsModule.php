<?php

namespace App\Tests;

use App\Auth\Guards\JwtGuard;
use App\Database\DataBaseModule as DataBase;
use App\Questions\QuestionsService;
use App\Tests\Guards\TestOwnerGuard;

class TestsModule
{
    private static ?TestsModule $instance = null;

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
        $this->services['jwtGuard'] = new JwtGuard();
        $this->services['pdo'] = DataBase::getInstance();

        $this->services['testOwnerGuard'] = new TestOwnerGuard(
            $this->services['pdo'],
        );

        $this->services['testsService'] = new TestsService(
            $this->services['pdo'],
        );

        $this->services['questionsService'] = new QuestionsService(
            $this->services['pdo'],
        );

        $this->services['testsFacade'] = new TestsFacade(
            $this->services['pdo'],
            $this->services['testsService'],
            $this->services['questionsService']
        );

        $this->services['testsController'] = new TestsController(
            $this->services['testsService'],
            $this->services['testsFacade'],
            $this->services['jwtGuard'],
            $this->services['testOwnerGuard']
        );
    }

    public function getTestsController(): TestsController
    {
        return $this->services['testsController'];
    }
}
