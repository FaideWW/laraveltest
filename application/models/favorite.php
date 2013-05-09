<?

	class Favorite extends Eloquent
	{

		public static function user()
		{
			return $this->belongs_to('User');
		}
		
		public static function item()
		{
			return $this->belongs_to('Item');
		}

		
	}

?>