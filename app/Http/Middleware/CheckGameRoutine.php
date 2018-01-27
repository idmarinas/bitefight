<?php

namespace App\Http\Middleware;

use Closure;
use Database\Models\ClanApplications;
use Database\Models\ClanRank;
use Database\Models\Message;

class CheckGameRoutine
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
    	$user = user();

    	if($user) {
			$user_new_message_count = Message::where('receiver_id', $user->getId())
				->where('status', Message::STATUS_UNREAD)
				->count();

			view()->share('user_new_message_count', $user_new_message_count);

			$clan_application_count = 0;

			if($user->getClanId() > 0) {
				/**
				 * @var ClanRank $userRank
				 */
				$userRank = ClanRank::where('clan_id', $user->getClanId())
					->first($user->getClanRank());

				if($userRank->hasRightToAddMembers()) {
					$clan_application_count = ClanApplications::where('clan_id', $user->getClanId())
						->count();
				}
			}

			view()->share('clan_application_count', $clan_application_count);

			if(isUserPremiumActivated() && $user->getPremium() < time()) {
				// Premium end, downgrade
				$user->setApMax($user->getApMax() - 60);
			} else if(!isUserPremiumActivated() && $user->getPremium() > time()) {
				// Activated premium, upgrade
				$user->setApMax($user->getApMax() + 60);
			}
		}

		return $next($request);
    }
}
