<script type="text/javascript">
$(function() {
	$('#snippet-select').change(function() {
		var $option = $('option:selected', this);
		if($option.val() == 0)
			$('#snippet-edit-button').hide();
		else
			$('#snippet-edit-button')
				.show()
				.css({
					display: 'inline-block'
				})
				.attr('href', BASE_URL + '/snippet/edit/' + $option.val())
	});

	$('body').on('post:api-snippet', update_snippets_list);
	$('body').on('put:api-snippet', update_snippets_list);
})


function update_snippets_list(e, response) {
	var select = $('#snippet-select');

	select
		.append($('<option>', {value: response.name, text: response.name}))
		.select2('val', response.name)
		.change();
}
</script>

<?php

if(empty($templates))
{
	$templates = array(
		__('--- Not set ---')
	);
	$snippets = Model_File_Snippet::find_all();

	foreach ($snippets as $snippet)
	{
		$templates[$snippet->name] = $snippet->name;
	}
}

if(empty($template)) $template = NULL;
if(empty($default)) $default = NULL;
if(empty($select_name)) $select_name = 'template';

$hidden = empty($template) ? 'hidden' : '';
?>

<?php if(!empty($header)): ?>
<div class="widget-header">
	<h4><?php echo $header; ?></h4>
</div>
<?php endif; ?>
<div class="widget-content">
	<div class="control-group">
		<label class="control-label"><?php echo __('Snippet'); ?></label>
		<div class="controls">

			<?php echo Form::select( 'template', $templates, $template, array(
				'class' => 'input-medium', 'id' => 'snippet-select'
			) ); ?>

			<div class="btn-group">
				<?php if( ACL::check('snippet.edit')): ?>
				<?php  echo UI::button(__('Edit snippet'), array(
					'href' => Route::get('backend')->uri(array(
						'controller' => 'snippet', 
						'action' => 'edit',
						'id' => $template
					)), 'icon' => UI::icon('edit'),
					'class' => 'popup fancybox.iframe btn btn-primary '.$hidden, 'id' => 'snippet-edit-button'
				)); ?>
				<?php endif; ?>

				<?php if( ACL::check('snippet.add')): ?>
				<?php echo UI::button(__('Add snippet'), array(
					'href' => Route::get('backend')->uri(array(
						'controller' => 'snippet', 
						'action' => 'add'
					)),
					'icon' => UI::icon('plus'),
					'class' => 'popup fancybox.iframe btn btn-success'
				)); ?>
				<?php endif; ?>

				<?php echo $default; ?>
			</div>
		</div>
	</div>
</div>