<?php

namespace JVH\Import;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PagesFeed
{
	const URL = 'https://import.jvh.software/?jvh-import-feed=pages';

	public function getPages()
	{
		return json_decode( $this->getJson() );
	}

	private function getJson()
	{
		return file_get_contents( self::URL );
	}

	public function importPageById( $page_id )
	{
		$page_id = intval( $page_id );
		$page = $this->getPage( $page_id );

		if ( ! is_object( $page ) ) {
			wp_redirect( get_admin_url() . '/tools.php?page=import-jvh-feed&subpage=pages&result=failed' );
			return;
		}

		$this->importPage( $page );

		wp_redirect( get_admin_url() . '/tools.php?page=import-jvh-feed&subpage=pages&result=success' );
	}

	private function getPage( $page_id )
	{
		$page_id = intval( $page_id );

		$pages = $this->getPages();

		if ( property_exists( $pages, $page_id ) ) {
			return $pages->$page_id;
		}
	}

	private function importPage( $page )
	{
		$post_id = wp_insert_post( [
			'post_title' => $page->title,
			'post_content' => $page->content,
			'post_status' => 'publish',
			'post_type' => 'page',
		] );

		// Import extra classes
		if ( $this->shouldImportExtraClasses( $page->content ) ) {
			$this->importExtraClasses( $page->content );
		}

		if ( $this->pageHasGrids( $page->content ) ) {
			$grid_ids = $this->getGridIdsFromPage( $page->content );

			$grids_feed = new \JVH\Import\GridsFeed();
			$grids_feed->importByIds( $grid_ids );

			$this->replaceGridIds( $post_id, $grid_ids );
		}

		if ( $this->pageHasFacets( $page->content ) ) {
			$facet_ids = $this->getFacetIdsFromPage( $page->content );

			$facets_feed = new \JVH\Import\FacetsFeed();
			$facets_feed->importByIds( $facet_ids );

			$this->replaceFacetIds( $post_id, $facet_ids );
		}
	}

	private function replaceGridIds( $post_id, $grid_ids )
	{
		$new_grid_ids = $this->getNewGridIds( $grid_ids );
		$post_content = get_post_field( 'post_content', $post_id );

		foreach ( $grid_ids as $key => $grid_id ) {
			$new_grid_id = $new_grid_ids[$key];

			$post_content = preg_replace( '/(\[wpgb_grid_jvh.*)id="' . $grid_id . '"/U', '$1id="' . $new_grid_id . '"', $post_content );
			$post_content = preg_replace( '/(\[wpgb_facet_jvh.*)grid="' . $grid_id . '"/U', '$1grid="' . $new_grid_id .'"', $post_content );
		}

		wp_update_post( [
			'ID' => $post_id,
			'post_content' => $post_content,
		] );
	}

	private function getNewGridIds( $old_grid_ids )
	{
		$new_grid_ids = [];

		$grids_feed = new \JVH\Import\GridsFeed();
		$grids = $grids_feed->getGrids( $old_grid_ids );

		foreach ( $grids as $grid ) {
			$new_grid_id = $this->getGridIdByName( $grid->name );
			$new_grid_ids[] = $new_grid_id;
		}

		return $new_grid_ids;
	}

	private function getGridIdByName( $name )
	{
			global $wpdb;

			$results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}wpgb_grids WHERE name = '{$name}' LIMIT 1", OBJECT );
			
			return $results[0]->id;
	}

	private function replaceFacetIds( $post_id, $facet_ids )
	{
		$new_facet_ids = $this->getNewFacetIds( $facet_ids );
		$post_content = get_post_field( 'post_content', $post_id );

		foreach ( $facet_ids as $key => $facet_id ) {
			$new_facet_id = $new_facet_ids[$key];

			$post_content = preg_replace( '/(\[wpgb_facet_jvh.*)id="' . $facet_id . '"/U', '$1id="' . $new_facet_id .'"', $post_content );
		}

		wp_update_post( [
			'ID' => $post_id,
			'post_content' => $post_content,
		] );
	}

	private function getNewFacetIds( $old_facet_ids )
	{
		$new_facet_ids = [];

		$facets_feed = new \JVH\Import\FacetsFeed();
		$facets = $facets_feed->getFacets( $old_facet_ids );

		foreach ( $facets as $facet ) {
			$new_facet_id = $this->getFacetIdByName( $facet->name );
			$new_facet_ids[] = $new_facet_id;
		}

		return $new_facet_ids;
	}

	private function getFacetIdByName( $name )
	{
			global $wpdb;

			$results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}wpgb_facets WHERE name = '{$name}' LIMIT 1", OBJECT );
			
			return $results[0]->id;
	}

	private function pageHasGrids( $page_content )
	{
		return count( $this->getGridIdsFromPage( $page_content ) ) > 0;
	}

	private function getGridIdsFromPage( $page_content )
	{
		preg_match_all( '/\[wpgb_grid_jvh.*id="(\d+)"/U', $page_content, $matches );

		return $matches[1];
	}

	private function pageHasFacets( $page_content )
	{
		return count( $this->getFacetIdsFromPage( $page_content ) ) > 0;
	}

	private function getFacetIdsFromPage( $page_content )
	{
		preg_match_all( '/\[wpgb_facet_jvh.* id="(\d+)"/U', $page_content, $matches );

		return $matches[1];
	}

	private function importExtraClasses( $content )
	{
		$classes = $this->extractExtraClasses( $content );
		$feed = new \JVH\Feed\ExtraClassesFeed();

		foreach ( $classes as $class_name ) {
			$class = $feed->importClassByName( $class_name );
		}
	}

	private function shouldImportExtraClasses( $content )
	{
		return $this->hasCssClassPostype() && $this->hasExtraClasses( $content );
	}

	private function hasExtraClasses( $content )
	{
		return count( $this->extractExtraClasses( $content ) ) > 0;
	}

	private function extractExtraClasses( $content )
	{
		$classes = [];

		preg_match_all( '/extra_css_class="(.*)"/U', $content, $matches );

		foreach ( $matches[1] as $classes_string ) {
			foreach ( explode( ',', $classes_string ) as $class ) {
				$classes[] = $class;
			}
		}

		return $classes;
	}

	private function hasCssClassPostype()
	{
		return post_type_exists( 'css-class' );
	}
}

if ( isset( $_GET['import-page'] ) ) {
	add_action( 'admin_init', function() {
		$page_id = intval( $_GET['import-page'] );

		$feed = new \JVH\Import\PagesFeed();

		$feed->importPageById( $page_id );
	} );
}
