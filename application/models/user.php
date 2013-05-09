<?

	class User extends Eloquent
	{

		public static $timestamps = true;

		public function address()
		{
			return $this->has_one('Address');
		}

		public function favorites()
		{
			return $this->has_many_and_belongs_to('Item', 'favorites');
		}
	}

?>