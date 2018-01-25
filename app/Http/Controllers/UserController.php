<?php
/**
 * Created by PhpStorm.
 * User: Osein
 * Date: 1/25/2018
 * Time: 10:21 AM
 */

namespace App\Http\Controllers;

use App\Exceptions\InvalidRequestException;
use Illuminate\Support\Facades\Input;

class UserController extends Controller
{

	public function __construct()
	{
		$this->middleware('auth');
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

}