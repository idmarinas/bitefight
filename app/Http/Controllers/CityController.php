<?php
/**
 * Created by PhpStorm.
 * User: Osein
 * Date: 1/22/2018
 * Time: 10:41 PM
 */

namespace App\Http\Controllers;

use App\Exceptions\InvalidRequestException;
use Database\Models\UserActivity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class CityController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth');
	}

	public function getIndex()
	{
		return view('city.index');
	}

	public function getChurch()
	{
		$activity = UserActivity::where('activity_type', UserActivity::ACTIVITY_TYPE_CHURCH)
			->where('user_id', user()->getId())
			->first();

		$delta = max($activity?$activity->end_time - time():0, 0);
		$usedTimes = ceil($delta / 3600);

		return view('city.church', [
			'delta' => $delta,
			'usedTimes' => $usedTimes,
			'requiredAp' => 5 * pow(2, $usedTimes)
		]);
	}

	public function postChurch()
	{
		$user = user();

		if($user->getHpNow() == $user->getHpMax())
			throw new InvalidRequestException();

		$activity = UserActivity::where('activity_type', UserActivity::ACTIVITY_TYPE_CHURCH)
			->where('user_id', user()->getId())
			->first();

		$delta = max($activity?$activity->end_time - time():0, 0);
		$usedTimes = ceil($delta / 3600);
		$requiredAp = 5 * pow(2, $usedTimes);

		if($user->getApNow() < $requiredAp)
			throw new InvalidRequestException();

		$time = time();

		if(!$activity) {
			$activity = new UserActivity;
			$activity->setUserId($user->getId());
			$activity->setActivityType(UserActivity::ACTIVITY_TYPE_CHURCH);
			$activity->setEndTime(0);
			$activity->setStartTime($time);
		}

		if($activity->getEndTime() < $time) {
			$activity->setEndTime($time + 3600);
		} else {
			$activity->setEndTime($activity->getEndTime() + 3600);
		}

		$activity->save();

		$user->setHpNow($user->getHpMax());
		$user->setApNow($user->getApNow() - $requiredAp);

		session()->flash('churchUsed', true);
		return redirect(url('/city/church'));
	}

	public function getGraveyard()
	{
		$activity = UserActivity::where('activity_type', UserActivity::ACTIVITY_TYPE_GRAVEYARD)
			->where('user_id', user()->getId())
			->first();

		return view('city.graveyard', [
			'working' => $activity && $activity->end_time > time(),
			'end_time' => $activity ? $activity->end_time : 0,
			'work_rank' => $this->getGraveyardRank(user()),
			'bonus_gold' => $this->getBonusGraveyardGold(user())
		]);
	}

	public function postGraveyard()
	{
		$duration = Input::get('workDuration');

		if ($duration < 1 || $duration > 8)
			throw new InvalidRequestException();

		$activity = UserActivity::where('activity_type', UserActivity::ACTIVITY_TYPE_GRAVEYARD)
			->where('user_id', user()->getId())
			->first();
		$time = time();

		if (!$activity) {
			$activity = new UserActivity;
			$activity->setUserId(user()->getId());
			$activity->setActivityType(UserActivity::ACTIVITY_TYPE_GRAVEYARD);
		}

		$activity->setStartTime($time);
		$activity->setEndTime($time + $duration * 900);
		$activity->save();
		return redirect(url('/city/graveyard'));
	}

	public function postGraveyardCancel()
	{
		$activity = UserActivity::where('activity_type', UserActivity::ACTIVITY_TYPE_GRAVEYARD)
			->where('user_id', user()->getId())
			->first();

		if(!$activity)
			throw new InvalidRequestException();

		$activity->setEndTime($activity->getStartTime() - 1);
		$activity->save();
		return redirect(url('/city/graveyard'));
	}

	public function getVoodoo()
	{
		return view('city.voodoo');
	}

	public function postVoodoo()
	{
		$user = user();

		if(!empty(Input::get('buy_methamorphosis'))) {
			if($user->getHellstone() < 50) {
				throw new InvalidRequestException();
			}

			$user->setHellstone($user->getHellstone() - 50);
			$user->setRace(0);

			if($user->getClanId() > 0) {
				if($user->getClanRank() == 1) {
					DB::transaction(function() {
						$user = user();

						DB::table('clan')->delete($user->getClanId());
						DB::table('clan_applications')->where('clan_id', $user->getClanId())->delete();
						DB::table('clan_description')->where('clan_id', $user->getClanId())->delete();
						DB::table('clan_donations')->where('clan_id', $user->getClanId())->delete();
						DB::table('clan_message')->where('clan_id', $user->getClanId())->delete();
						DB::table('clan_rank')->where('clan_id', $user->getClanId())->delete();
						DB::table('user')->where('clan_id', $user->getClanId())->update(['clan_id' => 0, 'clan_rank' => 0]);
					});
				}

				$user->setClanId(0);
			}
		}

		if(!empty(Input::get('buy_shadow_lord'))) {
			if($user->getHellstone() < 15) {
				throw new InvalidRequestException();
			}

			$user->setHellstone($user->getHellstone() - 15);
			$time = time();
			$user->setPremium(($user->getPremium() > $time ? $user->getPremium() : time()) + 1209600);
		}

		return redirect(url('/voodoo'));
	}
}