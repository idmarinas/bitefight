<?php
/**
 * Created by PhpStorm.
 * User: Osein
 * Date: 1/25/2018
 * Time: 10:21 AM
 */

namespace App\Http\Controllers;

use App\Exceptions\InvalidRequestException;
use Database\Models\Clan;
use Database\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class UserController extends Controller
{

	public function __construct()
	{
		$this->middleware('auth', [
		    'except' => ['getNews', 'getHighscore', 'getPreview']
        ]);
	}

    public function getNews()
    {
        $news = DB::table('news')->get();
        return view('home.news', ['news' => $news]);
    }

	public function getHideout()
	{
		return view('user.hideout');
	}

	public function postHideout()
	{
		$structure = Input::get('structure');
		$week = Input::get('week');
		$hellstoneCost = $week == 4 ? 20 : ($week == 6 ? 69 : 55);
		$duration = $week == 4 ? 2419200 : ($week == 6 ? 3628800 : 7257600);
		$user = user();

		if(in_array( $structure, ['treasure', 'royal', 'gargoyle', 'book'])) {
			if($user->getHellstone() < $hellstoneCost) throw new InvalidRequestException();
			$user->{'h_'.$structure} = $user->{'h_'.$structure} > time() ? $user->{'h_'.$structure} + $duration : time() + $duration;
			$user->setHellstone($user->getHellstone() - $hellstoneCost);
		} else {
			if(!in_array($structure, ['domicile', 'wall', 'land', 'path'])) throw new InvalidRequestException();

			$goldCost = getHideoutCost($structure, $user->{'h_'.$structure});
			if($user->getGold() < $goldCost) throw new InvalidRequestException();
			$user->setGold($user->getGold() - $goldCost);
			if($structure == 'path') $user->setApMax($user->getApMax() + 1);
			$user->{'h_'.$structure}++;
		}

		return redirect(url('/hideout'));
	}

	public function getNotepad()
	{
		$note = DB::table('user_note')->where('user_id', user()->getId())->first();
		return view('user.notepad', ['user_note' => $note?$note->note:'']);
	}

	public function postNotepad()
	{
		$note = Input::get('note');

		$dbNote = DB::table('user_note')->where('user_id', user()->getId())->first();
		if (!$dbNote) {
			DB::table('user_note')->insert([
				'user_id' => user()->getId(),
				'note' => $note
			]);
		} else {
			DB::table('user_note')
				->where('user_id', user()->getId())
				->update(['note' => $note]);
		}

		return redirect(url('/notepad'));
	}

	public function getSearch()
    {
        return view('user.search');
    }

    public function postSearch()
    {
        $searchType = Input::get('searchtyp');
        $search = Input::get('text');
        $exact = Input::get('exakt');

        if ($searchType == 'name') {
            $results = User::select('id', 'name', 'race', 's_booty')
                ->where('name', $exact ? '=' : 'LIKE', $exact ? $search : '%'.$search.'%')
                ->paginate(25);
        } else {
            $results = Clan::select(
                'id', 'name', 'tag', 'stufe', 'race',
                DB::raw('(SELECT SUM(s_booty) FROM users WHERE clan_id = clan.id) AS booty'),
                DB::raw('(SELECT COUNT(1) FROM users WHERE clan_id = clan.id) AS members')
            )->where($searchType == 'clan' ? 'name' : 'tag',
                $exact ? '=' : 'LIKE',
                $exact ? $search : '%'.$search.'%')
                ->paginate(25);
        }

        return view('user.search', [
            'searchType' => $searchType,
            'searchString' => $search,
            'exact' => $exact,
            'results' => $results
        ]);
    }

    public function getHighscore()
    {
        $race = Input::get('race', 0);
        $type = Input::get('type', 'player');
        $page = Input::get('page', 1);
        $order = Input::get('order', 'raid');
        $showArr = Input::get('show', []);

        if ($type == 'player') {
            $showArr = array_slice(
                empty($showArr) ? ['level', 'raid', 'fightvalue'] : $showArr,
                0,
                5
            );

            if (!in_array($order, $showArr))
                $order = in_array('raid', $showArr) ? 'raid' : $showArr[0];

            $result = User::select('name', 'id', 'race');

            if ($race > 0 && $race < 3)
                $result->where('race', $race);

            foreach ($showArr as $show) {
                if ($show == 'level') {
                    $result->addSelect(DB::raw('FLOOR(SQRT(exp / 5)) + 1 AS level'));
                } elseif ($show == 'raid') {
                    $result->addSelect('s_booty AS raid');
                } elseif ($show == 'fightvalue') {
                    $result->addSelect('battle_value AS fightvalue');
                } elseif ($show == 'fights') {
                    $result->addSelect('s_fight AS fights');
                } elseif ($show == 'fight1') {
                    $result->addSelect('s_victory AS fight1');
                } elseif ($show == 'fight2') {
                    $result->addSelect('s_defeat AS fight2');
                } elseif ($show == 'fight0') {
                    $result->addSelect('s_draw AS fight0');
                } elseif ($show == 'goldwin') {
                    $result->addSelect('s_gold_captured AS goldwin');
                } elseif ($show == 'goldlost') {
                    $result->addSelect('s_gold_lost AS goldlost');
                } elseif ($show == 'hits1') {
                    $result->addSelect('s_damage_caused AS hits1');
                } elseif ($show == 'hits2') {
                    $result->addSelect('s_hp_lost AS hits2');
                } elseif ($show == 'trophypoints') {
                    // I dont know what is this lol
                } elseif ($show == 'henchmanlevels') {
                    // I dont know this too, but oh lol well find out later
                }
            }
        } else {
            $showArr = array_slice(
                empty($showArr) ? ['castle', 'raid', 'warraid'] : $showArr,
                0,
                5
            );

            if (!in_array($order, $showArr))
                $order = in_array('raid', $showArr) ? 'raid' : $showArr[0];

            $result = Clan::select('clan.id', 'clan.name', 'clan.tag', 'clan.race')
                ->leftJoin('users', 'users.clan_id', '=', 'clan.id')
                ->groupBy('clan.id');

            if ($race > 0 && $race < 3)
                $result->where('race', $race);

            foreach ($showArr as $show) {
                if ($show == 'castle') {
                    $result->addSelect('clan.stufe AS castle');
                } elseif ($show == 'raid') {
                    $result->addSelect(DB::raw('SUM(users.s_booty) AS raid'));
                } elseif ($show == 'warraid') {
                    $result->addSelect(DB::raw('SUM(users.battle_value) AS warraid'));
                } elseif ($show == 'fights') {
                    $result->addSelect(DB::raw('SUM(users.s_fight) AS fights'));
                } elseif ($show == 'fight1') {
                    $result->addSelect(DB::raw('SUM(users.s_victory) AS fight1'));
                } elseif ($show == 'fight2') {
                    $result->addSelect(DB::raw('SUM(users.s_defeat) AS fight2'));
                } elseif ($show == 'fight0') {
                    $result->addSelect(DB::raw('SUM(users.s_draw) AS fight0'));
                } elseif ($show == 'members') {
                    $result->addSelect(DB::raw('COUNT(1) AS members'));
                } elseif ($show == 'ppm') {
                    // Average booty
                } elseif ($show == 'seals') {
                    // Seals
                } elseif ($show == 'gatesopen') {
                    // Opened gates
                } elseif ($show == 'lastgateopen') {
                    // Last gate opening
                }
            }
        }

        /**
         * @var LengthAwarePaginator $result
         */
        $result = $result->orderBy($order, 'desc')
            ->paginate(50);

        $startRank = ($page - 1) * 50 + 1;

        $vampireCount = User::where('race', 1)->count();
        $werewolfCount = User::where('race', 2)->count();
        $vampireBlood = User::where('race', 1)
            ->select(DB::raw('SUM(s_booty) AS booty'))
            ->first()->booty;
        $werewolfBlood = User::where('race', 2)
            ->select(DB::raw('SUM(s_booty) AS booty'))
            ->first()->booty;
        $vampireBattle = User::where('race', 1)
            ->select(DB::raw('SUM(s_fight) AS fights'))
            ->first()->fights;
        $werewolfBattle = User::where('race', 2)
            ->select(DB::raw('SUM(s_fight) AS fights'))
            ->first()->fights;
        $vampireGold = User::where('race', 1)
            ->select(DB::raw('SUM(gold) AS gold'))
            ->first()->gold;
        $werewolfGold = User::where('race', 2)
            ->select(DB::raw('SUM(gold) AS gold'))
            ->first()->gold;

        $linkExtras_show = '';

        foreach ($showArr as $show) {
            $linkExtras_show .= '&show[]='.$show;
        }

        $myPosLink = urlGetParams('/highscore/mypos', ['type' => $type, 'race' => $race, 'order' => $order]) . $linkExtras_show;
        $showHeadLink = urlGetParams('/highscore', ['type' => $type, 'race' => $race]) . $linkExtras_show;


        return view('user.highscore', [
            'race' => $race,
            'type' => $type,
            'page' => $page,
            'order' => $order,
            'show' => $showArr,
            'results' => $result,
            'startRank' => $startRank,
            'vampireCount' => $vampireCount,
            'werewolfCount' => $werewolfCount,
            'vampireBlood' => $vampireBlood,
            'werewolfBlood' => $werewolfBlood,
            'vampireBattle' => $vampireBattle,
            'werewolfBattle' => $werewolfBattle,
            'vampireGold' => $vampireGold,
            'werewolfGold' => $werewolfGold,
            'myPosLink' => $myPosLink,
            'showHeadLink' => $showHeadLink
        ]);
    }

    public function postHighscoreMyPosition()
    {
        $race = Input::get('race', 0);
        $type = Input::get('type', 'player');
        $page = Input::get('page', 1);
        $order = Input::get('order', 'raid');
        $showArr = Input::get('show', []);

        if($type == 'player') {
            $showArr = array_slice(
                empty($showArr) ? ['level', 'raid', 'fightvalue'] : $showArr,
                0,
                5
            );

            $result = DB::table('users');

            if ($race > 0 && $race < 3)
                $result->where('race', $race);

            foreach ($showArr as $show) {
                if ($show == 'level') {
                    $result = $result->where('exp', '>=', \user()->getExp())->orderBy('exp', 'desc');
                } elseif ($show == 'raid') {
                    $result = $result->where('s_booty', '>=', \user()->getSBooty())->orderBy('s_booty', 'desc');
                } elseif ($show == 'fightvalue') {
                    $result = $result->where('battle_value', '>=', \user()->getBattleValue())->orderBy('battle_value', 'desc');
                } elseif ($show == 'fights') {
                    $result = $result->where('s_fight', '>=', \user()->getSFight())->orderBy('s_fight', 'desc');
                } elseif ($show == 'fight1') {
                    $result = $result->where('s_victory', '>=', \user()->getSVictory())->orderBy('s_victory', 'desc');
                } elseif ($show == 'fight2') {
                    $result = $result->where('s_defeat', '>=', \user()->getSDefeat())->orderBy('s_defeat', 'desc');
                } elseif ($show == 'fight0') {
                    $result = $result->where('s_draw', '>=', \user()->getSDraw())->orderBy('s_draw', 'desc');
                } elseif ($show == 'goldwin') {
                    $result = $result->where('s_gold_captured', '>=', \user()->getSGoldCaptured())->orderBy('s_gold_captured', 'desc');
                } elseif ($show == 'goldlost') {
                    $result = $result->where('s_gold_lost', '>=', \user()->getSGoldLost())->orderBy('s_gold_lost', 'desc');
                } elseif ($show == 'hits1') {
                    $result = $result->where('s_damage_caused', '>=', \user()->getSDamageCaused())->orderBy('s_damage_caused', 'desc');
                } elseif ($show == 'hits2') {
                    $result = $result->where('s_hp_lost', '>=', \user()->getSHpLost())->orderBy('s_hp_lost', 'desc');
                } elseif ($show == 'trophypoints') {
                    // I dont know what is this lol
                } elseif ($show == 'henchmanlevels') {
                    // I dont know this too, but oh lol well find out later
                }
            }

            $resultCount = $result->count();
        } else {
            $showArr = array_slice(
                empty($showArr) ? ['castle', 'raid', 'warraid'] : $showArr,
                0,
                5
            );

            if (!in_array($order, $showArr))
                $order = in_array('raid', $showArr) ? 'raid' : $showArr[0];

            $clanObj = Clan::leftJoin('users', 'users.clan_id', '=', 'clan.id')
                ->groupBy('clan.id')->where('clan.id', \user()->getClanId());

            if ($order == 'castle') {
                $clanObj->select('clan.stufe AS castle');
            } elseif ($order == 'raid') {
                $clanObj->select(DB::raw('SUM(users.s_booty) AS raid'));
            } elseif ($order == 'warraid') {
                $clanObj->select(DB::raw('SUM(users.battle_value) AS warraid'));
            } elseif ($order == 'fights') {
                $clanObj->select(DB::raw('SUM(users.s_fight) AS fights'));
            } elseif ($order == 'fight1') {
                $clanObj->select(DB::raw('SUM(users.s_victory) AS fight1'));
            } elseif ($order == 'fight2') {
                $clanObj->select(DB::raw('SUM(users.s_defeat) AS fight2'));
            } elseif ($order == 'fight0') {
                $clanObj->select(DB::raw('SUM(users.s_draw) AS fight0'));
            } elseif ($order == 'members') {
                $clanObj->select(DB::raw('COUNT(1) AS members'));
            } elseif ($order == 'ppm') {
                // Average booty
            } elseif ($order == 'seals') {
                // Seals
            } elseif ($order == 'gatesopen') {
                // Opened gates
            } elseif ($order == 'lastgateopen') {
                // Last gate opening
            }

            $clanObj = $clanObj->first();

            if($order == 'castle') {
                $countQuery = Clan::where('stufe', '>', $clanObj->castle)
                    ->orWhere(function($q) use($clanObj) {
                        $q->where('stufe', $clanObj->castle);
                        $q->where('id', '>', \user()->getClanId());
                    });
            } else {
                $countQuery = Clan::leftJoin('users', 'users.clan_id', '=', 'clan.id')
                    ->groupBy('clan.id');

                if ($order == 'raid') {
                    $countQuery->havingRaw('SUM(users.s_booty) >= '.$clanObj->{$order});
                } elseif ($order == 'warraid') {
                    $countQuery->havingRaw('SUM(users.battle_value) >= '.$clanObj->{$order});
                } elseif ($order == 'fights') {
                    $countQuery->havingRaw('SUM(users.s_fight) >= '.$clanObj->{$order});
                } elseif ($order == 'fight1') {
                    $countQuery->havingRaw('SUM(users.s_victory) >= '.$clanObj->{$order});
                } elseif ($order == 'fight2') {
                    $countQuery->havingRaw('SUM(users.s_defeat) >= '.$clanObj->{$order});
                } elseif ($order == 'fight0') {
                    $countQuery->havingRaw('SUM(users.s_draw) >= '.$clanObj->{$order});
                } elseif ($order == 'members') {
                    $countQuery->havingRaw('SUM(users.id) >= '.$clanObj->{$order});
                } elseif ($order == 'ppm') {
                    // Average booty
                } elseif ($order == 'seals') {
                    // Seals
                } elseif ($order == 'gatesopen') {
                    // Opened gates
                } elseif ($order == 'lastgateopen') {
                    // Last gate opening
                }
            }

            $resultCount = $countQuery->count();
        }

        $page = ceil($resultCount / 50);

        $linkExtras_show = '';

        foreach ($showArr as $show) {
            $linkExtras_show .= '&show[]='.$show;
        }

        $myPosLink = urlGetParams('/highscore', ['type' => $type, 'race' => $race, 'order' => $order, 'page' => $page]) . $linkExtras_show;

        return redirect($myPosLink);
    }

    public function getPreview($userId)
    {
        $user = User::select(
            'users.*', 'user_description.descriptionHtml', 'clan_rank.rank_name',
            'clan_rank.war_minister', 'clan.logo_sym', 'clan.logo_bg',
            'clan.id AS clan_id', 'clan.name AS clan_name', 'clan.tag AS clan_tag'
        )->leftJoin('clan', 'clan.id', '=', 'users.clan_id')
            ->leftJoin('clan_rank', 'clan_rank.id', '=', 'users.clan_rank')
            ->leftJoin('user_description', 'users.id', '=', 'user_description.user_id')
            ->first();

        if (!$user) {
            throw new InvalidRequestException();
        }

        $stat_max = max($user->str, $user->dex, $user->dex, $user->end, $user->cha);
        $userLevel = getLevel($user->exp);
        $previousLevelExp = getPreviousExpNeeded($userLevel);
        $nextLevelExp = getExpNeeded($userLevel);
        $levelExpDiff = $nextLevelExp - $previousLevelExp;

        return view('user.preview', [
            'puser' => $user,
            'exp_red_long' => ($user->exp - $previousLevelExp) / $levelExpDiff * 400,
            'str_red_long' => $user->str / $stat_max * 400,
            'def_red_long' => $user->def / $stat_max * 400,
            'dex_red_long' => $user->dex / $stat_max * 400,
            'end_red_long' => $user->end / $stat_max * 400,
            'cha_red_long' => $user->cha / $stat_max * 400,
        ]);
    }

}