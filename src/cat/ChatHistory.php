<?php

namespace cat;

use cat\ChatMessage as ChatMessage;

/**
 * Class responsible for managing chat messages in a chat history.
 */
class ChatHistory
{
    /**
     * Adds a message to the chat if it does not already exist.
     *
     * @param int $chatId The ID of the chat to which the message should be added.
     * @param int $userId The ID of the user sending the message.
     * @param string $username The username of the user sending the message.
     * @param string $text The text content of the message.
     * @return void
     */
    public function addMessage(int $chatId, int $userId, string $username, string $text): void
    {
        // Перевірка на існування дублікату
        $exists = ChatMessage::where('chat_id', $chatId)
            ->where('user_id', $userId)
            ->where('text', $text)
            ->exists();
        if (!$exists) {
            ChatMessage::create([
                'chat_id' => $chatId,
                'user_id' => $userId,
                'username' => $username,
                'text' => $text,
            ]);
        }
    }

    /**
     * Retrieves the chat history for a given chat ID since a specified time.
     *
     * @param int $chatId The ID of the chat whose history is to be retrieved.
     * @param string $since The time from which to retrieve messages. Defaults to '24 hours ago'.
     * @return array The chat messages retrieved since the specified time.
     */
    public function getHistory(int $chatId, string $since = '24 hours ago'): array
    {
        return ChatMessage::where('chat_id', $chatId)
            ->where('created_at', '>=', date('Y-m-d H:i:s', strtotime($since)))
            ->get()->toArray();
    }

    public function clearHistory(): void
    {
        ChatMessage::truncate();
    }

    public function formatMessagesForGpt($messages)
    {
        $formattedMessages = [];
        foreach ($messages as $message) {
            $formattedMessages[] = [
                'role' => $message['username'] === 'assistant' ? 'assistant' : 'user',
                'content' => $message['text']
            ];
        }
        return $formattedMessages;
    }
}