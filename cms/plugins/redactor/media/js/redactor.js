if (!RedactorPlugins) var RedactorPlugins = {};

RedactorPlugins.elfinder = {
	init: function ()
	{
		var self = this;
		this.addBtnSeparator();
		this.addBtn('filemanager', 'elFinder', function() {
			cms.filemanager.open(self.$el.attr('id'));
		});
	},
};


RedactorPlugins.fullscreen = {
	init: function()
	{
		this.fullscreen = false;

		this.addBtn('fullscreen', 'Fullscreen', $.proxy(this.toggleFullscreen, this));
		this.setBtnRight('fullscreen');

		if (this.opts.fullscreen) this.toggleFullscreen();
	},
	toggleFullscreen: function()
	{
		var html;

		if (!this.fullscreen)
		{
			this.changeBtnIcon('fullscreen', 'normalscreen');
			this.setBtnActive('fullscreen');
			this.fullscreen = true;

			if (this.opts.toolbarExternal)
			{
				this.toolcss = {};
				this.boxcss = {};
				this.toolcss.width = this.$toolbar.css('width');
				this.toolcss.top = this.$toolbar.css('top');
				this.toolcss.position = this.$toolbar.css('position');
				this.boxcss.top = this.$box.css('top');
			}

			this.fsheight = this.$editor.height();

			if (this.opts.iframe) html = this.get();

			this.tmpspan = $('<span></span>');
			this.$box.addClass('redactor_box_fullscreen').after(this.tmpspan);

			$('body, html').css('overflow', 'hidden');
			$('body').prepend(this.$box);

			if (this.opts.iframe) this.fullscreenIframe(html);

			this.fullScreenResize();
			$(window).resize($.proxy(this.fullScreenResize, this));
			$(document).scrollTop(0, 0);

			this.getFocus();
		}
		else
		{
			this.removeBtnIcon('fullscreen', 'normalscreen');
			this.setBtnInactive('fullscreen');
			this.fullscreen = false;

			$(window).off('resize', $.proxy(this.fullScreenResize, this));
			$('body, html').css('overflow', '');

			this.$box
					.removeClass('redactor_box_fullscreen')
					.css({ width: 'auto', height: 'auto' });

			if (this.opts.iframe) html = this.$editor.html();
			this.tmpspan.after(this.$box).remove();

			if (this.opts.iframe) this.fullscreenIframe(html);
			else this.syncCode();

			var height = this.fsheight;
			if (this.opts.autoresize) height = 'auto';

			if (this.opts.toolbarExternal)
			{
				this.$box.css('top', this.boxcss.top);
				this.$toolbar.css({
					'width': this.toolcss.width,
					'top': this.toolcss.top,
					'position': this.toolcss.position
				});
			}

			if (!this.opts.iframe) this.$editor.css('height', height);
			else this.$frame.css('height', height);

			this.$editor.css('height', height);
			this.getFocus();
		}
	},
	fullscreenIframe: function(html)
	{
		this.$editor = this.$frame.contents().find('body').attr({
			'contenteditable': true,
			'dir': this.opts.direction
		});

		// set document & window
		if (this.$editor[0])
		{
			this.document = this.$editor[0].ownerDocument;
			this.window = this.document.defaultView || window;
		}

		// iframe css
		this.iframeAddCss();

		if (this.opts.fullpage) this.setFullpageOnInit(html);
		else this.set(html);

		if (this.opts.wym) this.$editor.addClass('redactor_editor_wym');
	},
	fullScreenResize: function()
	{
		if (!this.fullscreen) return false;

		var toolbarHeight = this.$toolbar.height();

		var pad = this.$editor.css('padding-top').replace('px', '');
		var height = $(window).height() - toolbarHeight;
		this.$box.width($(window).width() - 2).height(height + toolbarHeight);

		if (this.opts.toolbarExternal)
		{
			this.$toolbar.css({
				'top': '0px',
				'position': 'absolute',
				'width': '100%'
			});

			this.$box.css('top', toolbarHeight + 'px');
		}

		if (!this.opts.iframe) this.$editor.height(height - (pad * 2));
		else
		{
			setTimeout($.proxy(function()
			{
				this.$frame.height(height);

			}, this), 1);
		}

		this.$editor.height(height);
	}
};

/*
	Redactor object
*/
cms.plugins.redactor = {};


// Switch on tinymce handler
cms.plugins.redactor.switchOn_handler = function( textarea_id, params )
{
	var local_params = {
		focus: true,
		//wym: true,
		autoresize: false,
		lang: LOCALE,
		minHeight: 200
	};
	
	params = $.extend(local_params, params);

	return $('#' + textarea_id).redactor(params);
};

// Switch off tinymce handler
cms.plugins.redactor.switchOff_handler = function( editor, textarea_id )
{
	editor.destroyEditor();	
};

cms.plugins.redactor.exec_handler = function( editor, command, textarea_id, data )
{
	switch(command) {
		case 'insert':
			if (/(jpg|gif|png|JPG|GIF|PNG|JPEG|jpeg)$/.test(data)){
				data = '<img src="' + data + '">';
			} else if (/((ftp|http|https|gopher|mailto|news|nntp|telnet|wais|file|prospero|aim|webcal):(([A-Za-z0-9$_.+!*(),;/?:@&~=-])|%[A-Fa-f0-9]{2}){2,}(#([a-zA-Z0-9][a-zA-Z0-9$_.+!*(),;/?:@&~=%-]*))?([A-Za-z0-9$_+!*();/?:~-]))/.test(data)) {
				data = '<a href="' + data + '">' + data + '</a>';
			}

			editor.insertHtml(data);
			break;
		case 'changeHeight':
			editor.data('redactor').$editor.height(data);
	}

	return true;
};

/*
	When DOM init
*/
jQuery(function(){
	cms.filters
		.add( 'redactor', cms.plugins.redactor.switchOn_handler, cms.plugins.redactor.switchOff_handler, cms.plugins.redactor.exec_handler );
});