<?php
/**
 * Quick Tag Manager Admin Setting
 *
 * @author  Kazuya Takami
 * @version 1.0.0
 * @since   1.0.0
 * @see     wp-quick-tag-manager-admin-db.php
 */
class Quick_Tag_Manager_Admin_Post {

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
	 * Defined nonce.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	private $nonce_name;
	private $nonce_action;

	/**
	 * Constructor Define.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @param   String $text_domain
	 */
	public function __construct ( $text_domain ) {
		$this->text_domain = $text_domain;
		$this->nonce_name   = "_wpnonce_" . $text_domain;
		$this->nonce_action = "edit-"     . $text_domain;

		/**
		 * Update Status
		 *
		 * "ok" : Successful update
		 */
		$status = "";

		/** DB Connect */
		$db = new Quick_Tag_Manager_Admin_Db();

		/** Set Default Parameter for Array */
		$options = array(
			'id'         => '',
			'html_id'    => '',
			'display'    => '',
			'arg1'       => '',
			'arg2'       => '',
			'access_key' => '',
			'title'      => '',
			'priority'   => '',
			'instance'   => '',
			'activate'   => 'on',
			'note'       => ''
		);

		/** Key Set */
		if ( isset( $_GET[$this->key_name] ) && is_numeric( $_GET[$this->key_name] ) ) {
			$options['id'] = esc_html( $_GET[$this->key_name] );
		}

		/** DataBase Update & Insert Mode */
		if ( ! empty( $_POST ) && check_admin_referer( $this->nonce_action, $this->nonce_name ) ) {
			if ( isset( $_POST[ $this->key_name ] ) && is_numeric( $_POST[ $this->key_name ] ) ) {
				$db->update_options( $_POST );
				$options['id'] = $_POST[ $this->key_name ];
				$status        = "ok";
			} else {
				if ( isset( $_POST[ $this->key_name ] ) && $_POST[ $this->key_name ] === '' ) {
					$options['id'] = $db->insert_options( $_POST );
					$status        = "ok";
				}
			}
		}

		/** Mode Judgment */
		if ( isset( $options['id'] ) && is_numeric( $options['id'] ) ) {
			$options = $db->get_options( $options['id'] );
		}

		$this->page_render( $options, $status );
	}

	/**
	 * Setting Page of the Admin Screen.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @param   array  $options
	 * @param   string $status
	 */
	private function page_render ( array $options, $status ) {
		$html  = '';
		$html .= '<div class="wrap">';
		$html .= '<h1>' . esc_html__( 'Quick Tag Manager Settings', $this->text_domain ) . '</h1>';
		echo $html;

		switch ( $status ) {
			case "ok":
				$this->information_render();
				break;
			default:
				break;
		}

		$html  = '<hr>';
		$html .= '<form method="post" action="">';
		$html .= '<input type="hidden" name="' . $this->key_name . '" value="' . esc_attr( $options['id'] ) . '">';
		echo $html;

		wp_nonce_field( $this->nonce_action, $this->nonce_name );

		$html  = '<table class="quick-tag-manager-table">';

		$html .= '<tr><th>Enabled : </th><td>';
		$html .= '<input type="checkbox" name="activate" value="on"';
		$html .= ( isset( $options['activate'] ) && $options['activate'] === "on" ) ? ' checked' : '';
		$html .= '></td></tr>';

		$html .= '<tr><th class="require"><label for="html_id">' . esc_html__( 'ID attribute name', $this->text_domain ) . ':</label></th><td>';
		$html .= '<input type="text" name="html_id" id="html_id" class="regular-text" required autofocus maxlength="100" value="';
		$html .= esc_attr( $options['html_id'] ) . '">';
		$html .= '</td></tr>';

		$html .= '<tr><th class="require"><label for="display">' . esc_html__( 'Button value', $this->text_domain ) . ':</label></th><td>';
		$html .= '<input type="text" name="display" id="display" class="regular-text" required maxlength="100" value="';
		$html .= esc_attr( $options['display'] ) . '">';
		$html .= '</td></tr>';

		$html .= '<tr><th class="require"><label for="arg1">' . esc_html__( 'Starting Tag', $this->text_domain ) . ':</label></th><td>';
		$html .= '<textarea name="arg1" id="arg1" rows="10" cols="50" class="large-text code" maxlength="1000" required>' . stripslashes( $options['arg1'] ) . '</textarea>';
		$html .= '</td></tr>';

		$html .= '<tr><th><label for="arg2">' . esc_html__( 'Ending Tag', $this->text_domain ) . ':</label></th><td>';
		$html .= '<textarea name="arg2" id="arg2" rows="10" cols="50" class="large-text code" maxlength="1000">' . stripslashes( $options['arg2'] ) . '</textarea>';
		$html .= '</td></tr>';

		$html .= '<tr><th><label for="access_key">' . esc_html__( 'Access key', $this->text_domain ) . ':</label></th><td>';
		$html .= '<input type="text" name="access_key" id="access_key" class="regular-text" maxlength="50" value="';
		$html .= esc_attr( $options['access_key'] ) . '">';
		$html .= '</td></tr>';

		$html .= '<tr><th><label for="title">' . esc_html__( 'HTML button title', $this->text_domain ) . ':</label></th><td>';
		$html .= '<input type="text" name="title" id="title" class="regular-text" maxlength="100" value="';
		$html .= esc_attr( $options['title'] ) . '">';
		$html .= '</td></tr>';

		$html .= '<tr><th><label for="priority">' . esc_html__( 'Priority', $this->text_domain ) . ':</label></th><td>';
		$html .= '<input type="number" name="priority" id="priority" class="regular-text" maxlength="100" value="';
		$html .= esc_attr( $options['priority'] ) . '">';
		$html .= '</td></tr>';

		$html .= '<tr><th><label for="instance">' . esc_html__( 'Instance', $this->text_domain ) . ':</label></th><td>';
		$html .= '<input type="text" name="instance" id="instance" class="regular-text" maxlength="100" value="';
		$html .= esc_attr( $options['instance'] ) . '">';
		$html .= '</td></tr>';

		$html .= '<tr><th><label for="note">' . esc_html__( 'Note', $this->text_domain ) . ':</label></th><td>';
		$html .= '<input type="text" name="note" id="note" class="regular-text" maxlength="100" value="';
		$html .= esc_attr( $options['note'] ) . '">';
		$html .= '</td></tr>';

		$html .= '</table>';
		echo $html;

		submit_button();

		$html  = '</form></div>';
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
		$html .= '<p>Quick Tag Manager Information Update.</p>';
		$html .= '<button type="button" class="notice-dismiss">';
		$html .= '<span class="screen-reader-text">Quick Tag Manager Information Update.</span>';
		$html .= '</button>';
		$html .= '</div>';

		echo $html;
	}
}