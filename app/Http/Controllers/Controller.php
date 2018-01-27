<?php

namespace App\Http\Controllers;

use Database\Models\User;
use Database\Models\UserTalent;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	public function getNews()
	{
		$news = DB::table('news')->get();
		return view('home.news', ['news' => $news]);
	}

	public function getHighscore()
	{

	}

	/**
	 * @param User $user
	 * @return string
	 */
	public function getGraveyardRank($user) {
		$level = getLevel($user->getExp());

		if ($level < 10) {
			return __('city.city_graveyard_gravedigger');
		} elseif ($level < 25) {
			return __('city.city_graveyard_graveyard_gardener');
		} elseif ($level < 55) {
			return __('city.city_graveyard_corpse_predator');
		} elseif ($level < 105) {
			return __('city.city_graveyard_graveyard_guard');
		} elseif ($level < 195) {
			return __('city.city_graveyard_employee_manager');
		} elseif ($level < 335) {
			return __('city.city_graveyard_tombstone_designer');
		} elseif ($level < 511) {
			return __('city.city_graveyard_crypt_designer');
		} elseif ($level < 1024) {
			return __('city.city_graveyard_graveyard_manager');
		} else {
			return __('city.city_graveyard_graveyard_master');
		}
	}

	/**
	 * @param User $user
	 * @return int
	 */
	public function getBonusGraveyardGold($user)
	{
		$userTalentStr = UserTalent::leftJoin('talents', 'talents.id', '=', 'user_talents.talent_id')
			->select(DB::raw('SUM(talents.str) as totalTalentStr'))
			->where('user_talents.user_id', $user->getId())
			->first();

		$userTotalStr = $user->getStr() + $userTalentStr->totalTalentStr;

		$bonusWithStr = $userTotalStr * 0.5;
		$level = getLevel($user->getExp());

		if ($level > 19) {
			$bonusWithStr = $userTotalStr * 2;
		} elseif ($level > 14) {
			$bonusWithStr = $userTotalStr * 1.5;
		} elseif ($level > 4) {
			$bonusWithStr = $userTotalStr * 1;
		}

		$bonusWithLevel = ($level * (0.1035 * $level));

		return ceil($bonusWithLevel + $bonusWithStr);
	}
}
