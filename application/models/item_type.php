<?

	class Item_Type extends Eloquent
	{

		public static function item()
		{
			return $this->belongs_to('Item');
		}

	}

?>