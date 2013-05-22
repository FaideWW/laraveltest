<?php

class Base_Controller extends Controller {

	private $notificationQueue = array();

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
		Asset::add('jquery-contenthover-js', 'js/jquery.contenthover.min.js');
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
	 * Adds a message (alert, warning, error) to the notification queue, which will be displayed in the next view.
	 * @param  string $title    A text encoded string that represents the 
	 * @param  string $message  An HTML encoded string to display as the body of the message.
	 * @param  string $severity The level of notification which describes the styling (error, success, info)
	 * @param  bool   $long     Whether or not the alert should be formatted as a long message type (alert-block)
	 */
	public function pushNotification($title, $message, $severity, $long=false)
	{
		array_push($this->notificationQueue, array('title' => $title, 'message' => $message, 'severity' => $severity, 'isLong' => $long));
	}

	/**
	 * Returns the notification queue and emptys it
	 * @return array The notifications to be displayed on the next view rendering.
	 */
	public function getNotifications()
	{
		$ret = $this->notificationQueue;
		$this->notificationQueue = array();
		return $ret;

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