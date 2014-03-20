cms.init.add('categories_index', function () {
	// Reordering
	$('#categoriesReorderBtn').on('click', function () {
		var self = $(this);
		
		if(self.hasClass('btn-inverse')) {
			$('.map-items').show();
			$('#categoriesSortContainer').empty().hide();
			self.removeClass('btn-inverse');
		}else {
			self.addClass('btn-inverse');
			$('.map-items').hide();

			Api.get('categories.sort', {}, function(response) {
				$('#categoriesSortContainer')
					.html(response.response)
					.show();

				$('#nestable').nestable({
					group: 1,
					listNodeName: 'ul',
					listClass: 'dd-list unstyled',
				}).on('change', function(e, el) {
					
					var $prev_category = $(el).prev();
					var $next_category = $(el).next();
					var $parent_category = $(el).parent().parent();

					if($prev_category.length) {
						var target_id = $prev_category;
						var type = 'prev';
					} else if($next_category.length) {
						var target_id = $next_category;
						var type = 'next';
					} else if($parent_category.length) {
						var target_id = $parent_category;
						var type = 'parent';
					}
					
					if($(el).parent().parent().attr('id') == 'nestable') {
						type = 'scope';
					}
				
					Api.post('categories.sort', {
						'id': $(el).data('id'), 
						'target_id': target_id.data('id'),
						type: type
					});
				});
			});
		}
	});
});