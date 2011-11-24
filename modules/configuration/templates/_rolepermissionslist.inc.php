<ul class="simple_list">
<?php if (count($role->getPermissions())): ?>
	<?php foreach ($role->getPermissions() as $permission): ?>
		<?php $permission_details = TBGContext::getPermissionDetails($permission); ?>
		<li>
			<?php echo image_tag('action_ok.png', array('style' => 'margin: 2px 5px -2px 0;')).$permission_details['description']; ?>
		</li>
	<?php endforeach; ?>
<?php else: ?>
	<li class="faded_out"><?php echo __('This role does not have any associated permissions'); ?></li>
<?php endif; ?>
</ul>
