<?php

namespace JVH;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CssClass
{
	private $post_id;
	
	public function __construct( $post_id )
	{
		$this->post_id = $post_id;
	}

	public function getData()
	{
		return [
			'post_id' => $this->post_id,
			'title' => $this->getTitle(),
			'class' => $this->getClassName(),
			'styles' => $this->getStyles(),
			'categories' => $this->getCategories(),
			'image_url' => $this->getImageUrl(),
		];
	}

	private function getTitle()
	{
		return get_the_title( $this->post_id );
	}

	private function getClassName()
	{
		return get_post_meta( $this->post_id, 'css_class', true );
	}

	private function getStyles()
	{
		return get_post_meta( $this->post_id, 'css_styles', true );
	}

	private function getCategories()
	{
		$categories = [];

		foreach ( $this->getCategoryTerms() as $term ) {
			$categories[] = $term->name;
		}

		return $categories;
	}

	private function getCategoryTerms()
	{
		return get_the_terms( $this->post_id, 'css_class_categorie' );
	}

	private function getImageUrl()
	{
		return get_the_post_thumbnail_url( $this->post_id, 'full' );
	}
}
