<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$feed = new \JVH\Import\VcTemplatesFeed();

?>

<h2>VC Templates</h2>

<p>
	When you import VC Templates, any extra classes will also be imported.
</p>

<form>
	<input type="hidden" name="page" value="import-jvh-feed" />
	<input type="hidden" name="subpage" value="vctemplates" />

	<select name="category-vc-template">
		<option value="">Choose a category</option>

		<?php foreach ( $feed->getCategoryNames() as $category ) { ?>
			<option><?php echo $category; ?></option>
		<?php } ?>
	</select>

	<input type="submit" id="post-query-submit" class="button">
</form>

<table class="wp-list-table widefat fixed striped table-view-list">
	<thead>
		<tr>
			<th scope="col" id="vctemplate_title" class="manage-column column-vctemplate-title">
				<?php _e( 'Title', 'jvhimport' ); ?>
			</th>
			<th scope="col" id="vctemplate_categories" class="manage-column column-vctemplate-categories">
				<?php _e( 'Categories', 'jvhimport' ); ?>
			</th>
			<th scope="col" id="vctemplate_image" class="manage-column column-vctemplate-image">
				<?php _e( 'Image', 'jvhimport' ); ?>
			</th>
		</tr>
	</thead>

	<tbody>
		<?php foreach( $feed->getTemplates() as $post_id => $template ) { ?>
			<tr>
				<td>
					<?php echo esc_html( $template->title ); ?>
					<br />
					<a href="<?php echo get_admin_url(); ?>/tools.php?page=import-jvh-feed&subpage=vctemplates&import-vctemplate=<?php echo intval( $template->post_id ); ?>">Import</a>
					<br />
					<a href="https://import.jvh.software/?p=<?php echo intval( $template->post_id ); ?>" target="_blank">Preview</a>
				</td>
				<td>
					<?php echo implode( ', ', $template->categories ); ?>
				</td>
				<td>
					<img src="<?php echo esc_url( $template->image_url ); ?>" style="max-width: 100%;" />
				</td>
			</tr>
		<?php } ?>
	</tbody>
</table>
