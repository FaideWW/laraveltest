<?

	class Notify_Controller extends Base_Controller
	{

		public function action_index()
		{
			return Response::error('404');
		}

		public function action_test()
		{
			$this->pushNotification('Warning!', 'This is a notification test.', 'warning');
			$this->pushNotification('Alert!', 'This is another notification test.', 'info');
			$this->pushNotification('Error!', 'Here\'s a third.', 'error');
			$this->pushNotification('Hey!', 'This one is a bit longer so we need to use the longer notation for displaying large messages without breaking the styling on the default notification block.', 'warning', true);
		
			$props = array_merge($this->view_array, array(
				'active' => 'home',
				'notes' => $this->getNotifications()
			));

			return View::make('home.menu', $props);

		}

	}

?>