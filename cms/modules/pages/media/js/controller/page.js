cms.init.add('page_index', function () {
	// Read coockie of expanded pages
	var matches = document.cookie.match(/expanded_rows=(.+?);/);
	var expanded_pages = matches ? decodeURIComponent(matches[1]).split(',') : [];
	var arr = [];

	for (var i = 0; i < expanded_pages.length; i++) {
		if (typeof(parseInt(expanded_pages[i])) == 'number')
			arr[i] = parseInt(expanded_pages[i]);
	}

	expanded_pages = arr;
	var expandedPagesAdd = function (page_id) {
		expanded_pages.push(page_id);

		document.cookie = ['expanded_rows', '=', encodeURIComponent(jQuery.unique(expanded_pages).join(',')), '; path=', window.location.pathname].join('');
	};

	var expandedPagesRemove = function (page_id) {
		expanded_pages = jQuery.grep(expanded_pages, function (value, i) {
			return value != page_id;
		});

		document.cookie = ['expanded_rows', '=', encodeURIComponent(jQuery.unique(expanded_pages).join(',')), '; path=', window.location.pathname].join('');
	}

	$('#pageMapItems').on('click', '.item-expander', function () {
		var li = $(this).parent().parent().parent().parent();
		var parent_id = li.data('id');

		var expander = $(this);

		if (!li.hasClass('item-expanded')) {
			var level = parseInt(li.parent().data('level'));
			var success_handler = function (html) {
				li.append(html);
				expander
					.addClass('item-expander-expand')
					.removeClass('icon-plus')
					.addClass('icon-minus');

				li.addClass('item-expanded');

				expandedPagesAdd(parent_id);

				cms.loader.hide();
			};

			// When ajax error of updating information about page position
			var error_handler = function (html) {
				cms.error('Ajax: Sub pages not loaded!', html);

				cms.loader.hide();
			}

			cms.loader.show();

			// Sending information about page position to frog
			jQuery.ajax({
				// options
				url:SITE_URL + ADMIN_DIR_NAME + '/page/children/',
				dataType:'html',
				data:{
					parent_id:parent_id,
					level:level
				},

				// events
				success:success_handler,
				error:error_handler
			});
		}
		else {
			if (expander.hasClass('item-expander-expand')) {
				expander
					.removeClass('item-expander-expand')
					.removeClass('icon-minus')
					.addClass('icon-plus');

				li.find('> ul').hide();

				expandedPagesRemove(parent_id);
			}
			else {
				expander
					.addClass('item-expander-expand')
					.removeClass('icon-plus')
					.addClass('icon-minus');

				li.find('> ul').show();

				expandedPagesAdd(parent_id);
			}
		}
	});


	// Reordering
	$('#pageMapReorderButton').on('click', function () {
		var self = $(this);
		
		if(self.hasClass('btn-inverse')) {
			$('#pageMapSearchItems').empty().hide();
			$('#pageMapHeader').show();
			self.removeClass('btn-inverse');
			
			$.get(SITE_URL + ADMIN_DIR_NAME + '/page/children', {parent_id: 1, level: 0}, function(resp) {
				$('#pageMapItems')
					.find('ul')
					.remove();
		
				$('#pageMapItems')
					.show()
					.find('li')
					.append(resp);
			}, 'html');

		}else {
			self.addClass('btn-inverse');
			$('#pageMapItems').hide();
			$('#pageMapHeader').hide();

			Api.get('pages.sort', {}, function(response) {
				$('#pageMapSearchItems')
					.html(response.response)
					.show();

				$('#nestable').nestable({
					group: 1,
					listNodeName: 'ul',
					listClass: 'dd-list unstyled',
				}).on('change', function(e, el) {
					var list   = e.length ? e : $(e.target);
					Api.post('pages.sort', {'pages': list.nestable('serialize')});
				});
			});
		}
	});

	$('#pageMap .form-search')
		.on('submit', function (event) {
			var form = $(this);

			if ($('.search-query', this).val() !== '') {
				$('#pageMapItems').hide();
				
				Api.get('pages.search', form.serialize(), function(resp) {
					$('#pageMapSearchItems')
						.html(resp.response);
				});
		
			} else {
				$('#pageMapItems').show();
				$('#pageMapSearchItems').hide();
			}

			return false;
		});
});


cms.init.add('page_add', function () {
	$('body').on('keyup', 'input[name="page[title]"]', function () {
		$('input[name="page[breadcrumb]"]')
			.add('input[name="page[meta_title]"]')
			.val($(this).val());
	});
});

cms.init.add(['page_add', 'page_edit'], function () {
	$('body').on('change', 'select[name="page[status_id]"]', function () {
		show_password_field($(this).val());
	});
	
	show_password_field($('select[name="page[status_id]"]').val());
	
	function show_password_field(val) {
		var select = $('select[name="page[status_id]"]');
		
		if(val == 200){
			select.parent().addClass('well well-small').find('.password-container').removeClass('hidden');
		} else {
			select.parent().removeClass('well well-small')
				.find('.password-container').addClass('hidden')
				.find('input').val('');
		}
	}
});