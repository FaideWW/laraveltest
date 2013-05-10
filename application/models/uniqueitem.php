<?

	class UniqueItem extends Eloquent
	{
		public function type()
		{
			return $this->belongs_to('ItemType', 'type');
		}

		public function item()
		{
			return $this->has_many('Item', 'unique_id');
		}
	}

?>