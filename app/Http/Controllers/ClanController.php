<?php
/**
 * Created by PhpStorm.
 * User: Osein
 * Date: 1/27/2018
 * Time: 7:05 PM
 */

namespace App\Http\Controllers;

use App\Exceptions\InvalidRequestException;
use Database\Models\Clan;
use Database\Models\ClanDescription;
use Database\Models\ClanDonations;
use Database\Models\ClanMessages;
use Database\Models\ClanRank;
use Database\Models\Message;
use Database\Models\User;
use Database\Models\UserMessageSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class ClanController extends Controller
{

	public function __construct()
	{
		$this->middleware('auth');
	}

	public function getIndex()
	{
		$data = [];

		if (user()->getClanId()) {
			$data['clan'] = Clan::leftJoin('clan_description', 'clan.id', '=', 'clan_description.clan_id')->find(user()->getClanId());

			$data['rank'] = ClanRank::find(user()->getClanRank());

			$tb = User::where('clan_id', \user()->getClanId())
				->select(DB::raw('SUM(s_booty) AS total_booty'))
				->first();

			$data['totalBlood'] = $tb ? $tb->total_booty : 0;

			$data['member_count'] = User::where('clan_id', \user()->getClanId())
				->count();

			if ($data['rank']->read_message) {
				$data['clan_messages'] = ClanMessages::where('clan_message.clan_id', \user()->getClanId())
					->leftJoin('users', 'users.id', '=', 'clan_message.user_id')
					->leftJoin('clan_rank', 'users.clan_rank', '=', 'clan_rank.id')
					->select(DB::raw('users.name, clan_message.*, clan_rank.rank_name'))
					->get();
			}
		}

		return view('clan.index', $data);
	}

	public function getCreate()
	{
		UserMessageSettings::getUserSetting('asd');
		return view('clan.create');
	}

	public function postCreate(Request $request)
	{
		$name = Input::get('name');
		$tag = Input::get('tag');

		$this->validate($request, [
			'name' => 'required|string|min:2|unique:clan',
			'tag' => 'required|string|min:2|unique:clan'
		]);

		$clan = new Clan;
		$clan->setName($name);
		$clan->setTag($tag);
		$clan->setRace(user()->getRace());
		$clan->setFoundDate(time());
		$clan->save();

		$msgSetting = UserMessageSettings::getUserSetting(UserMessageSettings::CLAN_FOUNDED);

		if($msgSetting->getFolderId() != UserMessageSettings::FOLDER_ID_DELETE_IMMEDIATELY) {
			$msg = new Message;
			$msg->setSenderId(Message::SENDER_SYSTEM);
			$msg->setReceiverId(user()->getId());
			$msg->setFolderId($msgSetting->getFolderId());
			$msg->setSubject('Clan information');
			$msg->setMessage('Your clan has been founded: '.$name.' ['.$tag.']');
			$msg->setStatus($msgSetting->isMarkRead() ? 2 : 1);
			$msg->save();
		}

		user()->setClanId($clan->getId());
		user()->setClanRank(1);

		return redirect(url('/clan/index'));
	}

	public function postNewMessage()
	{
		$message = Input::get('message');

		/**
		 * @var ClanRank $rank
		 */
		$rank = ClanRank::find(\user()->getClanRank());

		if(strlen($message) > 2000 || !$rank->isWriteMessage())
			throw new InvalidRequestException();

		$msg = new ClanMessages;
		$msg->setClanId(\user()->getClanId());
		$msg->setUserId(\user()->getId());
		$msg->setClanMessage($message);
		$msg->setMessageTime(time());
		$msg->save();

		return redirect(url('/clan/index'));
	}

	public function postDeleteMessage()
	{
		/**
		 * @var ClanRank $rank
		 */
		$rank = ClanRank::find(\user()->getClanRank());
		$message = Input::get('message_id', 0);

		if(!$rank->isDeleteMessage())
			throw new InvalidRequestException();

		ClanMessages::where('clan_id', \user()->getClanId())
			->delete($message);

		return redirect(url('/clan/index'));
	}

	public function postDonate()
	{
		$amount = Input::get('donation', 0);

		if($amount == 0 || $amount > \user()->getGold())
			throw new InvalidRequestException();

		DB::table('clan')->where('id', \user()->getClanId())->update([
			'capital' => DB::raw('capital + '.$amount)
		]);

		user()->setGold(\user()->getGold() - $amount);

		$donation = new ClanDonations;
		$donation->setUserId(\user()->getId());
		$donation->setClanId(\user()->getClanId());
		$donation->setDonationAmount($amount);
		$donation->setDonationTime(time());
		$donation->save();

		return redirect(url('/clan/index'));
	}

	public function postHideoutUpgrade()
	{
		/**
		 * @var ClanRank $rank
		 */
		$rank = ClanRank::find(\user()->getClanRank());

		if(!$rank->isSpendGold())
			throw new InvalidRequestException();


		$clan = Clan::find(\user()->getClanId());
		$hideoutCost = getClanHideoutCost($clan->getStufe() + 1);

		if($clan->getCapital() < $hideoutCost)
			throw new InvalidRequestException();

		$clan->setCapital($clan->getCapital() - $hideoutCost);
		$clan->setStufe($clan->getStufe() + 1);
		$clan->save();

		return redirect(url('/clan/index'));
	}

	public function getLogoBackground()
	{
		if(\user()->getClanRank() != 1 && \user()->getClanRank() != 2)
			throw new InvalidRequestException();

		return view('clan.logo', ['type' => 'background', 'clan' => Clan::find(\user()->getClanId())]);
	}

	public function getLogoSymbol()
	{
		if(\user()->getClanRank() != 1 && \user()->getClanRank() != 2)
			throw new InvalidRequestException();

		return view('clan.logo', ['type' => 'symbol', 'clan' => Clan::find(\user()->getClanId())]);
	}

	public function postLogoBackground()
	{
		if(\user()->getClanRank() != 1 && \user()->getClanRank() != 2)
			throw new InvalidRequestException();

		$background = Input::get('bg', 1);

		Clan::where('id', \user()->getClanId())->update([
			'logo_bg' => $background
		]);

		return redirect(url('/clan/logo/background'));
	}

	public function postLogoSymbol()
	{
		if(\user()->getClanRank() != 1 && \user()->getClanRank() != 2)
			throw new InvalidRequestException();

		$sym = Input::get('symbol', 1);

		Clan::where('id', \user()->getClanId())->update([
			'logo_sym' => $sym
		]);

		return redirect(url('/clan/logo/symbol'));
	}

	public function getMemberList()
	{
		$clan = Clan::find(\user()->getClanId());
		$order = Input::get('order', 'exp');
		$type = Input::get('type', 'desc');

		if(!$clan)
			throw new InvalidRequestException();

		$users = User::select(DB::raw('users.*, clan_rank.rank_name'))
			->leftJoin('clan_rank', 'users.clan_rank', '=', 'clan_rank.id')
			->where('users.clan_id', \user()->getClanId());

		if($order == 'name') {
			$order = 'users.name';
		} elseif($order == 'level') {
			$order = 'users.exp';
		} elseif($order == 'rank') {
			$order = 'clan_rank.rank_name';
		} elseif($order == 'res1') {
			$order = 'users.s_booty';
		} elseif($order == 'goldwon') {
			$order = 'users.s_gold_captured';
		} elseif($order == 'goldlost') {
			$order = 'users.s_gold_lost';
		} elseif($order == 'status') {
			$order = 'users.last_activity';
		}

		if($type == 'desc') {
			$users = $users->orderByDesc($order);
		} else {
			$users = $users->orderByAsc($order);
		}

		$members = $users
			->get();

		return view('clan.memberlist', [
			'order' => $order,
			'type' => $type,
			'members' => $members,
			'clan' => $clan
		]);
	}

	public function getDescription()
	{
		if(\user()->getClanRank() != 1 && \user()->getClanRank() != 2)
			throw new InvalidRequestException();

		/**
		 * @var ClanDescription $desc
		 */
		$desc = ClanDescription::where('clan_id', \user()->getClanId())->first();
		return view('clan.description', ['description' => $desc ? $desc->getDescription() : '']);
	}

	public function postDescription()
	{
		if(\user()->getClanRank() != 1 && \user()->getClanRank() != 2)
			throw new InvalidRequestException();

		$save = Input::get('save');
		$desc = Input::get('description');

		if($save) {
			$description = ClanDescription::where('clan_id', \user()->getClanId())->first();

			if(!$description) {
				$description = new ClanDescription;
				$description->setClanId(\user()->getClanId());
			}

			$description->setDescription($desc);
			$description->setDescriptionHtml(parseBBCodes($desc));
			$description->save();
		} else {
			ClanDescription::where('clan_id', \user()->getClanId())->delete();
		}

		return redirect(url('/clan/description'));
	}
}