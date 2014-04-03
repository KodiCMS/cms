var cms = {
	models: {},
	views: {},
	collections: {},
	routes: {},
	popup_target: null,
	
	// Error
	error: function (msg, e) {
		this.message(msg, 'error');
		$.jGrowl(msg, {theme: 'alert alert-error'});
	},
		
	message: function(msg, type) {
		if(!type) type = 'success';
		window.top.$.jGrowl(decodeURI(msg), {theme: 'alert alert-' + type});
	},
	error_field: function(name, message) {
		name = name.indexOf('.') !== -1 ? '['+name.replace(/\./g, '][') + ']' : name;
		var gpoups = $('.control-group:not(.error)');
		
		input = $(':input[name*="' + name + '"]', gpoups)
			.after('<span class="help-inline error-message">' + message + '</span>')
			.parentsUntil( '.control-group' )
			.parent()
			.addClass('error');
	
		var $tab_pane = input.parentsUntil('tab-pane');
		if($tab_pane.length) {
			$tab_id = $tab_pane.attr('id');
			$tab_pane.parent().parent().find('.nav-tabs li a[href="#'+$tab_id+'"]').addClass('tab-error');
		}
	},
	clear_error: function() {
		$('.control-group')
			.removeClass('error')
			.find('.error-message')
			.remove();
	
		$('.nav-tabs li a').removeClass('tab-error');
	},
	// Convert slug
	convert_dict: {
		'ą':'a', 'ä':'a', 'č':'c', 'ę':'e', 'ė':'e', 'i':'i', 'į':'i', 'š':'s', 'ū':'u', 'ų':'u', 'ü':'u', 'ž':'z', 'ö':'o',
		'а':'a','б':'b','в':'v','г':'g','д':'d','е':'e','ё':'yo','ж':'zh','з':'z','и':'i','й':'j','к':'k','л':'l','м':'m','н':'n','о':'o','п':'p','р':'r','с':'s','т':'t','у':'u','ф':'f','х':'h','ц':'c','ч':'ch','ш':'sh','щ':'shh','ы':'y','э':'e','ю':'yu','я':'ya','ь':'','ъ':'','і':'i','ї':'yi','А':'A','Б':'B','В':'V','Г':'G','Д':'D','Е':'E','Ё':'YO','Ж':'ZH','З':'Z','И':'I','Й':'J','К':'K','Л':'L','М':'M','Н':'N','О':'O','П':'P','Р':'R','С':'S','Т':'T','У':'U','Ф':'F','Х':'H','Ц':'C','Ч':'CH','Ш':'SH','Щ':'SHH','Ы':'Y','Э':'E','Ю':'YU','Я':'YA','І':'I','Ї':'YI','Є':'E','Ь':'','Ъ':'',
		'Ą':'A','Č':'C','Ę':'E','Ė':'E','Į':'I','Š':'S','Ū':'U','Ų':'U','Ž':'Z','ą':'a','č':'c','ę':'e','ė':'e','i':'i','į':'i','š':'s','ū':'u','ų':'u','ž':'z'
	},
	convertSlug: function (str, separator) {
		var default_separator = '-';
		if(!separator) {
			separator = default_separator;
		}
		return str
			.toString()
			.toLowerCase()
			.replace(/[àâ]/g, 'a')
			.replace(/[éèêë]/g, 'e')
			.replace(/[îï]/g, 'i')
			.replace(/[ô]/g, 'o')
			.replace(/[ùû]/g, 'u')
			.replace(/[ñ]/g, 'n')
			.replace(/[äæ]/g, 'ae')
			.replace(/[öø]/g, 'oe')
			.replace(/[ü]/g, 'ue')
			.replace(/[ß]/g, 'ss')
			.replace(/[å]/g, 'aa')
			.replace(/(.)/g, function (c) {
				return (cms.convert_dict[c] != undefined ? cms.convert_dict[c] : c);
			})
			.replace(/[^a-zа-яіїє0-9\.\_]/g, separator)
			.replace(/ /g, separator)
			.replace(/\-+/g, separator)
			.replace(/^-/, '');
	},
	
	// 
	loader: {
		init: function () {
			$('body')
				.append('<div class="_loader_container"><div class="_loader_bg"></div><span>' + __('Loading') + '</span>\n\
</div>');
		},
		show: function (speed) {
			if(!speed) {
				speed = 500;
			}
			$('._loader_container').fadeTo(speed, 0.4);
		},
		hide: function () {
			$('._loader_container').stop().fadeOut();
		}
	},
	
	/**
	 * Вычисление высоты контейнера с контентом
	 */
	content_height: null,
	calculateContentHeight: function() {
		if(this.content_height != null) 
			return this.content_height;

		var contentCont = $('#content'),
			headerCont = $('header'),
			footerCont = $('footer'),
			windowCont = $(window);

		var contentContHeight = windowCont.outerHeight() - headerCont.outerHeight(),
			contentContPadding = contentCont.outerHeight(!$('body').hasClass('iframe')) - contentCont.innerHeight() + ($('body').hasClass('iframe')) ? 0 : 85;

		this.content_height = contentContHeight - contentContPadding;

		return this.content_height;
	},
	
	translations: {},
	
	// Plugins
	plugins: {},
	
	// Messages
	messages: {
		init: function () {
			$('.message')
				.animate({top:0}, 1000);
		}
	},
	filters: {
		filters: [],
		switchedOn: {},
		editors: {},
		add: function (name, switchOn_handler, switchOff_handler, exec_handler) {
			if (switchOn_handler == undefined || switchOff_handler == undefined) {
				cms.error('System try to add filter without required callbacks.', name, switchOn_handler, switchOff_handler);
				return;
			}
			this.filters.push([ name, switchOn_handler, switchOff_handler, exec_handler ]);
		},
		switchOn: function (textarea_id, filter, params) {
			$('#' + textarea_id).css('display', 'block');
			if (this.filters.length > 0) {
				var old_filter = this.get(textarea_id);
				var new_filter = null;
				
				for (var i = 0; i < this.filters.length; i++) {
					if (this.filters[i][0] == filter) {
						new_filter = this.filters[i];
						break;
					}
				}
				if(old_filter !== new_filter) {
					this.switchOff(textarea_id);
				}
				try {
					this.switchedOn[textarea_id] = new_filter;
					this.editors[textarea_id] = new_filter[1](textarea_id, params);
					$('#' + textarea_id).trigger('filter:switch:on', this.editors[textarea_id]);
				}
				catch (e) {}
			}
		},
		switchOff: function (textarea_id) {
			var filter = this.get(textarea_id);
			try {
				if ( filter && typeof(filter[2]) == 'function' ) {
					filter[2](this.editors[textarea_id], textarea_id);
				}
				this.switchedOn[textarea_id] = null;
				$('#' + textarea_id).trigger('filter:switch:off');
			}
			catch (e) {}
		},
		get: function(textarea_id) {
			for (var key in this.switchedOn) {
				if ( key == textarea_id )
					return this.switchedOn[key];
			}
			return null;
		},	
		exec: function(textarea_id, command, data) {
			var filter = this.get(textarea_id);
			if( filter && typeof(filter[3]) == 'function' )
				return filter[3](this.editors[textarea_id], command, textarea_id, data);
			return false;
		}
	},
	filemanager: {
		open: function(object, type) {
			return $.fancybox.open({
				href : BASE_URL + '/elfinder/',
				type: 'iframe'
			}, {
				autoSize: false,
				width: 1000,
				afterLoad: function() {
					this.content[0].contentWindow.elfinderInit(object, type);
				}
			});
		}
	}
};

cms.addTranslation = function (obj) {
    for (var i in obj) {
        cms.translations[i] = obj[i];
    }
};

cms.navigation = {
	counter: {
		init: function() {
			$('#site_nav .dropdown').each(function() {
				var total = 0;

				$('.dropdown-menu a', this).each(function() {
					if($(this).data('counter') > 0) {
						total += $(this).data('counter');
						$(this).append('<span class="counter">' + $(this).data('counter') + '</span>');
					}
				});

				if(total > 0)
					$('.dropdown-toggle', this).append('<span class="counter">' + total + '</span>');
			});
			
			$('#subnav a').each(function() {
				if($(this).data('counter') > 0) {
					$(this).append('<span class="counter">' + $(this).data('counter') + '</span>');
				}
			});
		},
		add: function(href, count) {
			$('.dropdown-menu a[href*="'+href+'"]').data('counter', count);
			$('#subnav a[href*="'+href+'"]').data('counter', count);
			this.init();
		},
		remove: function(href) {
			$('.dropdown-menu a[href*="'+href+'"]').removeData('counter');
			$('#subnav a[href*="'+href+'"]').removeData('counter');
			this.init();
		}
	}
};

cms.ui = {
    callbacks:[],
    add:function (module, callback) {
        if (typeof(callback) != 'function')
            return this;

        cms.ui.callbacks.push([module, callback]);
		
		return this;
    },
    init:function (module) {
        for (var i = 0; i < cms.ui.callbacks.length; i++) {
			try {
				if(!module)
					cms.ui.callbacks[i][1]();
				else if(_.isArray(module) && _.indexOf(module, cms.ui.callbacks[i][0]) != -1 ) {
					cms.ui.callbacks[i][1]();
				}
				else if (module == cms.ui.callbacks[i][0]) {
					cms.ui.callbacks[i][1]();
				}
			} catch (e) {}
        }
    }
};

cms.init = {
	callbacks:[],
	add:function (rout, callback) {
		if (typeof(callback) != 'function')
			return false;

		if (typeof(rout) == 'object') {
			for (var i = 0; i < rout.length; i++)
				cms.init.callbacks.push([rout[i], callback]);
		}
		else if (typeof(rout) == 'string')
			cms.init.callbacks.push([rout, callback]);
		else
			return false;
		
		cms.init.callbacks.reverse();
	},
	run:function () {
		var body_id = $('body:first').attr('id').toString();

		for (var i = 0; i < cms.init.callbacks.length; i++) {
			var rout_to_id = 'body_' + cms.init.callbacks[i][0];

			if (body_id == rout_to_id)
				cms.init.callbacks[i][1]();
		}
	}
};

cms.ui.add('flags', function() {
	$('body').on('click', '.flags .label', function(e) {
		var $src = $(this).parent().data('target');
		if( ! $src ) $src = $(this).parent().prevAll(':input');
		
		var $container = $(this).parent();
		var $append = $container.data('append') == true;
		var $array = $container.data('array') == true;
		var $value = $(this).data('value');
		
		if($array) $value = $value.split(',');
		
		$('.label', $container).removeClass('label-success');
		$(this).addClass('label-success');

		if($append) {
			var $old_value = '';
			if($src.is(':input')) {
				$old_value += $src.val();
				$value = $old_value.length > 0 ? $old_value + ', ' + $value: $value;
				$src.val($value);
			}
			else {
				$old_value += $src.val();
				$value = $old_value.length > 0 ? $old_value + ', ' : $value;
				$src.text($value);
			}
		} else {
			if($src.hasClass('select2-offscreen'))
			{
				$src.select2("val", $value);
			}
			else if($src.is(':input'))
			{
				$src.val($value);
			}
			else {
				$src.text($value);
			}
		}
		
		e.preventDefault();
	});
}).add('btn-confirm', function() {
	$('body').on('click', '.btn-confirm', function () {
		if (confirm(__('Are you sure?')))
			return true;

		return false;
	});
}).add('nav-counter', function() {

	$('.dropdown-submenu .caret').remove();
	cms.navigation.counter.init();

}).add('outline', function() {
	$('.widget')
		.addClass('outline_inner')
		.wrap('<div class="outline"></div>');
}).add('tabable', function() {
	if($('.tabbable').length > 0) {
		$('#content .widget-header').each(function(i) {
			if($(this).hasClass('widget-section')) {
				$('<li class="nav-section"><h5><i class="icon-arrow-down"></i> ' + $(this).text() + '</h5></li>').appendTo($('.tabbable .nav'));
			} else {
				var text = $(this).text();
				
				if($(this).data('icon')) {
					text = '<i class="icon-'+$(this).data('icon')+'"></i> ' + text;
				}
				
				$('<li><a href="#tab' + i + '" data-toggle="tab">' + text + '</a></li>')
					.appendTo($('.tabbable .nav'));
			
				
				
				if($(this).hasClass('widget-header-onlytab'))
					$('<div class="tab-pane" id="tab' + i + '">' + $(this).next().html() + '</div>').appendTo($('.tabbable .tab-content'));
				else 
					$('<div class="tab-pane" id="tab' + i + '"><h2>'+$(this).text()+'</h2><hr />' + $(this).next().html() + '</div>').appendTo($('.tabbable .tab-content'));

				$(this).next().remove();
			}
			
			$(this).remove();
			
			
		});
		$('.tabbable .nav li a').on('click', function() {
			window.location.hash = $(this).attr('href');
		});

		if(window.location.hash.length > 0 && $('.tabbable .nav li a[href='+window.location.hash+']').length > 0) {
			$('.tabbable .nav li a[href='+window.location.hash+']').parent().addClass('active');
			$('.tabbable ' + window.location.hash).addClass('active');
		} else {
			$('.tabbable .nav li:first-child').addClass('active');
			$('.tabbable .tab-pane:first-child').addClass('active');
		}

		$('.tabbable .tab-pane').css({
			'min-height': cms.calculateContentHeight() - 130
		});
		
		$(window).resize(function() {
			$('.tabbable .tab-pane').css({
				'min-height': cms.calculateContentHeight() - 130
			});
		});
	}
	
	$(window).trigger('tabbable');
}).add('calculate_height', function() {
	cms.calculateContentHeight();

	$(window).resize(function() {
		cms.content_height = null;
		cms.calculateContentHeight();
	});
}).add('slimmScroll', function() {
	$('.widget-scroll').slimScroll();
}).add('navbar', function() {
	
	var user_nav = $('#user_nav');
	var brand = $('.brand');

	var sw = $('#site_nav').outerWidth();
	var uw = user_nav.outerWidth();
	var bw = brand.outerWidth(true);

	$(window).resize(function() {
		calcNav();
	});
	
	function calcNav() {
		var nw = $('.navbar-inner').width();
		var w = nw - sw - uw - bw - 20;
		var wh = false;
		
		if(w <= 0) {
			$('.text, .divider-vertical', user_nav).hide();
			wh = true;
		} else {
			wh = false;
			$('.text, .divider-vertical', user_nav).show();
		}
		
		if(wh) {
			var unw = $(user_nav).outerWidth();
			var nw = w + uw - unw;
			
			if(w <= 0 && nw < 0) brand.hide();
			else brand.fadeIn();
				
		} else brand.fadeIn();
	};
	
	calcNav();
}).add('spoiler', function() {
	var icon_open = 'icon-chevron-up',
		icon_close = 'icon-chevron-down';

	$('.spoiler-toggle')
		.click(function () {
			var $self = $(this);
			var $spoiler_cont = $('.spoiler');
			
			if($(this).data('spoiler')) {
				$spoiler_cont = $($(this).data('spoiler'));
			}
		
			$spoiler_cont.slideToggle('fast', function() {
				var $icon = $self.find('.spoiler-toggle-icon');
				if($(this).is(':hidden')) {
					$icon.removeClass(icon_open).addClass(icon_close);
				} else {
					$icon.addClass(icon_open).removeClass(icon_close);
				}
			});
			
			return false;
		}).each(function() {
			if($(this).data('hash') == window.location.hash.substring(1))
			{
				$(this).click();
				$('html,body').animate({scrollTop: $(this).offset().top}, 'slow');
			}
			
		})
		.find('h3, h4')
		.append(' <i class="spoiler-toggle-icon '+icon_close+'"></i>');
}).add('datepicker', function() {
	$('.datetimepicker').datetimepicker({
		format:'Y-m-d H:i:00',
		lang: LOCALE,
		dayOfWeekStart:1
	});
	
	$('.datepicker').datetimepicker({
		timepicker: false,
		format: 'Y-m-d',
		lang: LOCALE,
		dayOfWeekStart:1
	});
	
	$('.timepicker').datetimepicker({
		timepicker: true,
		datepicker: false,
		format: 'H:i:s',
		lang: LOCALE,
		dayOfWeekStart:1
	});
}).add('slug', function() {
	// Slug & metadata
    var slugs = {};
    $('body').on('keyup', '.slug-generator', function () {
		var $slug_cont = $('.slug');

		if($(this).data('slug')) {
			$slug_cont = $($(this).data('slug'));
		}
		
		$separator = '-';
		if($(this).data('separator')) {
			$separator = $(this).data('separator');
		}
		
        if ($slug_cont.val() == '')
            slugs[$slug_cont] = true;

        if (slugs[$slug_cont]) {
            var new_slug = cms.convertSlug($(this).val(), $separator);

            $slug_cont.val(new_slug);
        }
    });

	$('body').on('keyup', '.slug', function () {
		var c = String.fromCharCode(event.keyCode);
		var isWordcharacter = c.match(/\w/);
		
		if( ! isWordcharacter && event.keyCode != '32') return;
		
		$separator = '-';
		if($(this).data('separator')) {
			$separator = $(this).data('separator');
		}
		
		$(this).val(cms.convertSlug($(this).val(), $separator));
		slugs[$(this)] = false;

		if ($(this).val() == '')
			slugs[$(this)] = true;
	});
}).add('focus', function() {
	$('.focus').focus();
}).add('dropzone', function() {
	cms.uploader = new Dropzone('.dropzone', {
		success: function(file, r) {
			var response = $.parseJSON(r);
			var self = this;
			if(response.code != 200) {
				cms.message(response.message, 'error');
				
			} else if(response.message) {
				cms.message(response.message);
			}
			
			$(file.previewElement).fadeOut(500, function() {
				self.removeFile(file);
			});
		},
		error: function(file, message) {
			cms.message(message, 'error');
			this.removeFile(file);
		},
        dictDefaultMessage: __("Drop files here to upload"),
        dictFallbackMessage: __("Your browser does not support drag'n'drop file uploads."),
        dictFallbackText: __("Please use the fallback form below to upload your files like in the olden days."),
        dictFileTooBig: __("File is too big ({{filesize}}MB). Max filesize: {{maxFilesize}}MB."),
        dictInvalidFileType: __("You can't upload files of this type."),
        dictResponseError: __("Server responded with {{statusCode}} code."),
        dictCancelUpload: __("Cancel upload"),
        dictCancelUploadConfirmation: __("Are you sure you want to cancel this upload?"),
        dictRemoveFile: __("Remove file"),
        dictMaxFilesExceeded: __("You can only upload {{maxFiles}} files."),
	});
}).add('loader', function() {
    cms.loader.init();
}).add('fancybox', function() {
    $(".fancybox-image").fancybox();
}).add('popup', function() {
	$(".popup").fancybox({
		fitToView	: true,
		autoSize	: false,
		width		: '99%',
		height		: '99%',
		openEffect	: 'none',
		closeEffect	: 'none',
		beforeLoad: function() {
			this.href = updateQueryStringParameter(this.href, 'type', 'iframe');
			this.title = this.element.html();
			
			cms.popup_target = this.element;
		},
		helpers : {
    		title : {
    			type : 'inside'
    		}
    	}
	});

	var method = ACTION == 'add' ? 'put' : 'post';
	var $form_actions = $('.iframe .form-actions');
	
	var $action = CONTROLLER;

	if((typeof API_FORM_ACTION != 'undefined'))
		$action = API_FORM_ACTION;

	$('.btn-save', $form_actions).on('click', function(e) {
		var $data = $('form').serializeObject();
		Api[method]($action, $data);
		
		e.preventDefault();
	});

	$('.btn-save-close', $form_actions).on('click', function(e) {
		var $data = $('form').serializeObject();
		Api[method]($action, $data, function(response) {
			window.top.$.fancybox.close();
		});
		e.preventDefault();
	});

	$('.btn-close', $form_actions).on('click', function(e) {
		window.top.$.fancybox.close();
		e.preventDefault();
	});
}).add('select2', function() {
	$('select').not('.no-script').select2();
	$('.tags').select2({
		tags: [],
		minimumInputLength: 0,
		tokenSeparators: [TAG_SEPARATOR],
		createSearchChoice: function(term, data) {
			if ($(data).filter(function() {
				return this.text.localeCompare(term) === 0;
			}).length === 0) {
				return {
					id: term,
					text: term
				};
			}
		},
		multiple: true,
		ajax: {
			url: '/api-tags',
			dataType: "json",
			data: function(term, page) {
				return {term: term};
			},
			results: function(data, page) {
				if(!data.response) return {results: []};
				return {results: data.response};
			}
		},
		initSelection: function(element, callback) {
			var data = [];
			
			var tags = element.val().split(",");
			for(i in tags) {
				data.push({
					id: tags[i],
					text: tags[i]
				});
			};
			callback(data);
		}
	});
}).add('ajax_form', function() {
	$('body').on('submit', 'form.form-ajax', function() {
		var $self = $(this);
		var $buttons = $('button', $self)
			.attr('disabled', 'disabled');

		Api.post($self.attr('action'), $self.serialize(), function(response) {
			setTimeout(function() {
				$buttons.removeAttr('disabled');
			}, 5000);
		});

		return false;
	});
}).add('filemanager', function() {
	var input = $('input.input-filemanager:not(.init)')
		.addClass('init');
	
	$('<button class="btn" type="button"><i class="icon-folder-open"></i></button>')
		.insertAfter(input)
		.on('click', function() {
			cms.filemanager.open($(this).prev());
		});
		
	$('body').on('click', '.btn-filemanager', function() {
		var el = $(this).data('el');
		var type = $(this).data('type');

		if(!el) return false;
		
		cms.filemanager.open(el, type);
		return false;
	});
});

var Api = {
	_response: null,

	get: function(uri, data, callback) {
		this.request('GET', uri, data, callback);
		
		return this.response();
	},
	post: function(uri, data, callback) {
		this.request('POST', uri, data, callback);
		
		return this.response();
	},
	put: function(uri, data, callback) {
		this.request('PUT', uri, data, callback);
		
		return this.response();
	},

	'delete': function(uri, data, callback) {
		this.request('DELETE', uri, data, callback);
		
		return this.response();
	},

	request: function(method, uri, data, callback, show_loader) {
		uri = uri.replace('/' + ADMIN_DIR_NAME,'');
		
		if(uri.indexOf('-') == -1)
		{
			uri = '-' + uri;
		}
		else if(uri.indexOf('-') > 0 && uri.indexOf('/') == -1)
		{
			uri = SITE_URL + '/' + uri;
		}
		
		if(uri.indexOf('/api') == -1)
		{
			uri = SITE_URL + 'api' + uri;
		}
		
		if(show_loader == 'undefined')
			show_loader = true;
		
		$.ajaxSetup({
			contentType : 'application/json'
		});

		if(typeof(data) == 'object' && method != 'GET') 
			data = JSON.stringify(data);

		$.ajax({
			type: method,
			url: uri,
			data: data,
			dataType: 'json',
			beforeSend: function(){
				if(show_loader) cms.loader.show();
			},
			success: function(response) {

				if(response.code != 200) {
					if(typeof(callback) == 'function') callback(response);
					return Api.exception(response);
				}
				
				if (response.message) {
					cms.clear_error();

					if(response.message instanceof Object) {
						parse_messages(response.message);
					} else {
						cms.message(response.message);
					}
				}
	
				if(response.redirect) {
					$.get(window.top.CURRENT_URL, function(resp){
						window.location = response.redirect + '?type=iframe';
					});
				}
				this._response = response;
				
				var $event = method + uri.replace(/\//g, ':');
				window.top.$('body').trigger($event.toLowerCase(), [this._response.response]);

				if(typeof(callback) == 'function') callback(this._response);
			},
			error: function(jqXHR, textStatus, errorThrown) {
				if(typeof(callback) == 'function') callback(textStatus);
			}
		}).always(function() { 
			cms.loader.hide();
		});;
	},

	exception: function(response) {
		if(response.code == 120 && typeof(response.errors) == 'object') {
			for(i in response.errors) {
				cms.message(response.errors[i], 'error');
				cms.error_field(i, response.errors[i]);
			}
		} else if (response.message) {
			cms.message(response.message, 'error');
		}
	},
	response: function() {
		return this._response;
	}
};

// Run
$(document).ready(function() {
    cms.messages.init();
    cms.init.run();
    cms.ui.init();
	
	parse_messages(MESSAGE_ERRORS, 'error');
	parse_messages(MESSAGE_SUCCESS);
});

(function($) {
	$.fn.check = function () {
		return this.each(function () {
			this.checked = true;
		});
	};

	$.fn.uncheck = function () {
		return this.each(function () {
			this.checked = false;
		});
	};

	$.fn.checked = function () {
		return this.attr('checked');
	};

	$.fn.tabs = function () {
		return $('li a', this).on('click', function() {
			$(this)
				.parent()
				.addClass('active')
				.siblings()
				.removeClass('active');

			$('div.tab-pane').removeClass('active');
			$($(this).attr('href')).addClass('active');

			return false;
		});
	};

	$.fn.serializeObject = function()
	{
		var o = {};
		var a = this.serializeArray();
		$.each(a, function() {
			if (o[this.name] !== undefined) {
				if (!o[this.name].push) {
					o[this.name] = [o[this.name]];
				}
				o[this.name].push(this.value || '');
			} else {
				o[this.name] = this.value || '';
			}
		});
		return o;
	};

    $.QueryString = (function(a) {
        if (a == "") return {};
        var b = {};
        for (var i = 0; i < a.length; ++i)
        {
            var p=a[i].split('=');
            if (p.length != 2) continue;
            b[p[0]] = decodeURIComponent(p[1].replace(/\+/g, " "));
        }
        return b;
    })(window.location.search.substr(1).split('&'));

})(jQuery);

jQuery.browser = {};
jQuery.browser.mozilla = /mozilla/.test(navigator.userAgent.toLowerCase()) && !/webkit/.test(navigator.userAgent.toLowerCase());
jQuery.browser.webkit = /webkit/.test(navigator.userAgent.toLowerCase());
jQuery.browser.opera = /opera/.test(navigator.userAgent.toLowerCase());
jQuery.browser.msie = /msie/.test(navigator.userAgent.toLowerCase());

function updateQueryStringParameter(uri, key, value) {
	var re = new RegExp("([?|&])" + key + "=.*?(&|$)", "i");
	separator = uri.indexOf('?') !== -1 ? "&" : "?";
	if (uri.match(re)) {
		return uri.replace(re, '$1' + key + "=" + value + '$2');
	}
	else {
		return uri + separator + key + "=" + value;
	}
}

function parse_messages($messages, $type) {
	for(text in $messages) {
		if(text == '_external') {
			parse_messages($messages[text], $type);
			continue;
		}
		
		if($type == 'error'){
			cms.error_field(text, $messages[text]);
		}

		cms.message($messages[text], $type);
	}
}

function strtr (str, from, to) {
	if (typeof from === 'object') {
		var cmpStr = '';
		for (var j=0; j < str.length; j++){
			cmpStr += '0';
		}
		var offset = 0;
		var find = -1;
		var addStr = '';
		for (fr in from) {
			offset = 0;
			while ((find = str.indexOf(fr, offset)) != -1){
				if (parseInt(cmpStr.substr(find, fr.length)) != 0){
					offset = find + 1;
					continue;
				}
				for (var k =0 ; k < from[fr].length; k++){
					addStr += '1';
				}
				cmpStr = cmpStr.substr(0, find) + addStr + cmpStr.substr(find + fr.length, cmpStr.length - (find + fr.length));
				str = str.substr(0, find) + from[fr] + str.substr(find + fr.length, str.length - (find + fr.length));
				offset = find + from[fr].length + 1;
				addStr = '';
			}
		}
		return str;
	}

	for(var i = 0; i < from.length; i++) {
		str = str.replace(new RegExp(from.charAt(i),'g'), to.charAt(i));
	}

	return str;
}

var __ = function (str, values) {

    if (cms.translations[str] !== undefined)
    {
    	var str = cms.translations[str];
    }
    return values == undefined ? str : strtr(str, values);
};

/*! Copyright (c) 2011 Piotr Rochala (http://rocha.la)
 * Dual licensed under the MIT (http://www.opensource.org/licenses/mit-license.php)
 * and GPL (http://www.opensource.org/licenses/gpl-license.php) licenses.
 *
 * Version: 1.3.2
 *
 */
(function(e){jQuery.fn.extend({slimScroll:function(n){var r={width:"auto",height:"250px",size:"7px",color:"#000",position:"right",distance:"1px",start:"top",opacity:.4,alwaysVisible:false,disableFadeOut:false,railVisible:false,railColor:"#333",railOpacity:.2,railDraggable:true,railClass:"slimScrollRail",barClass:"slimScrollBar",wrapperClass:"slimScrollDiv",allowPageScroll:false,wheelStep:20,touchScrollStep:200,borderRadius:"7px",railBorderRadius:"7px"};var i=e.extend(r,n);this.each(function(){function x(t){if(!r){return}var t=t||window.event;var n=0;if(t.wheelDelta){n=-t.wheelDelta/120}if(t.detail){n=t.detail/3}var s=t.target||t.srcTarget||t.srcElement;if(e(s).closest("."+i.wrapperClass).is(m.parent())){T(n,true)}if(t.preventDefault&&!v){t.preventDefault()}if(!v){t.returnValue=false}}function T(e,t,n){v=false;var r=e;var s=m.outerHeight()-E.outerHeight();if(t){r=parseInt(E.css("top"))+e*parseInt(i.wheelStep)/100*E.outerHeight();r=Math.min(Math.max(r,0),s);r=e>0?Math.ceil(r):Math.floor(r);E.css({top:r+"px"})}c=parseInt(E.css("top"))/(m.outerHeight()-E.outerHeight());r=c*(m[0].scrollHeight-m.outerHeight());if(n){r=e;var u=r/m[0].scrollHeight*m.outerHeight();u=Math.min(Math.max(u,0),s);E.css({top:u+"px"})}m.scrollTop(r);m.trigger("slimscrolling",~~r);k();L()}function N(){if(window.addEventListener){this.addEventListener("DOMMouseScroll",x,false);this.addEventListener("mousewheel",x,false)}else{document.attachEvent("onmousewheel",x)}}function C(){l=Math.max(m.outerHeight()/m[0].scrollHeight*m.outerHeight(),d);E.css({height:l+"px"});var e=l==m.outerHeight()?"none":"block";E.css({display:e})}function k(){C();clearTimeout(a);if(c==~~c){v=i.allowPageScroll;if(h!=c){var e=~~c==0?"top":"bottom";m.trigger("slimscroll",e)}}else{v=false}h=c;if(l>=m.outerHeight()){v=true;return}E.stop(true,true).fadeIn("fast");if(i.railVisible){w.stop(true,true).fadeIn("fast")}}function L(){if(!i.alwaysVisible){a=setTimeout(function(){if(!(i.disableFadeOut&&r)&&!s&&!u){E.fadeOut("slow");w.fadeOut("slow")}},1e3)}}var r,s,u,a,f,l,c,h,p="<div></div>",d=30,v=false;var m=e(this);if(m.parent().hasClass(i.wrapperClass)){var g=m.scrollTop();E=m.parent().find("."+i.barClass);w=m.parent().find("."+i.railClass);C();if(e.isPlainObject(n)){if("height"in n&&n.height=="auto"){m.parent().css("height","auto");m.css("height","auto");var y=m.parent().parent().height();m.parent().css("height",y);m.css("height",y)}if("scrollTo"in n){g=parseInt(i.scrollTo)}else if("scrollBy"in n){g+=parseInt(i.scrollBy)}else if("destroy"in n){E.remove();w.remove();m.unwrap();return}T(g,false,true)}return}if(m.data("height")){i.height=m.data("height")}else{i.height=n.height=="auto"?m.parent().height():n.height}var b=e(p).addClass(i.wrapperClass).css({position:"relative",overflow:"hidden",width:i.width,height:i.height});m.css({overflow:"hidden",width:i.width,height:i.height});var w=e(p).addClass(i.railClass).css({width:i.size,height:"100%",position:"absolute",top:0,display:i.alwaysVisible&&i.railVisible?"block":"none","border-radius":i.railBorderRadius,background:i.railColor,opacity:i.railOpacity,zIndex:90});var E=e(p).addClass(i.barClass).css({background:i.color,width:i.size,position:"absolute",top:0,opacity:i.opacity,display:i.alwaysVisible?"block":"none","border-radius":i.borderRadius,BorderRadius:i.borderRadius,MozBorderRadius:i.borderRadius,WebkitBorderRadius:i.borderRadius,zIndex:99});var S=i.position=="right"?{right:i.distance}:{left:i.distance};w.css(S);E.css(S);m.wrap(b);m.parent().append(E);m.parent().append(w);if(i.railDraggable){E.bind("mousedown",function(n){var r=e(document);u=true;t=parseFloat(E.css("top"));pageY=n.pageY;r.bind("mousemove.slimscroll",function(e){currTop=t+e.pageY-pageY;E.css("top",currTop);T(0,E.position().top,false)});r.bind("mouseup.slimscroll",function(e){u=false;L();r.unbind(".slimscroll")});return false}).bind("selectstart.slimscroll",function(e){e.stopPropagation();e.preventDefault();return false})}w.hover(function(){k()},function(){L()});E.hover(function(){s=true},function(){s=false});m.hover(function(){r=true;k();L()},function(){r=false;L()});m.bind("touchstart",function(e,t){if(e.originalEvent.touches.length){f=e.originalEvent.touches[0].pageY}});m.bind("touchmove",function(e){if(!v){e.originalEvent.preventDefault()}if(e.originalEvent.touches.length){var t=(f-e.originalEvent.touches[0].pageY)/i.touchScrollStep;T(t,true);f=e.originalEvent.touches[0].pageY}});C();if(i.start==="bottom"){E.css({top:m.outerHeight()-E.outerHeight()});T(0,true)}else if(i.start!=="top"){T(e(i.start).position().top,null,true);if(!i.alwaysVisible){E.hide()}}N()});return this}});jQuery.fn.extend({slimscroll:jQuery.fn.slimScroll})})(jQuery)