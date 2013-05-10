<?

	class OrderDetail extends Eloquent
	{

		public static function item()
		{
			return $this->belongs_to('Item');
		}

		public static function orderheader()
		{
			return $this->belongs_to('OrderHeader');
		}

	}

?>