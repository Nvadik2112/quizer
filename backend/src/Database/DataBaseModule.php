<?php

namespace App\Database;

use App\Config\ConfigService;
use PDO;

class DataBaseModule {
    private static ?PDO $connection = null;
    private static ?MigrationManager $migrationManager = null;

    public static function getInstance(): PDO {
        if (self::$connection === null) {
            self::initializeConnection();
        }
        return self::$connection;
    }

    public static function runMigrations(): void {
        if (self::$migrationManager === null) {
            $connection = self::getInstance();
            self::$migrationManager = new MigrationManager($connection);
        }

        self::$migrationManager->applyMigrations();
    }

    public static function getMigrationManager(): MigrationManager {
        if (self::$migrationManager === null) {
            $connection = self::getInstance();
            self::$migrationManager = new MigrationManager($connection);
        }

        return self::$migrationManager;
    }

    private static function initializeConnection(): void {
        $configService = new ConfigService();

        $config = [
            'host' => $configService->get('DB_HOST'),
            'port' => $configService->get('DB_PORT'),
            'username' => $configService->get('DB_USER'),
            'password' => $configService->get('DB_PASSWORD'),
            'database' => $configService->get('DB_NAME'),
            'schema' => $configService->get('DB_SCHEMA')
        ];

        try {
            $dsn = "pgsql:host={$config['host']};port={$config['port']};dbname={$config['database']}";

            self::$connection = new PDO($dsn, $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);

            self::$connection->exec("SET search_path TO {$config['schema']}");

        } catch (\PDOException $e) {
            throw new \RuntimeException('Database connection failed: ' . $e->getMessage());
        }
    }
}