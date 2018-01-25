<?php

namespace App\Http\Middleware;

use Closure;

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

    	$user_new_message_count = 0;
    	$clan_application_count = 0;

    	if($user) {
			if(isUserPremiumActivated() && $user->getPremium() < time()) {
				// Premium end, downgrade
				$user->setApMax($user->getApMax() - 60);
			} else if(!isUserPremiumActivated() && $user->getPremium() > time()) {
				// Activated premium, upgrade
				$user->setApMax($user->getApMax() + 60);
			}
		}

		view()->share('user_new_message_count', $user_new_message_count);
		view()->share('clan_application_count', $clan_application_count);

		return $next($request);
    }
}
