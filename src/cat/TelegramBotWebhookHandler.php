<?php

namespace cat;

require 'vendor/autoload.php';

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Telegram\Bot\Api;
use Dotenv\Dotenv;
use Telegram\Bot\Exceptions\TelegramResponseException;

/**
 * Handles updates from the Telegram webhook and processes messages.
 */
class TelegramBotWebhookHandler
{
    private Api $telegram;
    private mixed $gptApiKey;
    private Client $guzzleClient;
    private ChatHistory $chatHistory;
    private string $prompt_conf;

    /**
     *
     */
    public function __construct()
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();

        $this->telegram = new Api($_ENV['TELEGRAM_BOT_TOKEN']);
        $this->prompt_conf = $_ENV['PROMPT_CONF'];
        $this->gptApiKey = $_ENV['OPENAI_API_KEY'];
        $this->guzzleClient = new Client();
        new Database();
        $this->chatHistory = new ChatHistory();
    }

    /**
     * Processes incoming updates from Telegram, including user messages, and handles chat interactions.
     *
     * @return void
     * @throws GuzzleException
     */
    public function handleUpdate(): void
    {
        try {
            $update = json_decode(file_get_contents('php://input'), true);

            if (isset($update['message'])) {
                $message = $update['message'];
                $chat_id = $message['chat']['id'];
                $user_id = $message['from']['id'];
                $username = $message['from']['username'] ?? 'anonymous';
                $text = $message['text'];
                $reply_to_message = $message['reply_to_message'] ?? null;

                $bot_id = $this->telegram->getMe()->getId();
                if ($user_id !== $bot_id) {

                    if ($reply_to_message) {
                        $replyText = $reply_to_message['text'] ?? '';
                        $text = "Відповідь на: " . $replyText . "\n" . $text;
                    }

                    $this->chatHistory->addMessage($chat_id, $user_id, $username, $text);
                    $lastInteraction = $this->chatHistory->getLastInteraction($chat_id, $user_id);

                    if (
                        (
                            isset($message['entities']) &&
                            in_array('mention', array_column($message['entities'], 'type'))
                        ) ||
                        str_contains($text, 'Марвін') ||
                        str_contains($text, 'Marvin') ||
                        ($lastInteraction && (time() - strtotime($lastInteraction['created_at'])) < 600) ||
                        ($reply_to_message && $reply_to_message['from']['id'] == $bot_id)
                    ) {
                        $delay = rand(1, 5);
                        sleep($delay);

                        $response = $this->getGptResponse($chat_id);
                        $this->telegram->sendMessage([
                            'chat_id' => $chat_id,
                            'text' => trim($response)
                        ]);

                        $this->chatHistory->addMessage($chat_id, $user_id, 'assistant', $response);
                    }
                }
            }
        } catch (TelegramResponseException $e) {
            error_log('Telegram API error: ' . $e->getMessage());
        } catch (Exception $e) {
            error_log('Global error: ' . $e->getMessage());
        }
    }


    /**
     * Retrieves a response from the GPT API based on the chat history.
     *
     * @param int $chatId The ID of the chat for which to fetch the GPT response.
     * @return string The response from the GPT API, or an error message if the request fails.
     * @throws GuzzleException
     */
    private function getGptResponse(int $chatId): string
    {
        $apiUrl = 'https://api.openai.com/v1/chat/completions';
        $headers = [
            'Authorization' => 'Bearer ' . $this->gptApiKey,
            'Content-Type' => 'application/json',
        ];

        // Отримання історії чату за останні 24 години
        $messagesFromDb = $this->chatHistory->getHistory($chatId);

        $messages = [];
        foreach ($messagesFromDb as $message) {
            $messages[] = [
                'role' => $message['username'] === 'assistant' ? 'assistant' : 'user',
                'content' => $message['text']
            ];
        }

        array_unshift($messages, [
            'role' => 'system',
            'content' => $this->prompt_conf,
        ]);

        $body = [
            'model' => 'gpt-4o',
            'messages' => $messages,
            'max_tokens' => 300,
        ];

        try {
            $response = $this->guzzleClient->post($apiUrl, [
                'headers' => $headers,
                'json' => $body,
            ]);

            $data = json_decode($response->getBody(), true);
            error_log(print_r($data, true));

            return $data['choices'][0]['message']['content'] ?? 'Не вдалось отримати відповідь від GPT';
        } catch (Exception $e) {
            error_log('GPT API error: ' . $e->getMessage());
            return 'Не вдалось отримати відповідь від GPT';
        }
    }


}