<?php

namespace App\Database;

use PDO;
use RuntimeException;

class MigrationManager
{
    private PDO $connection;
    private string $migrationsTable = 'migrations';
    private string $migrationsPath;

    public function __construct(PDO $connection, string $migrationsPath = null)
    {
        $this->connection = $connection;

        if ($migrationsPath === null) {
            $this->migrationsPath = __DIR__ . '/migrations/';
        } else {
            $this->migrationsPath = rtrim($migrationsPath, '/') . '/';
        }

        $this->createMigrationsTable();
    }

    private function createMigrationsTable(): void
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS {$this->migrationsTable} (
                id SERIAL PRIMARY KEY,
                migration VARCHAR(255) UNIQUE NOT NULL,
                applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ";
        $this->connection->exec($sql);
    }

    private function getAppliedMigrations(): array
    {
        $stmt = $this->connection->query("SELECT migration FROM {$this->migrationsTable}");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * @return array Массив путей к файлам (например, ['/path/0002_add_index.sql'])
     */
    private function getPendingMigrations(): array
    {
        $applied = $this->getAppliedMigrations();
        $allFiles = scandir($this->migrationsPath);
        $pending = [];

        foreach ($allFiles as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
                if (!in_array($file, $applied, true)) {
                    $pending[] = $this->migrationsPath . $file;
                }
            }
        }

        sort($pending);
        return $pending;
    }
    public function applyMigrations(): void
    {
        $pendingMigrations = $this->getPendingMigrations();

        if (empty($pendingMigrations)) {
            return;
        }

        $this->connection->beginTransaction();

        try {
            foreach ($pendingMigrations as $filePath) {
                try {
                    $this->applyMigration($filePath);
                } catch (\Exception $e) {
                    error_log("Migration failed: " . $e->getMessage());
                    throw $e;
                }
            }
            $this->connection->commit();
        } catch (\Exception $e) {
            $this->connection->rollBack();
            throw new RuntimeException("Ошибка применения миграций: " . $e->getMessage(), 0, $e);
        }
    }
    private function applyMigration(string $filePath): void
    {
        $fileName = basename($filePath);
        $sql = file_get_contents($filePath);

        if ($sql === false) {
            throw new RuntimeException("Не удалось прочитать файл миграции: {$fileName}");
        }

        $this->connection->exec($sql);

        $stmt = $this->connection->prepare(
            "INSERT INTO {$this->migrationsTable} (migration) VALUES (:migration)"
        );

        $stmt->execute(['migration' => $fileName]);
    }
}