@layout('layouts/main')

@section('content')
@parent

<div class="row">
	<h1>Checkout</h1>
	<div class="progress">
		
		<div class="bar" style="width: 0%">
			<span>
			
		</span>
		</div>
	</div>
	<div id="checkout-container">
		
		<fieldset>
			<table class="table table-hover">
				<caption>
					<p>Please review your cart before confirming your order.  You can edit the quantity of any item by changing the number in the Quantity column.  </p>
					<p>Click the <span class="remove-item">&times;</span> on any item to remove it from your cart (alternatively you can set the quantity to 0).</p>
					<p class="text-info">Your order will not be placed until the next step of the checkout.</p>
				</caption>
				<thead>
					<tr>
						<th>Item / Size</th>
						<th class="span4">Quantity</th>
						<th class="span4">Price</th>
					</tr>
				</thead>
				<tbody>
					@forelse ($finalcart as $item)
					
					<tr id="{{ $item['id'] }}" data-item-name="{{ $item['name'] }}" data-item-size="{{ $item['size'] }}">
						<td>
							{{ $item['name'] }} / {{ $item['size'] }}
							<span class="remove-item click-to-remove">&times;</span>
						</td>
						<td class="span4">{{ Form::text($item['id'].'_total', $item['quantity'], array('class' => 'input-mini item-value')) }}</td>
						<td id="{{ $item['id'].'_total' }}" class="item-price span4" data-item-price="{{ $item['price'] }}">{{ number_format($item['price'] * $item['quantity'], 2) }}</td>
					</tr>
				
					@empty

					<p>
						Somehow, you have an empty cart.  Please return to the <a href="{{ URL::to_action('menu@index') }}">menu</a> to select some items.
					</p>
					@endforelse
				</tbody>
			</table>

			<div class="span6 pull-right">
				<table class="table well">
					<tbody>	
						<tr>
							<td><strong>Subtotal</strong></td>
							<td class="subtotal"></td>
						</tr>
						<tr>
							<td><strong>GST (5%)</strong></td>
							<td class="tax"></td>
						</tr>
						<tr>
							<td><strong>Total</strong></td>
							<td class="total"></td>
						</tr>
					</tbody>
				</table>
				<button class="btn btn-large btn-primary pull-right" id="confirmorder">Confirm Order</button>
			</div>

		</fieldset>
	</div>
</div>

@section('pagescripts')
<script>
	$(document).ready(function()
	{

		var checkout_steps_array = [
			{
				'msg' : 'Confirm Your Order (1/3)',
				'pct' : '33%'
			},
			{
				'msg' : 'Your Information (2/3)',
				'pct' : '66%'
			},
			{
				'msg' : 'Complete!',
				'pct' : '100%'
			}
		];

		var checkout_last_step = 0;
		var checkout_current_step = 0;

		var cartArray = JSON.parse('{{ $jsoncart }}');
		
		$('input.item-value').change(function()
		{
			var name = $(this).attr('name');
			var value = Math.round($(this).val());
			if (value == 0)
			{
				removeItem(name);
				return;
			}
			$(this).val(value);
			updateValue(name, value)
		});

		$('span.click-to-remove').click(function()
		{
			var id = $(this).parent().parent().attr('id');
			removeItem(id);
		});

		var updateValue = function(item, quantity)
		{
			var id = (item.split('_'))[0];
			var per_unit_price = parseFloat($('#' + item).attr('data-item-price'));
			var total_price = per_unit_price * quantity;
			$('#' + item).text(total_price.toFixed(2));
			cartArray[$('#' + id).attr('data-item-name') + $('#' + id).attr('data-item-size')]['quantity'] = quantity;
			saveCart();
			updateTotals();
		}

		var removeItem = function(item)
		{
			var id = (item.split('_'))[0];
			delete cartArray[$('#' + id).attr('data-item-name') + $('#' + id).attr('data-item-size')];
			$('#' + id).remove();
			saveCart();
			updateTotals();
		}

		//POST shopping cart to Session
		var saveCart = function()
		{
			$.ajax({
				url: '{{ URL::to_action("menu@savecart") }}',
				data: {cart: JSON.stringify(cartArray)},
				type: 'POST'
			});
			console.log(JSON.stringify(cartArray));
		}

		//Overwrite POSTed shopping cart with an empty array
		var unloadCart = function()
		{
			$.ajax({
				url: '{{ URL::to_action("menu@unsavecart") }}',
				type: 'POST',
			});
		}

		var loadCart = function(data)
		{
			cartArray = JSON.parse(data);
			if (cartIsEmpty)
				initCart();
			redrawCart();
		}

		var updateTotals = function()
		{
			var totals = calculateTotals(cartArray);
			$('td.subtotal').text((totals['subtotal']).toFixed(2));
			$('td.tax').text((totals['tax']).toFixed(2));
			$('td.total').text((totals['total']).toFixed(2));
		}

		//returns an array of subtotal, tax, and total
		var calculateTotals = function(cart)
		{
			var sub = 0;
			for(item in cart)
			{
				price = (parseFloat(cart[item]['price']) * parseInt(cart[item]['quantity']));
				sub += price;
			}

			var tax = sub * 0.05;
			var total = sub + tax;
			return {
				'subtotal': sub,
				'tax' : tax,
				'total': total
			};
		}

		$('div.bar').css('width', '33%');
		$('div.bar>span').text('Confirm your Cart (1/3)');
		updateTotals();


		/**
		 * PJAX voodoo magic
		 */

		$('#confirmorder').click(function()
		{
			$.pjax({
				container: '#checkout-container',
				timeout: 10000,
				url: '{{ URL::to_action("menu@checkout_confirm") }}'
			});
			checkout_last_step = checkout_current_step;
		});

		$(document).on('pjax:send', function()
		{
			$('div.progress').addClass('progress-striped').addClass('active');
			$('div.bar>span').text('Loading...');
		});

		$(document).on('pjax:complete', function()
		{
			$('div.progress').removeClass('progress-striped').removeClass('active');
			checkout_current_step++;
			console.log(checkout_current_step);
			$('div.bar').css('width', checkout_steps_array[checkout_current_step]['pct']);
			$('div.bar>span').text(checkout_steps_array[checkout_current_step]['msg']);
		});

		$(document).on('pjax:popstate', function()
		{
			var t = checkout_current_step;
			checkout_current_step = checkout_last_step;
			checkout_last_step = t;

			console.log(checkout_current_step);
			$('div.bar').css('width', checkout_steps_array[checkout_current_step]['pct']);
			$('div.bar>span').text(checkout_steps_array[checkout_current_step]['msg']);
		});

	});
</script>
@endsection

@endsection