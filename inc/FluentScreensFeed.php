<?php

namespace JVH\Import;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FluentScreensFeed
{
	const URL = 'https://import.jvh.software/?jvh-import-feed=fluentscreens';

	public function getScreenByFormId( $form_id )
	{
		foreach ( $this->getAllScreens() as $screen ) {
			if ( $screen['form_id'] == $form_id ) {
				return $screen['image_url'];
			}
		}
	}

	private function getAllScreens()
	{
		return json_decode( $this->getJson(), true );
	}

	private function getJson()
	{
		return file_get_contents( self::URL );
	}
}
