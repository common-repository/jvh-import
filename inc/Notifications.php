<?php

namespace JVH\Import;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Notifications
{
	public function hasNotification()
	{
		return $this->hasFailedNotice() || $this->hasSuccessnotice();
	}

	public function display()
	{
		if ( $this->hasFailedNotice() ) {
			$this->showFailedNotice();
		}
		else if ( $this->hasSuccessNotice() ) {
			$this->showSuccessNotice();
		}
	}

	private function showFailedNotice()
	{
		$text = __( 'Import failed.', 'jvhimport' );

		$this->showNotice( $text, 'warning' );
	}

	private function showSuccessNotice()
	{
		$text = __( 'Imported successfuly.', 'jvhimport' );

		$this->showNotice( $text, 'success' );
	}

	private function showNotice( $text, $type )
	{
		echo '<div class="notice notice-' . esc_attr( $type ) . ' is-dismissible"><p>' . esc_html( $text ) . '</p></div>';
	}
	
	private function hasFailedNotice()
	{
		return isset ( $_GET['fluent-import-failed'] ) || ( isset( $_GET['result'] ) && $_GET['result'] === 'failed' );
	}

	private function hasSuccessNotice()
	{
		return isset ( $_GET['fluent-import-failed'] ) || ( isset( $_GET['result'] ) && $_GET['result'] === 'success' );
	}
}
