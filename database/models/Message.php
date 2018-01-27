<?php

namespace Database\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Message
 * @package App
 *
 * @property int $id
 * @property int $sender_id
 * @property int $receiver_id
 * @property int $folder_id
 * @property int $type
 * @property string $subject
 * @property string $message
 * @property int $status
 * @property int $send_time
 */
class Message extends Model
{
    //
}
