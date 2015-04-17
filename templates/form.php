<div class="qbttc-form">
	<div class="wrap">
		<h2><?php echo $qbttc->get_form()->get_menu_title(); ?></h2>

		<p><?php _e('Please, select a taxonomy and input your preferred hierarchy.', 'qbttc'); ?></p>

		<p><?php _e('You can use the Hierarchy Indent Character to build a tree hierarchy for your taxonomies.', 'qbttc'); ?></p>

		<form method="post" action="options.php">
			<?php 
			settings_fields( $qbttc->get_form()->get_menu_id() );
			do_settings_sections( $qbttc->get_form()->get_menu_id() );
			submit_button(__('Bulk Insert', 'qbttc')); 
			?>
		</form>

	</div>
</div>