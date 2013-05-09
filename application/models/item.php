<?
	
	class Item extends Eloquent
	{

		public function favorites()
		{
			return $this->has_many_and_belongs_to('User', favorites);
		}

		public function item_type()
		{
			return $this->has_one('Item_Type');
		}

		public function item_size()
		{
			return $this->has_one('Item_Size');
		}

	}

?>