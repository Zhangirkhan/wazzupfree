<?php
/**
 * 🔍 Скрипт для отладки и тестирования webhook'ов
 * Можно запускать из браузера или командной строки
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Определяем базовые пути
$basePath = __DIR__;
$logPath = $basePath . '/storage/logs/laravel.log';

// HTML заголовок если запускается из браузера
if (php_sapi_name() !== 'cli') {
    echo "<!DOCTYPE html><html><head><meta charset='utf-8'>";
    echo "<title>🔍 Webhook Monitor</title>";
    echo "<style>
        body { font-family: monospace; background: #1a1a1a; color: #00ff00; padding: 20px; }
        .error { color: #ff0000; }
        .warning { color: #ffff00; }
        .info { color: #00ffff; }
        .success { color: #00ff00; }
        .box { border: 1px solid #444; padding: 15px; margin: 10px 0; background: #2a2a2a; }
        pre { white-space: pre-wrap; word-wrap: break-word; }
        .btn { background: #007700; color: white; border: none; padding: 8px 16px; margin: 5px; cursor: pointer; }
        .btn:hover { background: #009900; }
    </style></head><body>";
    echo "<h1>🔍 Webhook Monitor</h1>";
}

$action = $_GET['action'] ?? $_POST['action'] ?? 'status';

echo "🕐 " . date('Y-m-d H:i:s') . "\n";
echo "================================\n\n";

switch ($action) {
    case 'status':
        showStatus();
        break;
    case 'test':
        sendTestWebhook();
        break;
    case 'logs':
        showRecentLogs();
        break;
    case 'errors':
        showErrors();
        break;
    case 'clear':
        clearLogs();
        break;
    case 'endpoints':
        showEndpoints();
        break;
    default:
        showMenu();
        break;
}

function showStatus() {
    global $logPath;

    echo "📊 Статус системы webhook'ов:\n";
    echo "=============================\n\n";

    // Проверяем файл логов
    if (file_exists($logPath)) {
        $fileSize = filesize($logPath);
        $lastModified = date('Y-m-d H:i:s', filemtime($logPath));
        echo "✅ Файл логов: найден\n";
        echo "📁 Размер: " . formatBytes($fileSize) . "\n";
        echo "📅 Последнее изменение: {$lastModified}\n";

        // Анализируем логи
        $content = file_get_contents($logPath);
        $webhookCount = substr_count($content, '=== WEBHOOK RECEIVED ===');
        $errorCount = substr_count($content, 'ERROR');
        $testCount = substr_count($content, 'TEST WEBHOOK');

        echo "📡 Всего webhook'ов: {$webhookCount}\n";
        echo "🧪 Тестовых: {$testCount}\n";
        echo "❌ Ошибок: {$errorCount}\n";
        echo "✅ Успешных: " . ($webhookCount - $errorCount) . "\n";

    } else {
        echo "❌ Файл логов не найден: {$logPath}\n";
    }

    // Проверяем доступность endpoint'ов
    echo "\n🌐 Проверка endpoint'ов:\n";
    echo "========================\n";

    $endpoints = [
        'Основной webhook' => 'https://back-chat.ap.kz/api/webhooks/wazzup24',
        'Тестовый endpoint' => 'https://back-chat.ap.kz/api/webhooks/wazzup24?test=1'
    ];

    foreach ($endpoints as $name => $url) {
        $status = checkEndpoint($url);
        echo ($status ? "✅" : "❌") . " {$name}: " . ($status ? "доступен" : "недоступен") . "\n";
    }
}

function sendTestWebhook() {
    echo "🧪 Отправка тестового webhook'а:\n";
    echo "===============================\n\n";

    $url = 'https://back-chat.ap.kz/api/webhooks/wazzup24';
    $data = ['test' => true];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'User-Agent: WebhookDebug/1.0'
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        echo "❌ Ошибка cURL: {$error}\n";
    } else {
        echo "📡 URL: {$url}\n";
        echo "📊 HTTP код: {$httpCode}\n";
        echo "📥 Ответ: {$response}\n";
        echo ($httpCode == 200 ? "✅" : "❌") . " Статус: " . ($httpCode == 200 ? "успешно" : "ошибка") . "\n";
    }
}

function showRecentLogs() {
    global $logPath;

    echo "📋 Последние записи webhook'ов:\n";
    echo "===============================\n\n";

    if (!file_exists($logPath)) {
        echo "❌ Файл логов не найден\n";
        return;
    }

    $command = "tail -n 100 '{$logPath}' | grep -E '(WEBHOOK|webhook)' | tail -n 20";
    $output = shell_exec($command);

    if (empty($output)) {
        echo "ℹ️  Записи webhook'ов не найдены\n";
    } else {
        echo "<pre>" . htmlspecialchars($output) . "</pre>\n";
    }
}

function showErrors() {
    global $logPath;

    echo "❌ Последние ошибки:\n";
    echo "====================\n\n";

    if (!file_exists($logPath)) {
        echo "❌ Файл логов не найден\n";
        return;
    }

    $command = "tail -n 500 '{$logPath}' | grep -E '(ERROR.*webhook|webhook.*ERROR)' -i | tail -n 10";
    $output = shell_exec($command);

    if (empty($output)) {
        echo "✅ Ошибки webhook'ов не найдены!\n";
    } else {
        echo "<pre class='error'>" . htmlspecialchars($output) . "</pre>\n";
    }
}

function clearLogs() {
    global $logPath;

    echo "🧹 Очистка логов:\n";
    echo "=================\n\n";

    if (file_exists($logPath)) {
        $backupPath = $logPath . '.backup.' . date('Y-m-d-H-i-s');
        if (copy($logPath, $backupPath)) {
            echo "💾 Бэкап создан: {$backupPath}\n";
            if (file_put_contents($logPath, '')) {
                echo "✅ Логи очищены\n";
            } else {
                echo "❌ Не удалось очистить логи\n";
            }
        } else {
            echo "❌ Не удалось создать бэкап\n";
        }
    } else {
        echo "ℹ️  Файл логов не найден\n";
    }
}

function showEndpoints() {
    echo "🌐 Доступные webhook endpoint'ы:\n";
    echo "===============================\n\n";

    $endpoints = [
        'Основной Wazzup24 webhook' => [
            'url' => 'https://back-chat.ap.kz/api/webhooks/wazzup24',
            'methods' => 'GET, POST',
            'description' => 'Основной endpoint для webhook\'ов от Wazzup24'
        ],
        'Webhook организации' => [
            'url' => 'https://back-chat.ap.kz/api/webhooks/organization/{slug}',
            'methods' => 'GET, POST',
            'description' => 'Webhook для конкретной организации'
        ]
    ];

    foreach ($endpoints as $name => $info) {
        echo "📍 {$name}:\n";
        echo "   URL: {$info['url']}\n";
        echo "   Методы: {$info['methods']}\n";
        echo "   Описание: {$info['description']}\n\n";
    }

    echo "🔗 Для тестирования используйте:\n";
    echo "   curl -X POST https://back-chat.ap.kz/api/webhooks/wazzup24 -H 'Content-Type: application/json' -d '{\"test\":true}'\n";
}

function showMenu() {
    echo "🔍 Меню мониторинга webhook'ов:\n";
    echo "===============================\n\n";

    if (php_sapi_name() !== 'cli') {
        echo "<div class='box'>";
        echo "<button class='btn' onclick=\"location.href='?action=status'\">📊 Статус</button>";
        echo "<button class='btn' onclick=\"location.href='?action=test'\">🧪 Тест</button>";
        echo "<button class='btn' onclick=\"location.href='?action=logs'\">📋 Логи</button>";
        echo "<button class='btn' onclick=\"location.href='?action=errors'\">❌ Ошибки</button>";
        echo "<button class='btn' onclick=\"location.href='?action=endpoints'\">🌐 Endpoint'ы</button>";
        echo "</div>";
    } else {
        echo "Доступные действия:\n";
        echo "  php webhook_debug.php status    - Показать статус\n";
        echo "  php webhook_debug.php test      - Отправить тест\n";
        echo "  php webhook_debug.php logs      - Показать логи\n";
        echo "  php webhook_debug.php errors    - Показать ошибки\n";
        echo "  php webhook_debug.php endpoints - Показать endpoint'ы\n";
    }
}

function checkEndpoint($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return $httpCode >= 200 && $httpCode < 400;
}

function formatBytes($size, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB'];

    for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
        $size /= 1024;
    }

    return round($size, $precision) . ' ' . $units[$i];
}

if (php_sapi_name() !== 'cli') {
    echo "</body></html>";
}
?>
