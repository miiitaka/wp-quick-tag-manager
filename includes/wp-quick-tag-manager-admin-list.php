<?php
/**
 * Quick Tag Manager Admin List
 *
 * @author  Kazuya Takami
 * @version 1.0.0
 * @since   1.0.0
 * @see     wp-quick-tag-manager-admin-db.php
 */
class Quick_Tag_Manager_Admin_List {

	/**
	 * Variable definition Text Domain.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	private $text_domain;

	/**
	 * Variable definition Key Name.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	private $key_name = 'quick_tag_manager_id';

	/**
	 * Constructor Define.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @param   String $text_domain
	 */
	public function __construct ( $text_domain ) {
		$this->text_domain = $text_domain;

		$db = new Quick_Tag_Manager_Admin_Db();
		$mode = "";

		if ( isset( $_GET['mode'] ) && $_GET['mode'] === 'delete' ) {
			if ( isset( $_GET[$this->key_name] ) && is_numeric( $_GET[$this->key_name] ) ) {
				$db->delete_options( $_GET[$this->key_name] );
				$mode = "delete";
			}
		}

		$this->page_render( $db, $mode );
	}

	/**
	 * List Page HTML Render.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @param   Quick_Tag_Manager_Admin_Db $db
	 * @param   String $mode
	 */
	private function page_render ( Quick_Tag_Manager_Admin_Db $db, $mode = "" ) {
		$post_url = admin_url() . 'admin.php?page=' . $this->text_domain . '/includes/wp-quick-tag-manager-admin-post.php';
		$self_url = $_SERVER['PHP_SELF'] . '?' . esc_html( $_SERVER['QUERY_STRING'] );

		$html  = '';
		$html .= '<div class="wrap">';
		$html .= '<h1>' . esc_html__( 'Quick Tag Manager Settings List', $this->text_domain );
		$html .= '<a href="' . $post_url . '" class="page-title-action">' . esc_html__( 'Add New', $this->text_domain ) . '</a>';
		$html .= '</h1>';
		echo $html;

		if ( $mode === "delete" ) {
			$this->information_render();
		}

		$html  = '<hr>';
		$html .= '<table class="wp-list-table widefat fixed striped posts quick-tag-manager-table-list">';
		$html .= '<tr>';
		$html .= '<th scope="row">' . esc_html__( 'Status',       $this->text_domain ) . '</th>';
		$html .= '<th scope="row">' . esc_html__( 'ID',           $this->text_domain ) . '</th>';
		$html .= '<th scope="row">' . esc_html__( 'Display',      $this->text_domain ) . '</th>';
		$html .= '<th scope="row">' . esc_html__( 'Starting Tag', $this->text_domain ) . '</th>';
		$html .= '<th scope="row">' . esc_html__( 'Ending Tag',   $this->text_domain ) . '</th>';
		$html .= '<th scope="row">' . esc_html__( 'Access Key',   $this->text_domain ) . '</th>';
		$html .= '<th scope="row">' . esc_html__( 'Title',        $this->text_domain ) . '</th>';
		$html .= '<th scope="row">' . esc_html__( 'Priority',     $this->text_domain ) . '</th>';
		$html .= '<th scope="row">&nbsp;</th>';
		$html .= '</tr>';
		echo $html;

		/** DB table get list */
		$results = $db->get_list_options();

		if ( $results ) {
			foreach ( $results as $row ) {
				$html  = '';
				if ( $row->activate === 'on' ) {
					$html .= '<tr class="active"><td><span>Enabled</span></td>';
				} else {
					$html .= '<tr class="stop"><td><span>Disabled</span></td>';
				}
				$html .= '<td>';
				$html .= '<a href="' . $post_url . '&quick_tag_manager_id=' . esc_html( $row->id ) . '">';
				$html .= esc_html( $row->html_id );
				$html .= '</a>&nbsp;&nbsp;&nbsp;&nbsp;';
				$html .= '</td>';
				$html .= '<td>' . esc_html( $row->display )    . '</td>';
				$html .= '<td>' . esc_html( $row->arg1 )       . '</td>';
				$html .= '<td>' . esc_html( $row->arg2 )       . '</td>';
				$html .= '<td>' . esc_html( $row->access_key ) . '</td>';
				$html .= '<td>' . esc_html( $row->title )      . '</td>';
				$html .= '<td>' . esc_html( $row->priority )   . '</td>';
				$html .= '<td>';
				$html .= '<a href="' . $post_url . '&quick_tag_manager_id=' . esc_html( $row->id ) . '">';
				$html .= esc_html__( 'Edit', $this->text_domain );
				$html .= '</a>&nbsp;&nbsp;&nbsp;&nbsp;';
				$html .= '<a href="' . $self_url . '&mode=delete&quick_tag_manager_id=' . esc_html( $row->id ) . '">';
				$html .= esc_html__( 'Delete', $this->text_domain );
				$html .= '</a>';
				$html .= '</td>';
				$html .= '</tr>';
				echo $html;
			}
		} else {
			echo '<td colspan="9">' . esc_html__( 'Without registration.', $this->text_domain ) . '</td>';
		}

		$html  = '</table>';
		$html .= '</div>';
		echo $html;
	}

	/**
	 * Information Message Render
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	private function information_render () {
		$html  = '<div id="message" class="updated notice notice-success is-dismissible below-h2">';
		$html .= '<p>Deletion succeeds.</p>';
		$html .= '<button type="button" class="notice-dismiss">';
		$html .= '<span class="screen-reader-text">Deletion succeeds.</span>';
		$html .= '</button>';
		$html .= '</div>';

		echo $html;
	}
}