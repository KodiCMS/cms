cms.init.add(['page_add', 'page_edit'], function() {
	$('#pageEditMetaTagsField').tagsInput({
		width: 'auto',
		'delimiter': TAG_SEPARATOR,
		defaultText: __('Add a tag'),
		autocomplete_url:'/ajax-tags-get'
	});
});

cms.ui.add('tags_input', function() {
	$('.tags').tagsInput({
		width: 'auto',
		'delimiter': TAG_SEPARATOR,
		defaultText: __('Add a tag')
	});
});