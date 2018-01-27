<?php

namespace Database\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ClanRank
 * @package App
 *
 * @property int $id
 * @property int $clan_id
 * @property string $rank_name
 * @property bool $read_message
 * @property bool $write_message
 * @property bool $read_clan_message
 * @property bool $add_members
 * @property bool $delete_message
 * @property bool $send_clan_message
 * @property bool $spend_gold
 * @property bool $war_minister
 * @property bool $vocalise_ritual
 */
class ClanRank extends Model
{
    //
}
