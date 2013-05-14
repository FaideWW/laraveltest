<?php

class Base_Controller extends Controller {


	protected static $navigation = array(
		array(
			'url' => 'home',
			'name' => 'Home',
		),
		array(
			'url' => 'about',
			'name' => 'About',
		),
		array(
			'url' => 'menu',
			'name' => 'Menu',
		),
	);

	protected static $root_path = 'http://www.fluffypuffgruff.com/laravel_test/';


	public function __construct()
	{
		//load js and css assets
		Asset::add('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js');
		Asset::add('bootstrap-js', 'js/bootstrap.min.js');
		Asset::add('bootstrap-filestyle-js', 'js/bootstrap-filestyle.min.js');
    Asset::add('bootstrap-css', 'css/bootstrap.min.css');
    Asset::add('style', 'css/style.css');
    Asset::add('bootstrap-css-responsive', 'css/bootstrap-responsive.min.css', 'bootstrap-css');

    //initialize the view default parameters
		$this->view_array = array(
			'root_path' => self::$root_path,
			'navigation' => self::$navigation,
			'active' => 'home'
		);
    parent::__construct();
	}

	/**
	 * Catch-all method for requests that can't be matched.
	 *
	 * @param  string    $method
	 * @param  array     $parameters
	 * @return Response
	 */
	public function __call($method, $parameters)
	{
		return Response::error('404');
	}

	/**
	 * Logs thes current controller's action and timestamp
	 * @return none
	 */
	public function logRequest()
	{
		$route = Request::route();
		Log::log('request', "Controller: {$route->controller} / Action: {$route->controller_action} called at ". date('Y-m-d H:i:s'));
	}
}