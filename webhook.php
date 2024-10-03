<?php
require 'vendor/autoload.php';

use cat\TelegramBotWebhookHandler;

$handler = new TelegramBotWebhookHandler();
$handler->handleUpdate();