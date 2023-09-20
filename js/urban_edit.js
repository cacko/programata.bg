$('add_part').observe('click', function(e) {
		var parts = $('content_parts').select('fieldset').size() / 2;
		if(parts == 3) return;
		new Ajax.Updater('content_parts', '/admin/?p=urban&act=addPart', {
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

//	var file = new Element('file', { 'class': 'btn', 'name': id, 'accept' : 'image/x-jpg', 'id': id});
//	my_div.insert(file);
	/*
	file.type = 'file';
	file.name = id;
	file.id = id;
	file.accept = 'image/x-jpg';
	file.class = 'btn';
	var br = document.createElement('br');
	*/
	//my_div.appendChild(file);
	//my_div.appendChild(br);

}