<?php defined('SYSPATH') or die('No direct access allowed.'); ?>

<ul data-level="<?php echo $level; ?>" class="unstyled">
	<?php foreach($childrens as $child): ?>
	<?php if($child instanceof Model_Page): ?>
	<li data-id="<?php echo $child->id; ?>" <?php if($child->is_expanded) echo('class="item-expanded"'); ?>>
		<div class="item">
			<div class="row-fluid">
				<div class="title span7">
					
					<?php if( $child->has_children ): ?>
					<?php
						if($child->is_expanded)
						{
							echo UI::icon( 'minus item-expander item-expander-expand');
						}
						else
						{
							echo UI::icon( 'plus item-expander');
						}
					?>
					<?php endif; ?>
					
					
					<?php if( ! ACL::check('page.edit') OR ! AuthUser::hasPermission( $child->get_permissions() ) ): ?>
					<?php echo UI::icon('lock'); ?>
					<?php echo $child->title; ?>
					<?php else: ?>
					<?php 
						echo UI::icon('file') . ' ';
						echo HTML::anchor( $child->get_url(), $child->title );
					?>
					<?php endif; ?>				
					<?php if( !empty($child->behavior_id) ): ?> <?php echo UI::label(__(ucfirst(Inflector::humanize( $child->behavior_id ))), 'default'); ?><?php endif; ?>
					
					<?php echo $child->get_public_anchor(); ?>
				</div>
				<div class="date span2">
					<?php echo Date::format($child->published_on); ?>
				</div>
				<div class="status span2">
					<?php echo $child->get_status(); ?>
				</div>
				<div class="actions span1">
					<?php if ( Acl::check( 'page.add')): ?>
					<?php echo UI::button(NULL, array(
						'href' => Route::get('backend')->uri(array(
							'controller' => 'page',
							'action' => 'add',
							'id' => $child->id
						)), 
						'icon' => UI::icon('plus'), 
						'class' => 'btn btn-mini'
					)); ?>
					<?php endif; ?>
					<?php if (Acl::check( 'page.delete')): ?>
					<?php echo UI::button(NULL, array(
						'href' => Route::get('backend')->uri(array(
							'controller' => 'page',
							'action' => 'delete',
							'id' => $child->id
						)), 'icon' => UI::icon('remove icon-white'), 
						'class' => 'btn btn-mini btn-confirm btn-danger'
					)); ?>
					<?php endif; ?>
				</div>
			</div>
		</div>
		
	<?php if( $child->is_expanded ) echo($child->children_rows); ?>
	<?php else: ?>
	<li>
		<div class="item">
			<div class="row-fluid">
				<div class="title span12">
					<?php echo $child; ?>
				</div>
			</div>
		</div>
	
	<?php endif; ?>
	</li>
	<?php endforeach; ?>
</ul>