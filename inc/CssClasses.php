<?php

namespace JVH;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CssClasses
{
	public function getAllClasses()
	{
		$classes = [];

		foreach ( $this->getAllClassPosts() as $post ) {
			$class = new \JVH\CssClass( $post->ID );
			$data = $class->getData();

			$classes[$data['class']] = $data;
		}

		return $classes;
	}

	private function getAllClassPosts()
	{
		$the_query = new \WP_Query([
			'posts_per_page' => -1,
			'post_type' => 'css-class',
		]);

		return $the_query->posts;
	}
}
