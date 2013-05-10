<?

	class ItemSize extends Eloquent
	{

		public static function item()
		{
			return $this->has_many('Item', 'size');
		}

	}

?>