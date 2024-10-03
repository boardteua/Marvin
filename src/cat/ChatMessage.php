<?php

namespace cat;

use Illuminate\Database\Eloquent\Model;

/**
 * This class represents a chat message within the system.
 * Chat messages are stored in the 'chat_messages' table.
 * The timestamps for this model are disabled.
 *
 * The following attributes can be mass assigned:
 * - chat_id: The ID of the chat this message belongs to.
 * - user_id: The ID of the user who sent the message.
 * - username: The username of the user who sent the message.
 * - text: The content of the chat message.
 * - created_at: The timestamp indicating when the message was created.
 */
class ChatMessage extends Model
{
    protected $table = 'chat_messages';
    public $timestamps = false;
    protected $fillable = [
        'chat_id', 'user_id', 'username', 'text', 'created_at'
    ];
}