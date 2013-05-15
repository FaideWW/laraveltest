<?

	class Menu_Controller extends Base_Controller
	{
		/**
		 * Displays the menu items for a given category.  If an invalid category is 
		 * selected, the user is redirected to 'all'.
		 * 
		 * @param  string  $category The category to display (defaults to 'all')
		 */
		public function action_index($category='all')
		{
			$bad_cat = Session::get('badcat');
			$category = str_replace('_', ' ', $category);
			$menu_categories = ItemType::all();
			if ($category == 'all')
				$items = UniqueItem::all();
			else
			{
				$valid_cat = false;
				foreach ($menu_categories as $c)
				{
					if ($c->id == $category)	
						$valid_cat = true;
				}
				if (!$valid_cat)
					return Redirect::to(self::$root_path + 'menu/all')->with('badcat', $category);
				$items = UniqueItem::where('type', '=', $category)->get();
			}

			$menu = $this->createMenuArray($items);

			$props = array_merge($this->view_array, array(
				'active' => 'menu',
				'active_cat' => $category,
				'cats' => $menu_categories,
				'menu' => $menu,
				'items' => $items,
				'redir' => $bad_cat
			));
			return View::make('home.menu', $props);
		}

		/**
		 * Creates an associative array with unique items as keys, and an array of all sizes and prices (from the database) as values
		 * @param  array  $uitems Array of unique item records from UniqueItem
		 * @return array          Associative arary with $uitems as keys, and `Item`s as values 
		 */
		private function createMenuArray($uitems = null)
		{
			//if $uitems was not passed, fetch it
			if ($uitems === null)
				$uitems = UniqueItem::all();

			$menu = array();

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

				$menu[$k] = $v;
			}
			return $menu;
		}

	}

?>