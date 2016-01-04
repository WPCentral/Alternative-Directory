<?php

if ( ! defined('ABSPATH') ) {
	die();
}

class WP_Central_Plugins_Rest {

	/**
	 * Base route name
	 */
	protected $namespace = 'plugins';


	public function __construct() {
		add_filter( 'rest_api_init', array( $this, 'register_routes' ), 30 );
	}

	/**
	 * Register the routes for the post type
	 *
	 * @param array $routes Routes for the post type
	 * @return array Modified routes
	 */
	public function register_routes() {
		register_rest_route( $this->namespace, '/report/(?P<plugin>[a-z0-9-]+)', array(
			'callback' => array( $this, 'report_plugin_data' ),
			'methods'  => WP_REST_Server::CREATABLE
		) );
	}

	public function report_plugin_data( WP_REST_Request $request ) {
		$plugin_slug = $request->get_param( 'plugin' );

		if ( ! $plugin = get_page_by_path( $plugin_slug, OBJECT, 'plugin' ) ) {
			$plugin = WP_Central_Plugins_CPT::create( $plugin_slug );

			if ( ! $plugin ) {
				return new WP_Error( 'rest_user_invalid_id', __( "Plugin doesn't exist." ), array( 'status' => 404 ) );
			}
		}

		if ( ! ( $plugin instanceof WP_Post ) ) {
			return new WP_Error( 'rest_user_invalid_id', __( "Plugin doesn't exist." ), array( 'status' => 404 ) );
		}


		$postdata = $request->get_param( 'postdata' );
		if ( is_array( $postdata ) ) {
			foreach ( $postdata as $key => $value ) {
				if ( is_string( $value ) ) {
					$postdata[ $key ] = sanitize_text_field( $value );
				}
				else {
					unset( $postdata[ $key ] );
				}
			}

			if ( $postdata ) {
				$postdata['ID'] = $plugin->ID;
				wp_update_post( $postdata );
			}
		}


		$metadata = $request->get_param( 'metadata' );
		if ( is_array( $metadata ) ) {
			foreach ( $metadata as $key => $value ) {
				if ( is_array( $value ) ) {
					$value = array_map( 'sanitize_text_field', $value );
				}
				else {
					$value = sanitize_text_field( $value );
				}

				update_post_meta( $plugin->ID, sanitize_text_field( $key ), $value );
			}
		}

		$response = new WP_REST_Response( true );

		return $response;
	}

}