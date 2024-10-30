<?php

namespace JVH\Feed;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ExtraClassesFeed
{
	const URL = 'https://import.jvh.software/?jvh-import-feed=css-classes';

	public function importClassByName( $class_name )
	{
		$class = $this->getClassByName( $class_name );

		$this->importClass( $class );
	}

	public function importClass( $class )
	{
		if ( $this->isExistingClass( $class['class'] ) ) {
			return;
		}

		$this->importCategories( $class['categories'] );

		$category_ids = $this->getCatogoryIds( $class['categories'] );

		$post_id = wp_insert_post( [
			'post_title' => $class['title'],
			'post_status' => 'publish',
			'post_type' => 'css-class',
			'meta_input' => [
				'css_class' => $class['class'],
				'css_styles' => $class['styles'],
			],
		] );

		wp_mail( 'jaap.huisman89@gmail.com', 'test image debug', print_r( $class, true ) );

		// Set featured image
		if ( isset( $class['image_url'] ) ) {
			$image_id = media_sideload_image( $class['image_url'], $post_id, $class['title'], 'id' );
			update_post_meta( $post_id, '_thumbnail_id', $image_id );
		}

		wp_set_post_terms( $post_id, $category_ids, 'css_class_categorie' );
	}

	private function isExistingClass( $css_class )
	{
		return count( $this->getClassPosts( $css_class ) ) > 0;
	}

	private function getClassPosts( $css_class )
	{
		$the_query = new \WP_Query([
			'posts_per_page' => -1,
			'post_type' => 'css-class',
			'meta_key' => 'css_class',
			'meta_value' => $css_class,
		]);

		return $the_query->posts;
	}

	private function getCatogoryIds( $class_names )
	{
		$term_ids = [];

		foreach ( $class_names as $name ) {
			$term = get_term_by( 'name', $name, 'css_class_categorie' );
			$term_ids[] = $term->term_id;
		}

		return $term_ids;
	}

	private function importCategories( $category_names )
	{
		foreach ( $category_names as $category_name ) {
			wp_insert_term( $category_name, 'css_class_categorie' );
		}
	}

	private function getClassByName( $class_name )
	{
		foreach ( $this->getAllClasses() as $class ) {
			if ( $class['class'] == $class_name ) {
				return $class;
			}
		}
	}

	public function getAllClasses()
	{
		return json_decode( $this->getJson(), true );
	}

	private function getJson()
	{
		return file_get_contents( self::URL );
	}
}
