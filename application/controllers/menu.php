<?

	class Menu_Controller extends Base_Controller
	{
		/**
		 * Displays the menu items for a given category.  If an invalid category is 
		 * selected, the user is redirected to 'all'.
		 * 
		 * @param  string  $category The category to display (defaults to 'all')
		 */
		public function action_index()
		{
			$cats = ItemType::all();

			$menu = $this->createMenuArray($cats);

			$props = array_merge($this->view_array, array(
				'active' => 'menu',
				'menu' => $menu
			));
			return View::make('home.menu', $props);
		}

		public function action_addfav()
		{
			$item = explode('_', Input::get('itemname'));
			$uitemid = UniqueItem::where_name($item[0])->first()->id;
			$itemid = Item::where_unique_id($uitemid)->where_size($item[1])->first()->id;
			$fav = new Favorite;
			$userid = Auth::user()->id;
			$fav->user_id = $userid;
			$fav->item_id = $itemid;
			$fav->save();
			return $this->action_index();
		}

		public function action_unfav()
		{
			$item = explode('_', Input::get('itemname'));
			$uitemid = UniqueItem::where_name($item[0])->first()->id;
			$itemid = Item::where_unique_id($uitemid)->where_size($item[1])->first()->id;
			$userid = Auth::user()->id;
			$fav = Favorite::where_user_id($userid)->where_item_id($itemid)->first();
			if ($fav != null)
				$fav->delete();
			return $this->action_index();
		}


		/**
		 * Creates an associative array with cats and unique items as keys, and an array of all sizes and prices (from the database) as values
		 * @param  array  $cats   Array of category records from ItemType
		 * @return array          Nested associative array of signature ($cats => ($uitems => ($size => $price)))
		 */
		private function createMenuArray($cats = null)
		{
			//if $cats was not passed, fetch them
			if ($cats === null)
				$cats = ItemType::all();

			$menu = array();

			//if user is logged in, add a favorites option
			if (Auth::check())
			{
				$fav_array = array();

				foreach(Favorite::where_user_id(Auth::user()->id)->get() as $fav)
				{
					$item = Item::find($fav->item_id);
					$uitem = UniqueItem::find($item->unique_id);
					$k = $uitem->name;
					$v[$item->size] = $item->price;
					$fav_array[$k] = $v;
				}

				$menu['favorites'] = $fav_array;
			}

			foreach ($cats as $cat)
			{
				$cat_array = array();
				$uitems = $cat->items;
				foreach ($uitems as $uitem)
				{
					$k = $uitem->name;
					$items = Item::where_unique_id($uitem->id)->get();
					$v = array();
					if (empty($items))
						continue;
					foreach ($items as $sku)
					{
						$v[$sku->size] = $sku->price;
					}

					$cat_array[$k] = $v;
				}
				$menu[$cat->id] = $cat_array;
			}

			return $menu;
		}

	}

?>