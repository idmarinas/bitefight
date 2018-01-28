<?php
/**
 * Created by PhpStorm.
 * User: Osein
 * Date: 1/27/2018
 * Time: 7:24 PM
 */

namespace App\Http\Controllers;

class MessageController extends Controller
{

	public function __construct()
	{
		$this->middleware('auth');
	}

	public function getIndex()
	{
		return view('message.index');
	}

}