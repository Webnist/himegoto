<?php
/*
  Plugin Name: Himegoto
  Plugin URI: http://plugins.webnist.net/
  Description:
  Version: 0.7.1.2
  Author: Webnist
  Author URI: http://webni.st
  License: GPLv2 or later
*/
if ( !defined( 'HIMEGOTO_DOMAIN' ) )
	define( 'HIMEGOTO_DOMAIN', 'kouchiku-danshi' );

if ( !defined( 'HIMEGOTO_PLUGIN_URL' ) )
	define( 'HIMEGOTO_PLUGIN_URL', plugins_url() . '/' . dirname( plugin_basename( __FILE__ ) ) );

if ( !defined( 'HIMEGOTO_PLUGIN_DIR' ) )
	define( 'HIMEGOTO_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . dirname( plugin_basename( __FILE__ ) ) );

new Himegoto();

class Himegoto {

	private $version = '0.1.7';
	private $base_dir;
	private $plugin_dir;
	private $plugin_url;
	private $menu_slug = 'himegoto';

	public function __construct() {
		$this->base_dir = dirname( plugin_basename( __FILE__ ) );
		$this->plugin_dir = WP_PLUGIN_DIR . '/' . $this->base_dir;
		$this->plugin_url = WP_PLUGIN_URL . '/' . $this->base_dir;
		$this->menu_slug = 'himegoti';

		load_plugin_textdomain( HIMEGOTO_DOMAIN, false, $this->base_dir . '/languages/' );
		if ( is_admin() ) {
			add_action( 'init', array( &$this, 'create_initial_post_types' ) );
		}
		add_action( 'wp_footer', array( &$this, 'himegoto_content' ) );
		add_action( 'wp_enqueue_scripts', array( &$this, 'himegoto_scripts' ) );
	}

	public function create_initial_post_types() {

		$labels = array(
			'name' => __( 'Himegoto', HIMEGOTO_DOMAIN ),
			'singular_name' => __( 'Himegoto', HIMEGOTO_DOMAIN ),
			'add_new_item' => __( 'Add New Himegoto', HIMEGOTO_DOMAIN ),
			'edit_item' => __( 'Edit Himegoto', HIMEGOTO_DOMAIN ),
			'new_item' => __( 'New Himegoto', HIMEGOTO_DOMAIN ),
			'view_item' => __( 'View Himegoto', HIMEGOTO_DOMAIN ),
			'search_items' => __( 'Search Himegoto', HIMEGOTO_DOMAIN ),
			'not_found' => __( 'No Himegoto found.', HIMEGOTO_DOMAIN ),
			'not_found_in_trash' => __( 'No Himegoto found in Trash.', HIMEGOTO_DOMAIN ),
		);
		$args = array(
			'labels' => $labels,
			'publicly_queryable' => false,
			'show_ui' => true,
			'show_in_nav_menus' => false,
			'capability_type' => 'post',
			'hierarchical' => true,
			'supports' => array( 'title', 'editor' ),
			'rewrite' => false,
			'query_var' => false,
			'has_archive' => false,
		);
		register_post_type( 'himegoto', $args );

	}

	public function search_himegoto_id() {
		global $wpdb;
		$get_query = get_search_query();
		$n = ! empty( $q['exact'] ) ? '' : '%';
		$sql = "SELECT ID FROM {$wpdb->posts} WHERE post_title = '{$get_query}' AND post_status = 'publish' AND post_type = 'himegoto'";
		$sql = (int) $wpdb->get_var( $wpdb->prepare( $sql ) );
		return $sql;
	}

	public function himegoto_scripts() {
		$title = get_the_title( $this->search_himegoto_id() );
		if ( !is_admin() && is_search() && get_search_query() == $title ) {
				wp_enqueue_script( 'himegoto-script', HIMEGOTO_PLUGIN_URL . '/js/common.min.js', array( 'jquery' ), '0.7.1.0', true );
				wp_enqueue_style( 'himegoto-style', HIMEGOTO_PLUGIN_URL . '/style.css' , array(), '0.7.1.0' );
			}

	}
	public function himegoto_content() {
		$id = $this->search_himegoto_id();
		$title = get_the_title( $id );
		if ( !is_admin() && is_search() && get_search_query() == $title ) {
			$my_post = get_post( $id );
			$content = $my_post->post_content;
			$content = apply_filters('the_content', $content);
			$content = str_replace(']]>', ']]&gt;', $content);
			$output = '';
			$output .= '<div id="himegoto-content">' . "\n";
			$output .= $content;
			$output .= '</div>' . "\n";
			echo $output;
		}
	}
}