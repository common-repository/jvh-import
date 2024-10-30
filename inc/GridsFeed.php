<?php

namespace JVH\Import;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class GridsFeed
{
	const URL = 'https://import.jvh.software/?jvh-import-feed=grids';

	public function importByIds( $grid_ids )
	{
		$grids = $this->getGrids( $grid_ids );

		$this->import( $grids );
	}

	private function import( $grids )
	{
		$grid_class = new \WP_Grid_Builder\Admin\Grids();
		$grid_class->import( $grids );
	}

	public function getGrids( $grid_ids )
	{
		$grids = [];

		foreach ( $this->getAllGrids() as $grid ) {
			if ( in_array( $grid->id, $grid_ids ) ) {
				$grids[] = $grid;
			}
		}

		return $grids;
	}

	private function getAllGrids()
	{
		return $this->getData()->grids;
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
