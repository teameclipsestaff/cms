(function($) {


var $table = $('#blocks'),
	totalBlocks = $table.children('tbody').children().length;


var sorter = new blx.ui.DataTableSorter($table, {
	onSortChange: function() {

		// Get the new block order
		var blockIds = [];

		for (var i = 0; i < sorter.$items.length; i++)
		{
			var blockId = parseInt($(sorter.$items[i]).attr('data-block-id'));
			blockIds.push(blockId);
		}

		// Send it to the server
		var data = {
			blockIds: JSON.stringify(blockIds)
		};

		$.post(blx.actionUrl+'content/reorderEntryBlocks', data, function(response) {
			if (response.success)
				blx.cp.displayNotice(blx.t('New block order saved.'));
			else
				blx.cp.displayError(blx.t('Couldn’t save new block order.'));
		});
	}
});


$table.find('.deletebtn').click(function() {
	var $row = $(this).closest('tr'),
		blockName = $row.children(':first').children('a').text();

	if (confirm(blx.t('Are you sure you want to delete the entry block “{block}”?', { block: blockName })))
	{
		var data = {
			blockId: $row.attr('data-block-id')
		};

		$.post(blx.actionUrl+'content/deleteEntryBlock', data, function(response) {
			if (response.success)
			{
				$row.remove();

				totalBlocks--;
				if (totalBlocks == 0)
				{
					$table.remove();
					$('#noblocks').show();
				}

				blx.cp.displayNotice(blx.t('Entry block deleted.'));
			}
			else
				blx.cp.displayError(blx.t('Couldn’t delete entry block.'));
		});
	}
});


})(jQuery);
