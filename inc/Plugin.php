<?php

namespace JVH\Import;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Plugin
{
	public function setup()
	{
		$this->addAdminPage();
		$this->addExportFunction();
	}

	private function addAdminPage()
	{
		add_action( 'admin_menu', function() {
			add_submenu_page(
				'tools.php',
				'Import from JVH feed', 
				'JVH Import', 
				'manage_options',
				'import-jvh-feed', 
				[$this, 'renderImportPage'],
			);
		} );
	}

	private function addExportFunction()
	{
		$this->addExportButton();
		$this->addExportScript();
	}

	private function addExportButton()
	{
		add_action( 'post_submitbox_misc_actions', function ( $post ) {
			if ( $post->post_type !== 'jvh-vc-template' ) {
				return;
			}
		?>

			<div class="misc-pub-section">
				<button type="button" class="button jvh_export_button export_vc_snippets">
					<span class="dashicons dashicons-cloud" title="Export snippet"></span>
					Opslaan in Cloud
				</button>
			</div>
		<?php
		} );
	}

	private function addExportScript()
	{
		add_action( 'admin_enqueue_scripts', function() {
			wp_enqueue_script( 'export-vc-templates', plugin_dir_url( __DIR__ ) . '/assets/js/export-vc-templates.js', ['jquery'], '1.0.0', true );
			wp_enqueue_style( 'export-vc-templates', plugin_dir_url( __DIR__ ) . '/assets/css/vc-templates.css' );

			$css_classes = new \JVH\CssClasses();

			wp_localize_script( 'export-vc-templates', 'exportData', [
				'jvhImportKey' => get_option( 'jvh_import_key' ),
				'cssClasses' => $css_classes->getAllClasses(),
			] );
		} );
	}

	public function renderImportPage()
	{
		include __DIR__ . '/../template-parts/import-page.php';
	}

	private function renderSubpage()
	{
		if ( ! isset( $_GET['subpage'] ) ) {
			return;
		}

		switch ( $_GET['subpage'] ) {
			case 'fluentforms':
				$this->renderFluentFormsTable();
				break;
			case 'vctemplates':
				$this->renderVcTemplatesTable();
				break;
			case 'pages':
				$this->renderPagesTable();
				break;
		}
	}

	private function renderFluentFormsTable()
	{
		include __DIR__ . '/../template-parts/fluent-forms-table.php';
	}

	private function renderVcTemplatesTable()
	{
		include __DIR__ . '/../template-parts/vctemplates-table.php';
	}

	private function renderPagesTable()
	{
		include __DIR__ . '/../template-parts/pages-table.php';
	}

	private function isFluentFormsActivated()
	{
		return defined( 'FLUENTFORM_VERSION' );
	}

	private function isVcTemplatesActivated()
	{
		return post_type_exists( 'jvh-vc-template' );
	}
}
