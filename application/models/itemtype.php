<?

	class ItemType extends Eloquent
	{

		public static function items()
		{
			return $this->has_many('UniqueItem');
		}

	}

?>