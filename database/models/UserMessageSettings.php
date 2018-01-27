<?php

namespace Database\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UserMessageSettings
 * @package App
 *
 * @property int id
 * @property int user_id
 * @property string setting
 * @property int folder_id
 * @property bool mark_read
 */
class UserMessageSettings extends Model
{
	/**
	 * @return int
	 */
	public function getId(): int
	{
		return $this->id;
	}

	/**
	 * @param int $id
	 * @return UserMessageSettings
	 */
	public function setId(int $id): UserMessageSettings
	{
		$this->id = $id;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getUserId(): int
	{
		return $this->user_id;
	}

	/**
	 * @param int $user_id
	 * @return UserMessageSettings
	 */
	public function setUserId(int $user_id): UserMessageSettings
	{
		$this->user_id = $user_id;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSetting(): string
	{
		return $this->setting;
	}

	/**
	 * @param string $setting
	 * @return UserMessageSettings
	 */
	public function setSetting(string $setting): UserMessageSettings
	{
		$this->setting = $setting;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getFolderId(): int
	{
		return $this->folder_id;
	}

	/**
	 * @param int $folder_id
	 * @return UserMessageSettings
	 */
	public function setFolderId(int $folder_id): UserMessageSettings
	{
		$this->folder_id = $folder_id;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isMarkRead(): bool
	{
		return $this->mark_read;
	}

	/**
	 * @param bool $mark_read
	 * @return UserMessageSettings
	 */
	public function setMarkRead(bool $mark_read): UserMessageSettings
	{
		$this->mark_read = $mark_read;
		return $this;
	}

}
