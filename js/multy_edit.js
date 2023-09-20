$('add_part').observe('click', function(e) {
		var parts = $('content_parts').select('fieldset').size() / 2;
		new Ajax.Updater('content_parts', '/admin/?p=multy&act=addPart', {
			method : 'get',
			parameters : {
				"part" : parts + 1,
			},
			insertion: 'bottom'
		});
});
function addFilePart(part) {
	var my_div = $('files' + part)
	var files = my_div.select('input').size();
	var next_file = files + 1;
	var id = 'mainImage_' + part + '_' + next_file;
	var html = '<input type="file" name="' + id + '" ';
	html += 'id="' + id + '" size="47" accept="image/jpg" class="btn" /><br />';
	my_div.insert(new Element('input', { 'class': 'btn', 'name': id, 'accept' : 'image/jpg', 'type': 'file'}));
	my_div.insert(new Element('br'));
}