<?

	class UniqueItem extends Eloquent
	{

		public static $timestamps = false;

		public function type()
		{
			return $this->belongs_to('ItemType', 'type');
		}

		public function item()
		{
			return $this->has_many('Item', 'unique_id');
		}
		
		public function favorites()
		{
			return $this->has_many_and_belongs_to('User', 'favorites');
		}
	}

?>