<script>
	function elfinderInit(id, type) {
		var elfinder = $('body').elfinder({
			lang: '<?php echo explode('-', I18n::lang())[0]; ?>',
			url : SITE_URL + 'api-elfinder',
			height: 595,
			resizable: false,
			getFileCallback : function(file) {
				if(_.isObject(file)) {
					file = file.url;
				}
				if(_.isObject(id)) {
					id.val(file);
					window.top.$.fancybox.close();
				}
				else {
					if(window.top.cms.filters.exec(id, 'insert', file))
						window.top.$.fancybox.close();
				}
			},
			uiOptions: {
				toolbar : [
					[<?php if(ACL::check('filemanager.mkdir')): ?>'mkdir'<?php endif; ?>, <?php if(ACL::check('filemanager.upload')): ?>'upload'<?php endif; ?>],
					['open', 'download'],
					['info'],
					['quicklook'],
					<?php if(ACL::check('filemanager.edit')): ?>['copy', 'cut', 'paste'],<?php endif; ?>
					<?php if(ACL::check('filemanager.delete')): ?>['rm'],<?php endif; ?>
					<?php if(ACL::check('filemanager.edit')): ?>['duplicate', 'rename', 'edit', 'resize'],<?php endif; ?>
					<?php if(ACL::check('filemanager.edit')): ?>['extract', 'archive'],<?php endif; ?>
					['search'],
					['view']
				]
			}
			<?php if(!ACL::check('filemanager.edit')): ?>,contextmenu: false<?php endif; ?>
			<?php if(!ACL::check('filemanager.upload')): ?>,dragUploadAllow: false<?php endif; ?>
			<?php if(!ACL::check('filemanager.edit')): ?>,allowShortcuts : false<?php endif; ?>
		}).elfinder('instance');
	};
</script>
