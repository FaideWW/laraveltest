<?

	class ItemType extends Eloquent
	{

		public function items()
		{
			return $this->has_many('UniqueItem', 'type');
		}

	}

?>