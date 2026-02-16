<?php
// check_database_state.php

require_once __DIR__ . '/vendor/autoload.php';

use App\Database\DataBaseModule;

try {
    $pdo = DataBaseModule::getInstance();

    echo "=== Проверка состояния БД ===\n\n";

    // 1. Проверяем таблицу migrations
    echo "1. Таблица migrations:\n";
    try {
        $stmt = $pdo->query("SELECT migration, applied_at FROM migrations ORDER BY id");
        $migrations = $stmt->fetchAll();

        if (empty($migrations)) {
            echo "   ❌ Таблица migrations пуста или не существует\n";
        } else {
            echo "   ✅ Записи в migrations:\n";
            foreach ($migrations as $m) {
                echo "   - {$m['migration']} ({$m['applied_at']})\n";
            }
        }
    } catch (Exception $e) {
        echo "   ❌ Ошибка: " . $e->getMessage() . "\n";
    }

    // 2. Проверяем таблицу questions (ИСПРАВЛЕНО)
    echo "\n2. Таблица questions:\n";
    try {
        $stmt = $pdo->query("SELECT EXISTS (
            SELECT FROM information_schema.tables 
            WHERE table_schema = current_schema()
            AND table_name = 'questions'
        )");

        if ($stmt->fetchColumn()) {
            echo "   ✅ Таблица questions существует\n";

            // Структура таблицы
            $columns = $pdo->query("
                SELECT column_name, data_type, is_nullable
                FROM information_schema.columns 
                WHERE table_schema = current_schema()
                AND table_name = 'questions'
                ORDER BY ordinal_position
            ")->fetchAll();

            echo "   Структура:\n";
            foreach ($columns as $col) {
                echo "   - {$col['column_name']} ({$col['data_type']})\n";
            }

            // Количество записей
            $count = $pdo->query("SELECT COUNT(*) as count FROM questions")->fetch();
            echo "   Записей: {$count['count']}\n";
        } else {
            echo "   ❌ Таблица questions НЕ существует!\n";

            // Попытка создать таблицу
            echo "\n   Пытаюсь создать таблицу questions...\n";
            $sql = "
                CREATE TABLE IF NOT EXISTS questions (
                    id SERIAL PRIMARY KEY,
                    title VARCHAR(150) NOT NULL,
                    answers TEXT[] NOT NULL,
                    correct_answer_index INTEGER NOT NULL CHECK (correct_answer_index >= 0 AND correct_answer_index < array_length(answers, 1)),
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )
            ";

            try {
                $pdo->exec($sql);
                echo "   ✅ Таблица questions успешно создана!\n";
            } catch (Exception $e) {
                echo "   ❌ Ошибка создания: " . $e->getMessage() . "\n";
            }
        }
    } catch (Exception $e) {
        echo "   ❌ Ошибка: " . $e->getMessage() . "\n";
    }

    // 3. Проверяем таблицу users (дополнительно)
    echo "\n3. Таблица users:\n";
    try {
        $stmt = $pdo->query("SELECT EXISTS (
            SELECT FROM information_schema.tables 
            WHERE table_schema = current_schema()
            AND table_name = 'users'
        )");

        if ($stmt->fetchColumn()) {
            echo "   ✅ Таблица users существует\n";
            $count = $pdo->query("SELECT COUNT(*) as count FROM users")->fetch();
            echo "   Записей: {$count['count']}\n";
        } else {
            echo "   ❌ Таблица users НЕ существует\n";
        }
    } catch (Exception $e) {
        echo "   ❌ Ошибка: " . $e->getMessage() . "\n";
    }

    // 4. Проверяем файл миграции
    echo "\n4. Файл миграции questions:\n";
    $migrationFile = __DIR__ . '/src/Database/migrations/0002_create_questions_table.sql';
    echo "   Путь: $migrationFile\n";
    echo "   Существует: " . (file_exists($migrationFile) ? '✅ Да' : '❌ Нет') . "\n";

    // 5. Проверяем путь к миграциям
    echo "\n5. Директория миграций:\n";
    $migrationsDir = __DIR__ . '/src/Database/migrations';
    echo "   Путь: $migrationsDir\n";
    echo "   Существует: " . (is_dir($migrationsDir) ? '✅ Да' : '❌ Нет') . "\n";

    if (is_dir($migrationsDir)) {
        $files = scandir($migrationsDir);
        $sqlFiles = array_filter($files, fn($f) => str_ends_with($f, '.sql'));
        echo "   SQL файлы: " . implode(', ', $sqlFiles) . "\n";
    }

} catch (Exception $e) {
    echo "❌ Критическая ошибка: " . $e->getMessage() . "\n";
}

echo "\n=== Завершено ===\n";