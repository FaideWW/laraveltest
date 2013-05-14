<?
	
	class Admin_Controller extends Base_Controller
	{

		//----------- VIEWS ----------------

		public function action_index()
		{
			$props = array_merge($this->view_array, array('active' => 'admin'));
			return View::make('admin.index', $props);
		}

		public function action_items($action='view')
		{
			if ($action == 'add')
				$this->addItem();
			else if ($action == 'delete')
				$this->deleteItem();

			$items = Item::all();
			$uitems = UniqueItem::all();
			$cats = $this->map_cats(ItemType::all());
			$props = array_merge($this->view_array, array(
				'active' => 'admin',
				'items' => $items,
				'uitems' => $uitems,
				'cats' => $cats
			));

			//custom form macros for required fields
			Form::macro('item_name', function()
			{
				return '<input type="text" name="itemname"= id="itemname" placeholder="Dumplings" required>';
			});
			Form::macro('item_price', function()
			{
				return '<input type="text" name="itemprice" placeholder="0.00" id="itemprice" required>';
			});

			return View::make('admin.items', $props);
		}


		//--------- END VIEWS --------------



		/**
		 * Maps an array of ItemType objects into an associative array 
		 * with slugs for keys and names for values
		 * @param  array  $cats The array to map
		 * @return array        An associative array of slugs and names
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
			$props = Input::get();

			//check if item already exists
			$uitem = UniqueItem::where_name($props['itemname'])->first();

			if ($uitem == null)
			{
				//if not, create it
				$uitem = new UniqueItem;
				$uitem->name = $props['itemname'];
				$uitem->type = $props['itemtype'];
				$uitem->available = $props['itemavail'];
				$uitem->save();
			}

			//check if the size exists
			$item = Item::where_unique_id($uitem->id)->where_size($props['itemsize'])->first();

			if ($item == null)
			{
				//if it doesn't, create it
				$item = new Item;
				$item->unique_id = $uitem->id;
				$item->price = $props['itemprice'];
				$item->size = $props['itemsize'];
			}
			else  //if not, update the price
				$item->price = $props['itemprice'];
			
			$item->save();
		}

		/**
		 * Removes an item from the database
		 */
		private function deleteItem()
		{
			$itemid = Input::get('itemid', 'none');
			if ($itemid == 'none')
				return;

			
			$item = Item::find($itemid);
			$uid = $item->unique_id;
			$item->delete();


			$others = Item::where_unique_id($uid)->get();
			$uitem = UniqueItem::find($uid);
			//check if there are other items of the same unique_id
			//if not, delete it from `uniqueitems`
			if (empty($others))
				$uitem->delete();

		}

	}

?>