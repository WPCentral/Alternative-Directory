<?php

if ( ! defined('ABSPATH') ) {
	die();
}

class WP_Central_Plugins_CPT {

	public function __construct() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_filter( 'post_updated_messages', array( $this, 'codex_book_updated_messages' ) );

		add_filter( 'post_thumbnail_html', array( $this, 'filter_post_thumbnail' ), 10, 4 );
	}


	public static function create( $slug ) {
		$args = array(
			'post_name'   => $slug,
			'post_title'  => $slug,
			'post_type'   => 'plugin',
			'post_status' => 'publish'
		);

		$post_id = wp_insert_post( $args );
		$post    = get_post( $post_id );

		return $post;
	}


	/**
	 * Register a book post type.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/register_post_type
	 */
	public function register_post_type() {
		$labels = array(
			'name'               => _x( 'Plugins', 'post type general name', 'wpcentral' ),
			'singular_name'      => _x( 'Plugin', 'post type singular name', 'wpcentral' ),
			'menu_name'          => _x( 'Plugins', 'admin menu', 'wpcentral' ),
			'name_admin_bar'     => _x( 'Plugin', 'add new on admin bar', 'wpcentral' ),
			'add_new'            => _x( 'Add New', 'book', 'wpcentral' ),
			'add_new_item'       => __( 'Add New Plugin', 'wpcentral' ),
			'new_item'           => __( 'New Plugin', 'wpcentral' ),
			'edit_item'          => __( 'Edit Plugin', 'wpcentral' ),
			'view_item'          => __( 'View Plugin', 'wpcentral' ),
			'all_items'          => __( 'All Plugins', 'wpcentral' ),
			'search_items'       => __( 'Search Plugins', 'wpcentral' ),
			'parent_item_colon'  => __( 'Parent Plugins:', 'wpcentral' ),
			'not_found'          => __( 'No plugins found.', 'wpcentral' ),
			'not_found_in_trash' => __( 'No plugins found in Trash.', 'wpcentral' )
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'plugins' ),
			'capabilities' => array(
				'edit_post'          => 'update_core',
				'read_post'          => 'update_core',
				'delete_post'        => 'update_core',
				'edit_posts'         => 'update_core',
				'edit_others_posts'  => 'update_core',
				'publish_posts'      => 'update_core',
				'read_private_posts' => 'update_core'
			),
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title', 'editor', 'thumbnail' )
		);

		register_post_type( 'plugin', $args );
	}


	/**
	 * Book update messages.
	 *
	 * See /wp-admin/edit-form-advanced.php
	 *
	 * @param array $messages Existing post update messages.
	 *
	 * @return array Amended post update messages with new CPT update messages.
	 */
	public function codex_book_updated_messages( $messages ) {
		$post = get_post();

		$post_type_object = get_post_type_object( 'contributor' );

		$messages['plugin'] = array(
			0  => '', // Unused. Messages start at index 1.
			1  => __( 'plugin updated.', 'wpcentral' ),
			2  => __( 'Custom field updated.', 'wpcentral' ),
			3  => __( 'Custom field deleted.', 'wpcentral' ),
			4  => __( 'plugin updated.', 'wpcentral' ),
			/* translators: %s: date and time of the revision */
			5  => isset( $_GET['revision'] ) ? sprintf( __( 'Plugin restored to revision from %s', 'wpcentral' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => __( 'plugin published.', 'wpcentral' ),
			7  => __( 'plugin saved.', 'wpcentral' ),
			8  => __( 'plugin submitted.', 'wpcentral' ),
			9  => sprintf(
				__( 'plugin scheduled for: <strong>%1$s</strong>.', 'wpcentral' ),
				// translators: Publish box date format, see http://php.net/date
				date_i18n( __( 'M j, Y @ G:i', 'wpcentral' ), strtotime( $post->post_date ) )
			),
			10 => __( 'plugin draft updated.', 'wpcentral' )
		);

		if ( $post_type_object->publicly_queryable ) {
			$permalink = get_permalink( $post->ID );

			$view_link = sprintf( ' <a href="%s">%s</a>', esc_url( $permalink ), __( 'View plugin', 'wpcentral' ) );
			$messages['plugin'][1] .= $view_link;
			$messages['plugin'][6] .= $view_link;
			$messages['plugin'][9] .= $view_link;

			$preview_permalink = add_query_arg( 'preview', 'true', $permalink );
			$preview_link = sprintf( ' <a target="_blank" href="%s">%s</a>', esc_url( $preview_permalink ), __( 'Preview plugin', 'wpcentral' ) );
			$messages['plugin'][8]  .= $preview_link;
			$messages['plugin'][10] .= $preview_link;
		}

		return $messages;
	}

	public function filter_post_thumbnail( $html, $post_id, $post_thumbnail_id, $size ) {
		if ( ! $post_thumbnail_id && 'large' == $size ) {
			$post = get_post( $post_id );

			if ( 'plugin' == $post->post_type && $post->banners ) {
				$html = '<img width="772" height="250" src="' . $post->banners['banner-772x250'] . '" class="card-img-top img-fluid" />';
			}
		}

		return $html;
	}

}