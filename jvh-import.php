<?php
/**
 * Plugin Name:       JVH Import
 * Description:       Import elements from JVH feed, such as Fluent Forms forms and other elements later on. Used internally within JVH webbouw.
 * Version:           1.2.3
 * Author:            JVH webbouw
 * Author URI:        https://jvhwebbouw.nl
 * License:           GPL-v3
 * Requires PHP:      7.3
 * Requires at least: 5.0
 */

foreach ( glob( __DIR__ . '/inc/*.php' ) as $file ) {
    require_once $file;
}

$plugin = new \JVH\Import\Plugin();
$plugin->setup();
