cms.ui.add('tags_input', function() {
	$(':input.tags').tagsInput({
		width: 'auto',
		height: '50px',
		delimiter: TAG_SEPARATOR,
		defaultText: __('Add a tag'),
		autocomplete_url: SITE_URL + '/api-tags',
		autocomplete: {
			source: function( request, response ) {
				$.getJSON( SITE_URL + '/api-tags', {term: request.term}, function(data) {
					response(data.response)
				});
			}
		}
	});
});