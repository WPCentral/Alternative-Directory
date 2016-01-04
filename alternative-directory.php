<?php
/*
	Plugin Name: Alternative Directory
	Plugin URI:  http://wpcentral.io
	Description: 
	Version:     1.0
	Author:      markoheijnen
	Author URI:  http://markoheijnen.com
	License:     GPL

	Text Domain: alternative-directory
*/


if ( ! defined('ABSPATH') ) {
	die();
}

include 'inc/post-type.php';
include 'inc/rest-api.php';

class WP_Central_Plugins {

	public function __construct() {
		new WP_Central_Plugins_CPT;
		new WP_Central_Plugins_Rest;
	}

}

$wp_central_plugins = new WP_Central_Plugins;