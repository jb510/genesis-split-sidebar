<?php
/*
Plugin Name: Genesis Split Sidebar
Plugin URI: http://www.wordpress.org/plugins/genesis-split-sidebar
Description: Genesis Split Sidebar adds widgets areas and a layout for a split sidebar.
Author: Jon Brown
Author URI: http://www.wanderingjon.com/

Text Domain: gsplit
Domain Path: /languages/

Version: 0.9

License: GNU General Public License v2.0 (or later)
License URI: http://www.opensource.org/licenses/gpl-license.php
*/


/**
 * @package      Genesis Split Sidebars
 * @since        0.9.0
 * @link         http://wordpress.org/plugins/genesis-split-sidebar
 * @author       Jon Brown <jb@9seeds.com>
 * @copyright    Copyright (c) 2013, Jon Brown
 * @license      http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
 
/**
 * Prevent direct access to this file.
 */
if ( ! defined( 'ABSPATH' ) ) {
  exit( 'Sorry, you are not allowed to access this file directly.' );
}

register_activation_hook( __FILE__, 'gsplit_activation_check' );
/**
 * Activation hook callback.
 *
 * This functions runs when the plugin is activated. It checks to make sure the user is running
 * a minimum Genesis version, so there are no conflicts or fatal errors.
 *
 * @since 0.9.0
 */
function gsplit_activation_check() {

  if ( ! defined( 'PARENT_THEME_VERSION' ) || ! version_compare( PARENT_THEME_VERSION, '2.0.0', '>=' ) )
    gsplit_deactivate( '2.0.0', '3.6' );

}

/**
 * Deactivate Genesis Split Sidebar.
 *
 * This function deactivates Genesis Split Sidebar.
 *
 * @since 0.9.0
 */
function gsplit_deactivate( $genesis_version = '1.9.0', $wp_version = '3.3' ) {

  deactivate_plugins( plugin_basename( __FILE__ ) );
  wp_die( sprintf( __( 'Sorry, you cannot run Genesis Split Sidebar without WordPress %s and <a href="%s">Genesis %s</a>, or greater.', 'ss' ), $wp_version, 'http://my.studiopress.com/?download_id=91046d629e74d525b3f2978e404e7ffa', $genesis_version ) );

}


add_action( 'genesis_init', 'gsplit_genesis_init', 12 );
/**
 * Plugin initialization.
 *
 * Initialize the plugin, set the constants, hook callbacks to actions, and include the plugin library.
 *
 * @since 0.9.0
 */
function gsplit_genesis_init() {

  //* Deactivate if not running Genesis 1.8.0 or greater
  if ( ! class_exists( 'Genesis_Admin_Boxes' ) )
    add_action( 'admin_init', 'ss_deactivate', 10, 0 );

  //* Load translations
  load_plugin_textdomain( 'gsplit', false, 'genesis-split-sidebar/languages' );

  //* required hooks
  add_action( 'init', 'gsplit_create_split_sidebar_layout' );
  add_action( 'widgets_init', 'gsplit_register_split_sidebars' );
   add_action('get_header', 'gsplit_do_split_sidebar', 11);
}


/**
 * Create Split Sidebar Layout
 */
function gsplit_create_split_sidebar_layout() {
  genesis_register_layout( 'content-split-sidebar', array(
    'label' => __('Content/Split-Sidebars', 'core-functionality'),
    'img'   => plugins_url( 'images/layout-content-split-sidebar.gif', ( __FILE__ ) )
    )
  );
}

//* Register after post widget area
function gsplit_register_split_sidebars() {
  genesis_register_sidebar( array(
    'id'            => 'gsplit-sidebar-top',
    'name'          => __( 'Split Sidebar Top', 'gsplit' ),
    'description'   => __( 'This is the top widget area above the split sidebar', 'gsplit' ),
  ) );
  genesis_register_sidebar( array(
    'id'            => 'gsplit-sidebar-left',
    'name'          => __( 'Split Sidebar Left', 'gsplit' ),
    'description'   => __( 'This is the left widget area for the split sidebar', 'gsplit' ),
  ) );
  genesis_register_sidebar( array(
    'id'            => 'gsplit-sidebar-right',
    'name'          => __( 'Split Sidebar right', 'gsplit' ),
    'description'   => __( 'This is the right widget area for the split sidebar', 'gsplit' ),
  ) );
}


/**
 * Add two sidebars underneath the primary sidebar.
 */
function gsplit_display_split_sidebar() {
  ?>
  <div class="split-sidebar">
    <?php
    genesis_widget_area( 'gsplit-sidebar-top', array( 'before' => '<div class="sidebar split-sidebar-top widget-area">', 'after' => '</div>' ));
    genesis_widget_area( 'gsplit-sidebar-left', array( 'before' => '<div class="sidebar split-sidebar-left widget-area">', 'after' => '</div>' ));
    genesis_widget_area( 'gsplit-sidebar-right', array( 'before' => '<div class="sidebar split-sidebar-right widget-area">', 'after' => '</div>' ));
    ?>
  </div>
  <?php
}

/**
 * Split Sidebar Layout
 */
function gsplit_do_split_sidebar() {
  $site_layout = genesis_site_layout();
  if ( 'content-split-sidebar' == $site_layout ) {
  
    // Add mbody classes
    add_filter( 'body_class', 'gsplit_body_class' );
  
    // Load CSS
    add_action ( 'wp_enqueue_scripts' , 'gsplit_css');

    // Remove the Primary Sidebar from the Primary Sidebar area.
    remove_action( 'genesis_sidebar', 'genesis_do_sidebar' );
    remove_action( 'genesis_sidebar', 'ss_do_sidebar' ); // Genesis Simple Sidebars
  
    // Remove the Secondary Sidebar from the Secondary Sidebar area.
    remove_action( 'genesis_sidebar_alt', 'genesis_do_sidebar_alt' );
    remove_action( 'genesis_sidebar_alt', 'ss_do_sidebar_alt' ); // Genesis Simple Sidebars

    // Do split sidebar
    add_action( 'genesis_sidebar', 'gsplit_display_split_sidebar' );
  }
}

//* Add custom body class to the head
function gsplit_body_class( $classes ) {
  $classes[] = 'content-sidebar content-split-sidebar';
    return $classes;
}

//* Add custom body class to the head
function gsplit_css() {
  wp_register_style( 'gsplit', plugins_url( 'css/gsplit.css', ( __FILE__ ) ) );
    wp_enqueue_style ('gsplit');
}