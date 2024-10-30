<?php

namespace JVH\Import;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FluentFormsFeed
{
	const URL = 'https://import.jvh.software/?jvh-import-feed=fluentforms';

	public function getFormsWithScreens()
	{
		$forms = $this->getForms();

		foreach ( $forms as $key => $form ) {
			$screen_feed = new \JVH\Import\FluentScreensFeed();
			$image_url = $screen_feed->getScreenByFormId( $form['id'] );

			$forms[$key]['image_url'] = $image_url;
		}

		return $forms;
	}

	public function getForms()
	{
		return json_decode( $this->getJson(), true );
	}

	private function getJson()
	{
		return file_get_contents( self::URL );
	}

	/*
	 * Function is based on Fluent Form Transfer->import() function
	 */
	public function importForm( $form_id )
	{
		$form_id = intval( $form_id );
		$formItem = $this->getForm( $form_id );

		if ( ! is_array( $formItem ) ) {
			$_GET['fluent-import-failed'] = $form_id;
			return;
		}

		// First of all make the form object.
		$formFields = json_encode([]);
		if($fields = \FluentForm\Framework\Helpers\ArrayHelper::get($formItem, 'form', '')) {
			$formFields = json_encode($fields);
		} else if($fields = \FluentForm\Framework\Helpers\ArrayHelper::get($formItem, 'form_fields', '')) {
			$formFields = json_encode($fields);
		} else {
			wp_redirect( get_admin_url() . '/tools.php?page=import-jvh-feed&subpage=fluentforms&fluent-import-failed=' . $form_id );
		}

		$form = [
			'title'       => \FluentForm\Framework\Helpers\ArrayHelper::get($formItem, 'title'),
			'form_fields' => $formFields,
			'status' => \FluentForm\Framework\Helpers\ArrayHelper::get($formItem, 'status', 'published'),
			'has_payment' => \FluentForm\Framework\Helpers\ArrayHelper::get($formItem, 'has_payment', 0),
			'type' => \FluentForm\Framework\Helpers\ArrayHelper::get($formItem, 'type', 'form'),
			'created_by'  => get_current_user_id()
		];

		if ( empty( $form['title'] ) ) {
			wp_redirect( get_admin_url() . '/tools.php?page=import-jvh-feed&subpage=fluentforms&fluent-import-failed=' . $form_id );
		}

		if( \FluentForm\Framework\Helpers\ArrayHelper::get($formItem, 'conditions')) {
			$form['conditions'] = \FluentForm\Framework\Helpers\ArrayHelper::get($formItem, 'conditions');
		}

		if(isset($formItem['appearance_settings'])) {
			$form['appearance_settings'] = $formItem['appearance_settings'];
		}

		// Insert the form to the DB.
		$formId = wpFluent()->table('fluentform_forms')->insert($form);

		$insertedForms[$formId] = [
			'title' => $form['title'],
			'edit_url' => admin_url('admin.php?page=fluent_forms&route=editor&form_id='.$formId)
		];

		if(isset($formItem['metas'])) {

			foreach ($formItem['metas'] as $metaData) {
				$settings = [
					'form_id'  => $formId,
					'meta_key' => $metaData['meta_key'],
					'value'    => $metaData['value']
				];
				wpFluent()->table('fluentform_form_meta')->insert($settings);
			}

		} else {
			$oldKeys = [
				'formSettings',
				'notifications',
				'mailchimp_feeds',
				'slack'
			];
			foreach ($oldKeys as $key) {
				if(isset($formItem[$key])) {
					$settings = [
						'form_id'  => $formId,
						'meta_key' => $key,
						'value'    => json_encode($formItem[$key])
					];
					wpFluent()->table('fluentform_form_meta')->insert($settings);
				}
			}
		}

		wp_redirect( get_admin_url() . '/tools.php?page=import-jvh-feed&subpage=fluentforms&fluent-import-success=' . $form_id );
	}

	private function getForm( $form_id )
	{
		$form_id = intval( $form_id );

		foreach ( $this->getForms() as $form ) {
			if ( $form['id'] == $form_id ) {
				return $form;
			}
		}
	}
}

if ( isset( $_GET['import-fluent-form'] ) ) {
	add_action( 'admin_init', function() {
		$form_id = intval( $_GET['import-fluent-form'] );

		$feed = new \JVH\Import\FluentFormsFeed();
		$feed->importForm( $form_id );
	} );
}
