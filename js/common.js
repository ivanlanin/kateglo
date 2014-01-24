/**
 * Add new row
 */
function add_row(table, fields, count)
{
	row_count = $(table + ' tr').size() - 1; // minus header
	new_row = $(table + ' tbody>tr:last')
		.clone(true)
		.css('display', 'none');
	elms = fields.split(', ');
	for (var idx in elms)
	{
		old_elm = '#' + elms[idx] + '_' + (row_count - 1);
		new_elm = elms[idx] + '_' + (row_count);
		$(old_elm, new_row)
			.attr('name', new_elm)
			.attr('id', new_elm);
	}
	new_row
		.insertAfter(table + ' tbody>tr:last')
		.animate({ opacity: 'show' }, 'normal');
	$('#' + count).val(row_count + 1);
}