<?php
/**
 * Admin DB Connection
 *
 * @author  Kazuya Takami
 * @version 1.0.0
 * @since   1.0.0
 */
class Quick_Tag_Manager_Admin_Db {

	/**
	 * Variable definition.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	private $table_name;

	/**
	 * Constructor Define.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	public function __construct () {
		global $wpdb;
		$this->table_name = $wpdb->prefix . 'quick_tag_manager';
	}

	/**
	 * Create Table.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	public function create_table () {
		global $wpdb;

		$prepared     = $wpdb->prepare( "SHOW TABLES LIKE %s", $this->table_name );
		$is_db_exists = $wpdb->get_var( $prepared );

		if ( is_null( $is_db_exists ) ) {
			$charset_collate = $wpdb->get_charset_collate();

			$query  = " CREATE TABLE " . $this->table_name;
			$query .= " (id mediumint(9) NOT NULL AUTO_INCREMENT PRIMARY KEY";
			$query .= ",html_id tinytext NOT NULL";
			$query .= ",display tinytext NOT NULL";
			$query .= ",arg1 text NOT NULL";
			$query .= ",arg2 text";
			$query .= ",access_key tinytext";
			$query .= ",title tinytext";
			$query .= ",priority int";
			$query .= ",instance text";
			$query .= ",activate tinytext";
			$query .= ",register_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL";
			$query .= ",update_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL";
			$query .= ",UNIQUE KEY id (id)) " . $charset_collate;

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $query );
		}
	}

	/**
	 * Get Data.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @param   integer $id
	 * @return  array   $args
	 */
	public function get_options ( $id ) {
		global $wpdb;

		$query    = "SELECT * FROM " . $this->table_name . " WHERE id = %d";
		$data     = array( $id );
		$prepared = $wpdb->prepare( $query, $data );

		return (array) $wpdb->get_row( $prepared );
	}

	/**
	 * Get All Data.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @param   string $activate
	 * @return  array  $results
	 */
	public function get_list_options ( $activate = '' ) {
		global $wpdb;

		$prepared  = "SELECT * FROM " . $this->table_name;
		if ( $activate === 'on' ) {
			$prepared .= " WHERE activate = 'on'";
		}
		$prepared .= " ORDER BY id ASC";

		return (array) $wpdb->get_results( $prepared );
	}

	/**
	 * Insert Data.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @param   array $post($_POST)
	 * @return  integer $id
	 */
	public function insert_options ( array $post ) {
		global $wpdb;

		$data = array(
			'html_id'       => $post['html_id'],
			'display'       => $post['display'],
			'arg1'          => $this->delete_script( $post['arg1'] ),
			'arg2'          => $this->delete_script( $post['arg2'] ),
			'access_key'    => $post['access_key'],
			'title'         => $post['title'],
			'priority'      => $post['priority'],
			'instance'      => $post['instance'],
			'activate'      => isset( $post['activate'] ) ? $post['activate'] : '',
			'register_date' => date( 'Y-m-d H:i:s' ),
			'update_date'   => date( 'Y-m-d H:i:s' )
		);
		$prepared = array(
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%d',
			'%s',
			'%s',
			'%s',
			'%s'
		);

		$wpdb->insert( $this->table_name, $data, $prepared );
		return (int) $wpdb->insert_id;
	}

	/**
	 * Update Data.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @param   array $post($_POST)
	 */
	public function update_options ( array $post ) {
		global $wpdb;

		$data = array(
			'html_id'       => $post['html_id'],
			'display'       => $post['display'],
			'arg1'          => $this->delete_script( $post['arg1'] ),
			'arg2'          => $this->delete_script( $post['arg2'] ),
			'access_key'    => $post['access_key'],
			'title'         => $post['title'],
			'priority'      => $post['priority'],
			'instance'      => $post['instance'],
			'activate'      => isset( $post['activate'] ) ? $post['activate'] : '',
			'update_date'   => date( 'Y-m-d H:i:s' )
		);
		$key = array( 'id' => esc_html( $post['quick_tag_manager_id'] ) );
		$prepared = array(
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%d',
			'%s',
			'%s',
			'%s'
		);
		$key_prepared = array( '%d' );

		$wpdb->update( $this->table_name, $data, $key, $prepared, $key_prepared );
	}

	/**
	 * Delete Data.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @param   integer $id
	 */
	public function delete_options ( $id ) {
		global $wpdb;

		$key = array( 'id' => esc_html( $id ) );
		$key_prepared = array( '%d' );

		$wpdb->delete( $this->table_name, $key, $key_prepared );
	}

	/**
	 * Delete script tag.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @param   string $param
	 * @return  string $param
	 */
	private function delete_script ( $param ) {
		$param = preg_replace('!<script.*?>.*?</script.*?>!is', '', $param );
		$param = preg_replace('!onerror=".*?"!is', '', $param );
		return (string) $param;
	}
}