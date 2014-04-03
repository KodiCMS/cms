<script type="text/javascript">
	var EMAIL_TEMPLATE_ID = <?php echo (int) $template->id; ?>;
	
	$(function() {
		$('#email_types').on('change', function() {
			show_options($(this).val());
		});
		
		var message_type = $(':radio[name="message_type"]:checked').val();
		$(':radio[name="message_type"]').on('change', function() {
			message_type = $(this).val();
			change_message_redator(message_type)
		});
		
		change_message_redator(message_type);

		function change_message_redator(type) {
			if(type == '<?php echo Model_Email_Template::TYPE_HTML; ?>')
				cms.filters.switchOn( 'message', 'redactor' );
			else
				cms.filters.switchOff('message');
		}
		
		var activeInput;
		$(':input').not(':radio').not('select').add('.redactor_editor').on('focus', function() {
			activeInput = $(this);
		})
		
		$('#field_description').on('click', 'a', function() {
			var curInput = activeInput;

			if(!activeInput) return false;

			if(curInput.hasClass('redactor_editor') && message_type == '<?php echo Model_Email_Template::TYPE_HTML; ?>') {
				cms.filters.exec('message', 'insert', $(this).text());
			} else {
				var cursorPos = curInput.prop('selectionStart');
				var v = curInput.val();
				var textBefore = v.substring(0,  cursorPos );
				var textAfter  = v.substring( cursorPos, v.length );
				curInput.val( textBefore+ $(this).text() +textAfter );
			}
			
			return false;
		});

		show_options($('#email_types').val());
		function show_options(id) {
			Api.get('email-types.options', {uid: id}, function(resp) {
				var cont = $('#field_description .controls').empty();
				var ul = $('<ul class="unstyled" />').appendTo(cont);
				if(resp.response) {
					for(field in resp.response) {
						$('<li><a href="#">{'+field+'}</a> - ' + resp.response[field] + '</li>').appendTo(ul);
					}
				}
			})
		}
	});
	
</script>

<?php echo Form::open(Route::get('email_controllers')->uri(array('controller' => 'templates', 'action' => $action, 'id' => $template->id)), array(
	'class' => Bootstrap_Form::HORIZONTAL
)); ?>

<?php echo Form::hidden('token', Security::token()); ?>
<div class="widget">
	<div class="widget-header">
		<h3><?php echo __('General information'); ?></h3>
	</div>
	<div class="widget-content">
		<div class="control-group">
			<label class="control-label" for="status"><?php echo __( 'Email status' ); ?></label>
			<div class="controls">
				<?php echo Form::select( 'status', array(
					Model_Email_Template::ACTIVE => __('Active'), 
					Model_Email_Template::INACTIVE => __('Inactive')
					), (bool) $template->status, array(
					'id' => 'status'
				) ); ?>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="status"><?php echo __( 'Email send type' ); ?></label>
			<div class="controls">
				<?php echo Form::select( 'use_queue', array(
					Model_Email_Template::USE_DIRECT => __('Direct sending'),
					Model_Email_Template::USE_QUEUE => __('Use queue'), 
					), $template->use_queue, array(
					'id' => 'use_queue'
				) ); ?>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="email_type"><?php echo __( 'Email type' ); ?></label>
			<div class="controls">
				<?php
				echo Form::select( 'email_type', $types, $template->email_type, array(
					'id' => 'email_types'
				) );
				?>
				
				<?php if ( Acl::check( 'email_type.add')): ?>
				<?php echo UI::button(__('Add email type'), array(
					'href' => Route::get( 'email_controllers')->uri(array('controller' => 'types', 'action' => 'add')), 'icon' => UI::icon('plus'),
					'class' => 'btn btn-primary'
				)); ?>
				<?php endif; ?>
			</div>
		</div>
		<hr />
		
		<div class="control-group">
			<label class="control-label title" for="subject"><?php echo __( 'Email subject' ); ?></label>
			<div class="controls">
				<?php
				echo Form::input( 'subject', $template->subject, array(
					'class' => 'input-title input-block-level', 'id' => 'subject'
				) );
				?>
			</div>
		</div>
		
		<?php
			echo Bootstrap_Form_Element_Control_Group::factory(array(
				'element' => Bootstrap_Form_Element_Input::factory(array(
					'name' => 'email_from', 'value' => $template->email_from
				))
				->label(__('Email from'))
			));
			
			echo Bootstrap_Form_Element_Control_Group::factory(array(
				'element' => Bootstrap_Form_Element_Input::factory(array(
					'name' => 'email_to', 'value' => $template->email_to
				))
				->label(__('Email to'))
			));
		?>
	</div>
	<div class="widget-header">
		<h3><?php echo __('Email message'); ?></h3>
	</div>
	<div class="widget-content">
		<div class="control-group">
			<label class="control-label"><?php echo __( 'Message type' ); ?></label>
			<div class="controls">
				<label class="radio inline">
					<?php echo Form::radio('message_type', Model_Email_Template::TYPE_TEXT, $template->message_type == Model_Email_Template::TYPE_TEXT); ?> <?php echo __('Plain text'); ?>
				</label>
				<label class="radio inline">
					<?php echo Form::radio('message_type', Model_Email_Template::TYPE_HTML, $template->message_type == Model_Email_Template::TYPE_HTML); ?> <?php echo __('HTML'); ?>
				</label>
			</div>
		</div>
		
		<?php
			echo Bootstrap_Form_Element_Control_Group::factory(array(
				'element' => Bootstrap_Form_Element_Textarea::factory(array(
					'name' => 'message', 'body' => $template->message
				))
				->attributes('id', 'message')
				->attributes('class', 'input-block-level')
				->label(__('Email message'))
			));
		?>
		<div class="control-group" id="field_description"><div class="controls"></div></div>
	</div>
	<div class="form-actions widget-footer">
		<?php echo UI::actions($page_name); ?>
	</div>
</div>
<?php Form::close(); ?>