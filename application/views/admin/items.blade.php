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
			<th>Image</th>
			<th>Available?</th>
			<th>Actions</th>
		</tr>
	</thead>
	<tbody id="itemList">
		@forelse ($items as $item)
			<tr id="{{ $item->id }}">
				<td class="itemName">{{ UniqueItem::find($item->unique_id)->name }}</td>
				<td class="itemSize">{{ $item->size }}</td>
				<td class="itemType">{{ UniqueItem::find($item->unique_id)->type }}</td>
				<td class="itemPrice">{{ $item->price }}</td>
				<td class="itemImage">
					<a href="#" data-name="{{ UniqueItem::find($item->unique_id)->name }}" data-source="{{ (UniqueItem::find($item->unique_id)->imgurl == null) ? 'http://placekitten.com/' . rand(500,1000) . '/' .  rand(500,1000) : UniqueItem::find($item->unique_id)->imgurl }} " class="img-modal">Click to view</a></td>
				<td class="itemAvail">@if (UniqueItem::find($item->unique_id)->available) Yes @else No @endif</td>
				<td class="actions">
					<button id="edit{{ $item->id }}" class="itemedit btn">Edit</button>
					<button id="delete{{ $item->id }}" class="itemdel btn btn-danger" data-toggle="modal" data-target="#modalDeleteItem">Delete</button>
				</td>
			</tr>
		@empty
			<tr>
				<td colspan="7">
					There are no items to display.
				</td>
			</tr>
		@endforelse
			<tr>
			</tr>
	</tbody>
</table>
<button id="newItem" class="btn pull-right" data-toggle="modal" data-target="#modalCreateItem">Add New Item</button>

<!-- Modal form dialogue -->

<div id="modalCreateItem" class="modal hide fade">
	{{ Form::open_for_files('admin/items/add', 'POST', array('class' => 'form-horizontal form-no-margin')) }}
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3>Add New Item</h3>
	</div>
	<div class="modal-body">
		<div class="control-group">
			{{ Form::label('itemname', 'Item Name', array('class' => 'control-label')) }}
			<div class="controls">
				{{ Form::item_name() }}
			</div>
		</div>
		<div class="control-group">
			{{ Form::label('itemsize', 'Size', array('class' => 'control-label')) }}
			<div class="controls">
				{{ Form::select('itemsize', array('X' => 'Extra Large',	'L' => 'Large', 'M' => 'Medium', 'S' => 'Small'), $selected = null) }}
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
				{{ Form::item_price() }}
			</div>
		</div>
		<div class="control-group">
			{{ Form::label('itemthumb', 'Thumbnail (optional)', array('class' => 'control-label')) }}
			<div class="controls">
				{{ Form::file('itemthumb', array('class' => 'filestyle')) }}
			</div>
		</div>
		<div class="control-group">
			{{ Form::label('itemavail', 'Availability', array('class' => 'control-label')) }}
			<div class="controls">
				{{ Form::checkbox('itemavail', '1', true) }}
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
		{{ Form::submit('Add Item', array('class' => 'btn btn-primary')) }}
		{{ Form::close() }}
	</div>
</div>

<!-- End modal form -->

<!-- Modal confirm delete -->

<div id="modalDeleteItem" class="modal hide fade">
	<div class="modal-header">
		<h3>Warning!</h3>
	</div>
	<div class="modal-body">You are about to delete <strong id="itemname"></strong>.  Are you sure you want to do this?</div>
	<div class="modal-footer">
		{{ Form::open('admin/items/delete', 'POST', array('class' => 'form-no-margin')) }}
		{{ Form::hidden('itemid', 'none', array('id' => 'delete_id')) }}
		<button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
		{{ Form::submit('Yes, Delete', array('class' => 'btn btn-danger')) }}
		{{ Form::close() }}
	</div>

</div>

<!-- End modal confirm delete -->

<!-- Modal show thumbnail -->

<div id="modalShowThumb" class="modal hide fade">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 id="modal-image-name"></h3>
	</div>
	<div class="modal-body">
		<img id="modal-img-thumbnail" />
	</div>
</div>

<!-- End modal show thumbnail -->

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

		//insert the correct information into the deletion prompt
		$('button.itemdel').click(function()
		{
			var itemrow = $(this).closest('tr');
			var id = itemrow.attr('id');
			var itemname = itemrow.children('.itemName').text() + ' (' + itemrow.children('.itemSize').text() + ')';
			$('strong#itemname').text(itemname);
			$('input#delete_id').val(id);
		});

		//generate modal for image thumbnails
		$('.img-modal').click(function()
		{
			var itemname = $(this).attr('data-name');
			var url = $(this).attr('data-source');
			$('#modal-img-thumbnail').attr('src', url);
			$('#modal-image-name').text(itemname);
			$('#modalShowThumb').modal('show');
		});

		//auto-resize to fit image
		$('#modalShowThumb').on('show', function () {

    $('.modal-body',this).css({width:'auto',height:'auto', 'max-height':'100%'});
		});

		//init bootstrap-filestyle
		$(':file').filestyle();

	});
</script>
@endsection