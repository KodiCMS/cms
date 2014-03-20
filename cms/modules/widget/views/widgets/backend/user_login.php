<div class="widget-content ">
	<?php 
	
	echo Bootstrap_Form_Element_Control_Group::factory(array(
		'element' => Bootstrap_Form_Element_Input::factory(array(
			'name' => 'login_field', 'value' => $widget->get('login_field')
		))
		->label(__('Login ID (POST)'))
	));
	
	echo Bootstrap_Form_Element_Control_Group::factory(array(
		'element' => Bootstrap_Form_Element_Input::factory(array(
			'name' => 'password_field', 'value' => $widget->get('password_field')
		))
		->label(__('Password ID (POST)'))
	));
	
	echo Bootstrap_Form_Element_Control_Group::factory(array(
		'element' => Bootstrap_Form_Element_Input::factory(array(
			'name' => 'next_url', 'value' => $widget->get('next_url')
		))
		->label(__('Next page by default (URI)'))
		->attributes('class', Bootstrap_Form_Element_Input::XXLARGE)
	));
	?>
	<hr />
	<?php
	echo Bootstrap_Form_Element_Control_Group::factory(array(
		'element' => Bootstrap_Form_Element_Checkbox::factory(array(
			'name' => 'remember', 'value' => 1
		))
		->checked($widget->remember)
		->label(__('Allow Autologin'))
	));
	
	echo Bootstrap_Form_Element_Control_Group::factory(array(
		'element' => Bootstrap_Form_Element_Input::factory(array(
			'name' => 'remember_field', 'value' => $widget->get('remember_field')
		))
		->label(__('Autologin ID'))
	));
	?>
</div>

<script>
$(function() {
	$('button[name="new_rule"]').on('click', function() {
		var $cont = $('.roles-redirect-contaier');
		var $item = $('.roles-redirect-item:last-child');
		var $key = $('.roles-redirect-item').length;
		
		$item
			.clone()
			.find('.select2-container')
				.remove()
				.end()
			.find('select')
				.attr('name', 'roles_redirect[' + $key + '][roles][]')
				.find('option:selected')
					.removeAttr('selected')
					.end()
				.select2()
				.end()
			.find('input')
				.val('')
				.attr('name', 'roles_redirect[' + $key + '][next_url]')
				.end()
			.appendTo($cont);
		return false;
	});
});
</script>
<div class="widget-content ">
	<div class="roles-redirect-contaier">
		<?php foreach($widget->roles_redirect as $key => $data): ?>
		<div class="roles-redirect-item">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<div class="clearfix"></div>
			<?php
				echo Bootstrap_Form_Element_Control_Group::factory(array(
					'element' => Bootstrap_Form_Element_Select::factory(array(
						'name' => 'roles_redirect['.$key.'][roles][]', 'options' => $roles
					))
					->attributes('class', Bootstrap_Form_Element_Input::BLOCK_LEVEL)
					->selected(Arr::get($data, 'roles', array()))
					->label(__('Roles'))
				));

				echo Bootstrap_Form_Element_Control_Group::factory(array(
					'element' => Bootstrap_Form_Element_Input::factory(array(
						'name' => 'roles_redirect['.$key.'][next_url]', 'value' => Arr::get($data, 'next_url')
					))
					->label(__('Next page (URI)'))
					->attributes('class', Bootstrap_Form_Element_Input::XXLARGE)
				));
			?>
			<hr />
		</div>
		<?php endforeach; ?>
	</div>
	<?php echo Bootstrap_Form_Element_Button::factory(array(
		'title' => __('Add new rule'), 'name' => 'new_rule'
	))
	->pull_right()
	->icon('plus'); 
	?>
</div>