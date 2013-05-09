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
			$menu_categories = DB::table('item_types')->get();
			if ($category == 'all')
				$items = DB::table('items')->get();
			else
			{
				$valid_cat = false;
				foreach ($menu_categories as $c)
				{
					if ($c->id == $category)	
						$valid_cat = true;
				}
				if (!$valid_cat)
					return Redirect::to('menu/all')->with('badcat', $category);
				$items = DB::table('items')->where('type', '=', $category)->get();
			}
			return View::make('home.menu', array(
				'navigation' => self::$navigation,
				'active' => 'menu',
				'active_cat' => $category,
				'cats' => $menu_categories,
				'items' => $items,
				'redir' => $bad_cat
			));
		}

	}

?>