<?
	
	class Admin_Controller extends Base_Controller
	{

		public function action_index()
		{
			$props = array_merge($this->view_array, array('active' => 'admin'));
			return View::make('admin.index', $props);
		}

		public function action_items()
		{
			$items = Item::all();
			$props = array_merge($this->view_array, array(
				'active' => 'admin',
				'items' => $items
			));

			return View::make('admin.items', $props);
		}

	}

?>