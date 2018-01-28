<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        //
    ];

	/**
	 * The URIs that should be included for CSRF verification.
	 *
	 * @var array
	 */
    protected $included = [
		'/hunt/human',
		'/clan/hideout/upgrade',
		'/clan/deletemessage',
		'/clan/hideout/upgrade',
		'/clan/clanleave'
	];

	/**
	 * Determine if the request should be checked against csrf attack.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return bool
	 */
	protected function isReading($request)
	{
		foreach($this->included as $url) {
			if(strpos($request->getRequestUri(), $url) !== false) {
				return false;
			}
		}

		return parent::isReading($request);
	}

}
