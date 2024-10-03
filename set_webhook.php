<?php

require 'vendor/autoload.php';

use Telegram\Bot\Api;
use Dotenv\Dotenv;

// Завантаження змінних із .env файлу
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$telegram = new Api($_ENV['TELEGRAM_BOT_TOKEN']);

// Встановлення вебхука
$webhookUrl = $_ENV['TELEGRAM_WEBHOOK_URL'];
$response = $telegram->setWebhook(['url' => $webhookUrl]);

// Перевірка відповіді
if ($response) {
    echo "Webhook встановлено успішно!";
} else {
    echo "Не вдалося встановити webhook!";
}
