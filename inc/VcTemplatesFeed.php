<?php

namespace JVH\Import;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VcTemplatesFeed
{
	const URL = 'https://import.jvh.software/?jvh-import-feed=jvh-vc-template';

	public function getTemplates()
	{
		if ( $this->isFilteredFeed() ) {
			return $this->getFilteredTemplates();
		}
		else {
			return $this->getAllTemplates();
		}
	}

	public function getCategoryNames()
	{
		$categories = [];

		foreach ( $this->getAllTemplates() as $template ) {
			foreach ( $template->categories as $category ) {
				$categories[] = $category;
			}
		}

		return array_unique( $categories );
	}

	private function getFilteredTemplates()
	{
		return json_decode( $this->getFilteredJson() );
	}

	private function isFilteredFeed() : bool
	{
		return isset( $_GET['category-vc-template'] ) && ! empty( $_GET['category-vc-template'] );
	}

	private function getAllTemplates()
	{
		return json_decode( $this->getJson() );
	}

	private function getJson()
	{
		return file_get_contents( self::URL );
	}

	private function getFilteredJson()
	{
		return file_get_contents( $this->getFilteredUrl() );
	}

	private function getFilteredUrl()
	{
		return self::URL . '&category=' . str_replace( ' ', '%20', $_GET['category-vc-template'] );
	}

	public function importTemplateById( $template_id )
	{
		$template_id = intval( $template_id );
		$template = $this->getTemplate( $template_id );

		if ( ! is_object( $template ) ) {
			wp_redirect( get_admin_url() . '/tools.php?page=import-jvh-feed&subpage=vctemplates&result=failed' );
			return;
		}

		$this->importTemplate( $template );

		wp_redirect( get_admin_url() . '/tools.php?page=import-jvh-feed&subpage=vctemplates&result=success' );
	}

	public function importTemplate($template)
	{
		$post_id = wp_insert_post( [
			'post_title' => $template->title,
			'post_content' => $template->content,
			'post_status' => 'publish',
			'post_type' => 'jvh-vc-template',
		] );

		wp_set_post_terms( $post_id, $this->getTemplateCategoryIds( $template->categories ), 'category-jvh-template' );

		// Set featured image
		$image_id = media_sideload_image( $template->image_url, $post_id, $template->title, 'id' );
		update_post_meta( $post_id, '_thumbnail_id', $image_id );

		// Import extra classes
		if ( $this->shouldImportExtraClasses( $template->content ) ) {
			$this->importExtraClasses( $template->content );
		}
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

	private function getTemplate( $template_id )
	{
		$template_id = intval( $template_id );

		$templates = $this->getTemplates();

		if ( property_exists( $templates, $template_id ) ) {
			return $templates->$template_id;
		}
	}

	private function getTemplateCategoryIds( $category_names )
	{
		$ids = [];

		foreach ( $category_names as $category_name ) {
			$term = get_term_by( 'name', $category_name, 'category-jvh-template' );

			$ids[] = $term->term_id;
		}

		return $ids;
	}
}

if ( isset( $_GET['import-vctemplate'] ) ) {
	add_action( 'admin_init', function() {
		$template_id = intval( $_GET['import-vctemplate'] );

		$feed = new \JVH\Import\VcTemplatesFeed();
		$feed->importTemplateById( $template_id );
	} );
}
