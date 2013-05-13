<?
	
	class Admin_Controller extends Base_Controller
	{

		public function action_index()
		{
			$props = array_merge($this->view_array, array('active' => 'admin'));
			return View::make('admin.index', $props);
		}

		public function action_items($action='view')
		{
			if ($action == 'add')
				$this->addItem();

			$items = Item::all();
			$uitems = UniqueItem::all();
			$cats = $this->map_cats(ItemType::all());
			$props = array_merge($this->view_array, array(
				'active' => 'admin',
				'items' => $items,
				'uitems' => $uitems,
				'cats' => $cats
			));

			return View::make('admin.items', $props);
		}

		/**
		 * Maps an array of ItemType objects into an associative array 
		 * with slugs for keys and names for values
		 * @param  array  $cats The array to map
		 * @return arry         An associative array of slugs and names
		 */
		private function map_cats($cats)
		{
			$ret = array();
			for ($i = 0; $i < count($cats); $i++)
			{
				$ret[$cats[$i]->slug] = $cats[$i]->id;
			}

			return $ret;
		}

		/**
		 * Creates a new item and inserts it into the database.
		 */
		private function addItem()
		{
			$item = new UniqueItem;
			$props = Input::get();

			$item->name = $props['itemname'];
			$item->type = $props['itemtype'];
			$item->available = $props['itemavail'];

			$item->save();
		}

	}

?>