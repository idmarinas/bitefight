<?php
/**
 * Created by PhpStorm.
 * User: Osein
 * Date: 1/25/2018
 * Time: 10:21 AM
 */

namespace App\Http\Controllers;

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

}