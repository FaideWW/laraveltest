<?

	class Item_Size extends Eloquent
	{

		public static function item()
		{
			return $this->belongs_to('Item');
		}

	}

?>