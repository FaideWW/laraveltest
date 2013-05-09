<?

	class Address extends Eloquent
	{

		public static function user()
		{
			return $this->belongs_to('User');
		}

	}

?>