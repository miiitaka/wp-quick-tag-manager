<?php
/*
Plugin Name: Quick Tag Manager
Plugin URI: https://github.com/miiitaka/wp-quick-tag-manager
Description: This plugin allows you to add quick tags to posts and pages text editors.
Version: 1.0.0
Author: Kazuya Takami
Author URI: https://www.terakoya.work/
License: GPLv2 or later
Text Domain: wp-quick-tag-manager
Domain Path: /languages
*/
require_once( plugin_dir_path( __FILE__ ) . 'includes/wp-quick-tag-manager-admin-db.php' );

new Quick_Tag_Manager();

/**
 * Basic Class
 *
 * @author  Kazuya Takami
 * @version 1.0.0
 * @since   1.0.0
 */
class Quick_Tag_Manager {

	/**
	 * Variable definition.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	private $text_domain = 'wp-quick-tag-manager';

	/**
	 * Variable definition.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	private $version = '1.0.0';

	/**
	 * Constructor Define.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	public function __construct () {
		register_activation_hook( __FILE__, array( $this, 'create_table' ) );
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );

		if ( is_admin() ) {
			add_action( 'admin_init', array( $this, 'admin_init' ) );
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_action_links' ) );
			add_action( 'admin_print_footer_scripts', array( $this, 'add_quick_tags' ) );
		}
	}

	/**
	 * Create table.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	public function create_table () {
		$db = new Quick_Tag_Manager_Admin_Db();
		$db->create_table();
	}

	/**
	 * i18n.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	public function plugins_loaded () {
		//load_plugin_textdomain( $this->text_domain, false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

	/**
	 * admin init.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	public function admin_init () {
		wp_register_style( 'wp-quick-tag-manager-admin-style', plugins_url( 'css/style.css', __FILE__ ), array(), $this->version );
	}

	/**
	 * Add Menu to the Admin Screen.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	public function admin_menu () {
		add_menu_page(
			esc_html__( 'Quick Tag Manager', $this->text_domain ),
			esc_html__( 'Quick Tag Manager', $this->text_domain ),
			'manage_options',
			plugin_basename( __FILE__ ),
			array( $this, 'list_page_render' )
		);
		$list_page = add_submenu_page(
			__FILE__,
			esc_html__( 'All Settings', $this->text_domain ),
			esc_html__( 'All Settings', $this->text_domain ),
			'manage_options',
			plugin_basename( __FILE__ ),
			array( $this, 'list_page_render' )
		);
		$post_page = add_submenu_page(
			__FILE__,
			esc_html__( 'Quick Tag Manager', $this->text_domain ),
			esc_html__( 'Add New',           $this->text_domain ),
			'manage_options',
			plugin_dir_path( __FILE__ ) . 'includes/wp-quick-tag-manager-admin-post.php',
			array( $this, 'post_page_render' )
		);

		add_action( 'admin_print_styles-'  . $post_page, array( $this, 'add_style' ) );
		add_action( 'admin_print_styles-'  . $list_page, array( $this, 'add_style' ) );
	}

	/**
	 * Add Menu to the Admin Screen.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @param   array $links
	 * @return  array $links
	 */
	public function plugin_action_links( $links ) {
		$url = admin_url( 'admin.php?page=' . $this->text_domain . '/' . $this->text_domain . '.php' );
		$url = '<a href="' . esc_url( $url ) . '">' . __( 'Settings' ) . '</a>';
		array_unshift( $links, $url );
		return $links;
	}

	/**
	 * CSS admin add.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	public function add_style () {
		wp_enqueue_style( 'wp-quick-tag-manager-admin-style' );
	}

	/**
	 * Admin List Page Template Require.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	public function list_page_render () {
		require_once( plugin_dir_path( __FILE__ ) . 'includes/wp-quick-tag-manager-admin-list.php' );
		new Quick_Tag_Manager_Admin_List( $this->text_domain );
	}

	/**
	 * Admin Post Page Template Require.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	public function post_page_render () {
		require_once( plugin_dir_path( __FILE__ ) . 'includes/wp-quick-tag-manager-admin-post.php' );
		new Quick_Tag_Manager_Admin_Post( $this->text_domain );
	}

	/**
	 * Add Quick Tag.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	public function add_quick_tags () {
		if ( wp_script_is( 'quicktags' ) ) {
			$db      = new Quick_Tag_Manager_Admin_Db();
			$results = $db->get_list_options( 'on' );
			$count   = count( $results );

			if ( $count > 0 ) {
				echo '<script>';

				foreach ( $results as $row ) {
					$html  = 'QTags.addButton(';
					$html .= '"';
					$html .= isset( $row->html_id ) ? $row->html_id : '';
					$html .= '","';
					$html .= isset( $row->display ) ? $row->display : '';
					$html .= '",';
					echo $html;

					$this->replace_line_break( $row->arg1 );
					$this->replace_line_break( $row->arg2 );

					$html  = '"';
					$html .= isset( $row->access_key ) ? $row->access_key : '';
					$html .= '","';
					$html .= isset( $row->title ) ? $row->title : '';
					$html .= '","';
					$html .= isset( $row->priority ) ? $row->priority : '';
					$html .= '","';
					$html .= isset( $row->instance ) ? $row->instance : '';
					$html .= '");';
					echo $html;
				}

				echo '</script>';
			}
		}
	}

	/**
	 * Line break code replace.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @param   string $param
	 */
	private function replace_line_break ( $param ) {
		if ( isset( $param ) ) {
			$param = preg_replace( '/\r\n|\r|\n/', '\n', $param );
			$args  = explode( '\n', $param );
			$count = count( $args );

			for ( $i = 0; $i < $count; $i ++ ) {
				echo '"' . $args[ $i ] . '"';

				if ( $i < $count - 1 ) {
					echo ' + "\n" + ';
				}
			}
		} else {
			echo '""';
		}
		echo ',';
	}
}