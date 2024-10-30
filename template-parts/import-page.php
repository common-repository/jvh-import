<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$notifications = new \JVH\Import\Notifications();

?>

<div class="wrap">
	<h1>JVH Import</h1>

	<?php

	if ( $notifications->hasNotification() ) {
		$notifications->display();
	}

	?>

	<h2>
		<?php _e( 'What do you want to import?', 'jvhimport' ); ?>
	</h2>

	<ol>
		<?php if ( $this->isFluentFormsActivated() ) { ?>
			<li>
				<a href="<?php echo get_admin_url() . 'tools.php?page=import-jvh-feed&subpage=fluentforms'; ?>">
					Fluent Forms
				</a>
			</li>
		<?php } ?>
		<?php if ( $this->isVcTemplatesActivated() ) { ?>
			<li>
				<a href="<?php echo get_admin_url() . 'tools.php?page=import-jvh-feed&subpage=vctemplates'; ?>">
					JVH VC Templates
				</a>
			</li>
		<?php } ?>
			<li>
				<a href="<?php echo get_admin_url() . 'tools.php?page=import-jvh-feed&subpage=pages'; ?>">
					Pages
				</a>
			</li>
	</ol>

	<?php $this->renderSubpage(); ?>
</div>
