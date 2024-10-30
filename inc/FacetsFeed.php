<?php

namespace JVH\Import;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FacetsFeed
{
	const URL = 'https://import.jvh.software/?jvh-import-feed=facets';

	public function importByIds( $facet_ids )
	{
		$facets = $this->getFacets( $facet_ids );

		$this->import( $facets );
	}

	private function import( $facets )
	{
		$facet_class = new \WP_Grid_Builder\Admin\Facets();
		$facet_class->import( $facets );
	}

	public function getFacets( $facet_ids )
	{
		$facets = [];

		foreach ( $this->getAllFacets() as $facet ) {
			if ( in_array( $facet->id, $facet_ids ) ) {
				$facets[] = $facet;
			}
		}

		return $facets;
	}

	private function getAllFacets()
	{
		return $this->getData()->facets;
	}

	private function getData()
	{
		return json_decode( $this->getJson() );
	}

	private function getJson()
	{
		return file_get_contents( self::URL );
	}
}
