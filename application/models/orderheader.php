<?

	class OrderHeader extends Eloquent
	{

		public static $timestamps = true;

		public function details()
		{
			return $this->has_many('OrderDetail');
		}

		public function items()
		{
			return $this->has_many_and_belongs_to('Item', 'orderdetails');
		}

		public function customer()
		{
			return $this->belongs_to('User', 'customer_id');
		}

	}

?>