$(function() {
	$('input[data-related-array]').each(function() {
		var $self = $(this);
		var $ds_id = $self.data('related-array');
		
		$self.select2({
			placeholder: __("Type first 1 chars to find documents"),
			minimumInputLength: 1,
			multiple:true,
			formatSelection: function(state) {
				return '<a target="_blank" href="/backend/hybrid/document/view?ds_id=' + $ds_id + '&id=' + state.id + '">' + state.text + '</a>';
			},
			escapeMarkup: function(m) { return m; },
			ajax: {
				url: Api.build_url('datasource/hybrid-document.find'),
				data: function(query, pageNumber, context) {
					return {
						key: query,
						id: DOCUMENT_ID,
						ds_id: $ds_id,
						is_array: true
					}
				},
				dataType: 'json',
				results: function (resp, page) {
					return {results: resp.response};
				}
			},
			initSelection: function(element, callback) {
				var id = $(element).val(),
					ids = id.split(',');

				ids = _.map(ids, function(num){ return parseInt(num); });

				if (id !== "") {
					$.ajax(Api.build_url('datasource/hybrid-document.find'), {
						data: {
							ids: ids,
							id: DOCUMENT_ID,
							ds_id: $ds_id,
							is_array: true
						},
						dataType: 'json',
					}).done(function(resp, page) {
						selected = [];
						for(row in resp.response) {
							if(_.indexOf(ids, resp.response[row]['id']) >= 0){
								selected.push(resp.response[row]);
							}
						}
						callback(selected);
					});
				}
			}
		});

		$self.select2("container").find("ul.select2-choices").sortable({
			containment: 'parent',
			items: 'li:not(.select2-search-field)',
			tolerance: 'pointer',
			placeholder: 'ui-state-highlight select2-search-choice',
			opacity:.8,
			axis: "x",
			start: function () {
				$self.select2("onSortStart");
			},
			update: function () {
				$self.select2("onSortEnd");
			}
		});

		//$self.on("change", function() { console.log($self.val()); });
	});
});