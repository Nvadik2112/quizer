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

    // 2. Проверяем таблицу users
    echo "\n2. Таблица users:\n";
    try {
        $stmt = $pdo->query("SELECT EXISTS (
            SELECT FROM information_schema.tables 
            WHERE table_schema = current_schema()
            AND table_name = 'users'
        )");

        if ($stmt->fetchColumn()) {
            echo "   ✅ Таблица users существует\n";

            // Показываем структуру
            $columns = $pdo->query("
                SELECT column_name, data_type, is_nullable
                FROM information_schema.columns 
                WHERE table_schema = current_schema()
                AND table_name = 'users'
                ORDER BY ordinal_position
            ")->fetchAll();

            echo "   Структура:\n";
            foreach ($columns as $col) {
                echo "   - {$col['column_name']} ({$col['data_type']})\n";
            }

            // Показываем данные
            $users = $pdo->query("SELECT COUNT(*) as count FROM users")->fetch();
            echo "   Записей: {$users['count']}\n";
        } else {
            echo "   ❌ Таблица users НЕ существует!\n";
        }
    } catch (Exception $e) {
        echo "   ❌ Ошибка: " . $e->getMessage() . "\n";
    }

    // 3. Проверяем файл миграции
    echo "\n3. Файл миграции:\n";
    $migrationFile = __DIR__ . '/../src/Database/migrations/0001_create_users_table.sql';
    echo "   Путь: $migrationFile\n";
    echo "   Существует: " . (file_exists($migrationFile) ? '✅ Да' : '❌ Нет') . "\n";

    if (file_exists($migrationFile)) {
        $content = file_get_contents($migrationFile);
        echo "   Размер: " . strlen($content) . " байт\n";
        echo "   Содержимое (первые 200 символов):\n";
        echo "   " . substr($content, 0, 200) . "...\n";
    }

} catch (Exception $e) {
    echo "❌ Критическая ошибка: " . $e->getMessage() . "\n";
}

echo "\n=== Завершено ===\n";