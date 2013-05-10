<?
	
	class Item extends Eloquent
	{

		public function favorites()
		{
			return $this->has_many_and_belongs_to('User', 'favorites');
		}

		public function unique_item()
		{
			return $this->belongs_to('UniqueItem', 'unique_id');
		}

		public function item_size()
		{
			return $this->belongs_to('ItemSize', 'size');
		}

		public function orders()
		{
			return $this->has_many_and_belongs_to('OrderHeader', 'orderdetails');
		}

	}

?>