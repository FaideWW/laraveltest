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
			$props = array_merge($this->view_array, array(
				'active' => 'menu',
				'active_cat' => $category,
				'cats' => $menu_categories,
				'items' => $items,
				'redir' => $bad_cat
			));
			return View::make('home.menu', $props);
		}

	}

?>