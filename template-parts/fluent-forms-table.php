<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$feed = new \JVH\Import\FluentFormsFeed();

?>

<h2>Fluent Forms</h2>

<table class="wp-list-table widefat fixed striped table-view-list">
	<thead>
		<tr>
			<th scope="col" id="fluent_form_id" class="manage-column column-fluent-form-id" style="width: 50px;">Form id</th>
			<th scope="col" id="fluent_form_title" class="manage-column column-fluent-form-title">Title</th>
			<th scope="col" id="vctemplate_image" class="manage-column column-vctemplate-image">
				<?php _e( 'Image', 'jvhimport' ); ?>
			</th>
		</tr>
	</thead>

	<tbody>
		<?php foreach( $feed->getFormsWithScreens() as $form ) { ?>
			<tr>
				<td>
					<?php echo intval( $form['id'] ); ?>
				</td>
				<td>
					<?php echo esc_html( $form['title'] ); ?>
					<br />
					<a href="<?php echo get_admin_url(); ?>/tools.php?page=import-jvh-feed&subpage=fluentforms&import-fluent-form=<?php echo intval( $form['id'] ); ?>">Import</a>
					<br />
					<a href="https://import.jvh.software/?fluent_forms_pages=1&design_mode=1&preview_id=<?php echo intval( $form['id'] ); ?>#ff_preview" target="_blank">Preview</a>
					<td>
						<?php if ( isset( $form['image_url'] ) && ! empty( $form['image_url'] ) ) { ?>
							<img src="<?php echo esc_url( $form['image_url'] ); ?>" style="max-width: 100%;" />
						<?php } ?>
					</td>
				</td>
			</tr>
		<?php } ?>
	</tbody>
</table>
