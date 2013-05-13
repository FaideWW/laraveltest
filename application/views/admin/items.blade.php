@layout('layouts/main')

@section('content')

<h1>Administration - Menu Items</h1>
<table class="table table-striped">
	<caption>All Items</caption>
	<thead>
		<tr>
			<th>Name</th>
			<th>Size</th>
			<th>Type</th>
			<th>Price</th>
			<th>Available?</th>
		</tr>
	</thead>
	<tbody id="itemList">
		@forelse ($items as $item)
			<tr id="{{ $item->id }}">
				<td class="itemName">{{ $item->name }}</td>
				<td class="itemSize">{{ $item->size }}</td>
				<td class="itemType">{{ $uitems[$item->unique_id]->type }}</td>
				<td class="itemPrice">{{ $item->price }}</td>
			</tr>
		@empty
			<tr>
				<td class="itemName">test</td>
				<td class="itemSize">test</td>
				<td class="itemType">test</td>
				<td class="itemPrice">test</td>
				<td class="itemAvail">test</td>
			</tr>
		@endforelse
			<tr>
			</tr>
	</tbody>
</table>
<button id="newItem" class="btn pull-right" data-toggle="modal" data-target="#modalCreateItem">Add New Item</button>

<!-- Modal form dialogue -->

<div id="modalCreateItem" class="modal hide fade">
	{{ Form::open('admin/items/add', 'POST', array('class' => 'form-horizontal')) }}
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3>Add New Item</h3>
	</div>
	<div class="modal-body">
		<div class="control-group">
			{{ Form::label('itemname', 'Item Name', array('class' => 'control-label')) }}
			<div class="controls">
				{{ Form::text('itemname', 'Item Name') }}
			</div>
		</div>
		<div class="control-group">
			{{ Form::label('itemsizes', 'Sizes Available', array('class' => 'control-label')) }}
			<div class="controls">
				{{ Form::select('itemsizes', array('X' => 'Extra Large',	'L' => 'Large', 'M' => 'Medium', 'S' => 'Small'), $selected = null, array('multiple' => 'multiple')) }}
			</div>
		</div>
		<div class="control-group">
			{{ Form::label('itemtype', 'Item Category', array('class' => 'control-label')) }}
			<div class="controls">
				{{ Form::select('itemtype', $cats) }}
			</div>
		</div>
		<div class="control-group input-prepend">
			{{ Form::label('itemprice', 'Price', array('class' => 'control-label')) }}
			<div class="controls">
				<span class="add-on">$</span>
				{{ Form::text('itemprice', '0.00') }}
			</div>
		</div>
		<div class="control-group">
			{{ Form::label('itemavail', 'Availability', array('class' => 'control-label')) }}
			<div class="controls">
				{{ Form::checkbox('itemavail', 'yes', true) }}
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<button class="btn">Cancel</button>
		{{ Form::submit('Add Item', array('class' => 'btn btn-primary')) }}
	</div>
</div>

<!-- End modal form -->

@endsection

@section('pagescripts')
<script>


	$(document).ready(function()
	{
		var insertionCount = 0;

		var itemProperties =[
			'itemName',
			'itemSize',
			'itemType',
			'itemPrice',
			'itemAvail'
		];

		$('button#newItem').click(function()
		{

		});



	});




</jquer>
@endsection