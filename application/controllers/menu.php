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

			//check for POST data
			$action = Input::get('form_action');
			
			if (Auth::check())
			{
				if ($action == 'addfav')
					$this->arrayPushNotification($this->action_addfav());
				else if ($action == 'delfav')
					$this->arrayPushNotification($this->action_unfav());
			}

			if (Session::has('notification-error'))
				$this->pushNotification('', Session::get('notification-error'), 'error');

			$cats = ItemType::all();

			$menu = $this->createMenuArray($cats);

			$cart = json_encode(Session::get('cart'));

			$props = array_merge($this->view_array, array(
				'active' => 'menu',
				'menu' => $menu, 
				'cart' => $cart,
				'notes' => $this->getNotifications()
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
			
			return array(
				'title' => 'Success!',
				'message' => $item[0] . ' has been added to your favorites.',
				'severity' => 'success'
			);

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
						
			return array(
				'title' => 'Success!',
				'message' => $item[0] . ' has been removed from your favorites.',
				'severity' => 'success'
			);
		}

		public function action_savecart()
		{
			if (!Request::ajax())
				return Redirect::to_action('menu@index');
			$cart = Input::get();
			Session::put('cart', $cart);
			return Response::json($cart);
		}

		public function action_unsavecart()
		{
			if (!Request::ajax())
				return Redirect::to_action('menu@index');
			Session::forget('cart');
			return Response::json("{status:'success'}");
		}

		public function action_checkout($cart=null)
		{
			if ($cart === null and !Session::has('cart'))
				return Redirect::to_action('menu@index');

			if ($cart === null)
				$cart = Session::get('cart');
			$parsed_cart = json_decode($cart['cart']);
			$finalcart = array();
			foreach($parsed_cart as $item => $itemprops)
			{
				$itemname = $itemprops->name;
				$uniqueitem = UniqueItem::where_name($itemname)->first();
				$item = Item::where_unique_id($uniqueitem->id)->where_size($itemprops->size)->first();
				if ($item != null)
				{
					//format item for cart display
					
					$fitem = array(
						'id' => $item->id,
						'name' => $uniqueitem->name,
						'size' => $item->size,
						'price' => $item->price,
						'quantity' => $itemprops->quantity
					);
					$finalcart[] = $fitem;
				}
			}

			$props = array_merge($this->view_array, array(
				'active' => 'menu',
				'finalcart' => $finalcart,
				'jsoncart' => $cart['cart'],
				'notes' => $this->getNotifications()
			));

			return View::make('home.checkout', $props);
		}

		public function action_checkout_confirm()
		{
			if (!Session::has('cart'))
				return Redirect::to_action('menu@index')->with('notification-error', 'Your session has expired.  Please re-create your order.');

			if (Request::header('x-pjax'))
			{
				return View::make('pjax.confirmcheckout');
			}else{
				return View::make('home.confirmcheckout');
			}

		}




		public function action_checkout_test()
		{
			$cart = json_decode('{"cart":"{\"Beef Rice BowlM\":{\"name\":\"Beef Rice Bowl\",\"size\":\"M\",\"price\":\"5.00\",\"quantity\":1},\"Chicken WingsM\":{\"name\":\"Chicken Wings\",\"size\":\"M\",\"price\":\"5.00\",\"quantity\":1},\"Sweet Potato FriesM\":{\"name\":\"Sweet Potato Fries\",\"size\":\"M\",\"price\":\"5.00\",\"quantity\":1},\"Thai Chili PrawnsM\":{\"name\":\"Thai Chili Prawns\",\"size\":\"M\",\"price\":\"5.00\",\"quantity\":2}}"}', true);
			return $this->action_checkout($cart);
		}

		public function action_jsontest()
		{
			$s = '{"Beef Rice BowlM":{"name":"Beef Rice Bowl","size":"M","price":"5.00","quantity":1},"Chicken WingsM":{"name":"Chicken Wings","size":"M","price":"5.00","quantity":1},"Sweet Potato FriesM":{"name":"Sweet Potato Fries","size":"M","price":"5.00","quantity":1},"Thai Chili PrawnsM":{"name":"Thai Chili Prawns","size":"M","price":"5.00","quantity":2}}';
			var_dump(json_decode($s));
		}

		public function action_test_notifications()
		{	
			$this->pushNotification('Warning!', 'This is a notification test.', 'warning');
			$this->pushNotification('Alert!', 'This is another notification test.', 'info');
			$this->pushNotification('Error!', 'Here\'s a third.', 'error');
			$this->pushNotification('Hey!', 'This one is a bit longer so we need to use the longer notation for displaying large messages without breaking the styling on the default notification block.', 'warning', true);
			
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
					$v = array();
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