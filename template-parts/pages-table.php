<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$feed = new \JVH\Import\PagesFeed();

?>

<h2>Pages</h2>

<p>
	When you import pages, any extra classes, grids and facets will also be imported.
</p>

<table class="wp-list-table widefat fixed striped table-view-list">
	<thead>
		<tr>
			<th scope="col" id="vctemplate_title" class="manage-column column-vctemplate-title">
				<?php _e( 'Title', 'jvhimport' ); ?>
			</th>
		</tr>
	</thead>

	<tbody>
		<?php foreach( $feed->getPages() as $post_id => $page ) { ?>
			<tr>
				<td>
					<?php echo esc_html( $page->title ); ?>
					<br />
					<a href="<?php echo get_admin_url(); ?>/tools.php?page=import-jvh-feed&subpage=pages&import-page=<?php echo intval( $page->post_id ); ?>">Import</a>
					<br />
					<a href="https://import.jvh.software/?p=<?php echo intval( $page->post_id ); ?>" target="_blank">Preview</a>
				</td>
			</tr>
		<?php } ?>
	</tbody>
</table>
