<?php
/**
 * Plugin Name: Mail link Task wp-shop
 * Description: Add your tasks automatically on wp-shop
 * Version: 1.0.0
 * Author: Bactery
 * License: GPL2
 */

/**
 * La creation de tache par e-mail.
 *
 * @method __construct
 */
class Mail_Link_For_Wpshop {

	/**
	 * Initialise les notices admin pour activer l'adresse qui est ajouter le champ dans profile utilisateur, puis est relié a task manager pour pouvoir créer et supprimer des evenement sur l'agenda google.
	 *
	 * @method __construct
	 */
	public function __construct() {
		require( 'functions.php' );

		add_action( 'wp_enqueue_scripts', array( $this, 'wpb_adding_scripts' ) );
		add_filter( 'admin_init' , array( &$this, 'register_fields' ) );
		add_filter( 'admin_init' , array( &$this, 'update_setting_task_field' ) );
		add_action( 'admin_init' , array( &$this, 'callback_ask_task' ) );
		add_action( 'admin_menu', array( &$this, 'add_admin_menu' ) );
	}

	/**
	 * Ajoute le script qui contient toute les fonctions que j'ai codé en javascript et lui donne le nom 'form-mail'.
	 *
	 * @return void
	 */
	public function wpb_adding_scripts() {
		wp_register_script(
			'form-mail', // name your script so that you can attach other scripts and de-register, etc.
			__DIR__ . '/ajax_task_call.js', // this is the location of your script file.
			array( 'jquery' ) // this array lists the scripts upon which your script depends.
		);
		$translation_array = array(
			'text_confirm_popup' => __( 'êtes-vous sure de vouloir créer la tache ? Toute pièce(s) jointe(s) non télécharger sera perdu.', 'mail-for-task-manager' ),
		);
		wp_localize_script( 'form-mail', 'MailForTaskManager', $translation_array );
		wp_enqueue_script( 'form-mail' );
	}

	/**
	 * Enregistre et ajoute tout les champs definie plus bas dans parametres->Ecriture.
	 *
	 * @return void.
	 */
	public function register_fields() {
		register_setting( 'general', 'using_task_mail', 'esc_attr' );
		register_setting( 'general', 'task_mail', 'esc_attr' );
		register_setting( 'general', 'task_pass', 'esc_attr' );
		add_settings_field( 'using_task_mail', '<h2 class="title">' . __( 'Generating tasks via email' , 'using_task_mail' ) . '</h2>' , array( &$this, 'add_setting_using_task_mail' ) , 'writing' );
		add_settings_field( 'task_mail', '<label for="task_mail">' . __( 'Task mail' , 'task_mail' ) . '</label>' , array( &$this, 'add_setting_task_mail' ) , 'writing' );
		add_settings_field( 'task_pass', '<label for="task_pass">' . __( 'Task password' , 'task_pass' ) . '</label>' , array( &$this, 'add_setting_task_pass' ) , 'writing' );
	}

	/**
	 * Enqueue tout les scripts pour pouvoir utiliser jquery dans les fonctions javascript.
	 *
	 * @return void
	 */
	public function callback_before_admin_enqueue_scripts_js() {
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-form' );
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_media();
	}

	/**
	 * Ajoute le champ d'avertissement dans le menu 'Ecriture' dans les parametres de Wordpress.
	 */
	public function add_setting_using_task_mail() {
		?>
		<p> Before using this service make sure that you have imap enabled on your e-mail settings.<br>As of right now this service only work for gmail accounts. </p>
		<?php
	}

	/**
	 * Ajoute le champ e-mail dans le menu 'Ecriture' dans les parametres de Wordpress.
	 */
	public function add_setting_task_mail() {
		$task_mail = get_option( 'task_mail', true );
		?>
		<input type="text" name="task_mail" id="task_mail" value="<?php if ( isset( $task_mail ) ) { echo esc_attr( $task_mail ); } ?>" class="regular-text" />
		<?php
	}

	/**
	 * Ajoute le champ mot de passe dans le menu 'Ecriture' dans les parametres de Wordpress.
	 */
	public function add_setting_task_pass() {
		$task_pass = get_option( 'task_pass', true );
		?>
		<input type="password" name="task_pass" id="task_pass" value="<?php if ( isset( $task_pass ) ) { echo esc_attr( $task_pass ); } ?>" class="regular-text" />
		<?php
	}

	/**
	 * Met a jour l'email et le mot de passe à utiliser pour Mail_for_task_manager pour le site.
	 *
	 * @return void
	 */
	public function update_setting_task_field() {
		wp_verify_nonce( $_POST['task_mail'] );
		wp_verify_nonce( $_POST['task_pass'] );
		if ( isset( $_POST['task_mail'] ) && ! empty( $_POST['task_mail'] ) ) {
			update_option( 'task_mail', sanitize_text_field( $_POST['task_mail'] ) );
		}
		if ( isset( $_POST['task_pass'] ) && ! empty( $_POST['task_pass'] ) ) {
			update_option( 'task_pass', sanitize_text_field( $_POST['task_pass'] ) );
		}
	}
	/**
	 * Ajoute un champ dans le menu 'Ecriture' dans les parametres de Wordpress.
	 */
	public function add_admin_menu() {
		add_menu_page( 'Test', 'Mail for task manager', 'manage_options', 'mailfortask', array( &$this, 'mail_to_task_html' ) );
	}
	/**
	 * Boite html qui va montrer le mail sous forme de box.
	 *
	 * @return void nothing.
	 */
	public function mail_to_task_html() {
		wp_register_script( 'my_plugin_script_test', plugins_url( '/ajax_task_call.js', __FILE__ ), array( 'jquery' ) );

		wp_enqueue_script( 'my_plugin_script_test' );
		require( 'imapcall.php' );
		if ( $imap ) {
			$num_mails = imap_num_msg( $imap );
			for ( $i = 1 ; $i <= $num_mails ; $i++ ) {
				$header = imap_header( $imap, $i );
				if ( 'U' === $header->Unseen ) { // WPCS: XSS ok.
					$from_info = $header->from[0];
					$reply_info = $header->reply_to[0];
					$client_name = $from_info->personal;
					$client_mail = $from_info->mailbox . '@' . $from_info->host;
					$mail_title = $header->subject;
					$mail_test = _get_body_attach( $imap, $i );
					if ( ! empty( $mail_test['attachment'] ) ) {
						$attachs = 1;
					} else {
						$attachs = 0;
					}
					$body_text = $mail_test['body'];
					$body_text = strip_tags( $body_text, '<img>' );
					$search = '/(<img[^>]+>)/';
					$replace = '';
					$body_text = preg_replace( $search, $replace, $body_text, -1, $count );
					$mail_uid = imap_uid( $imap, $i );
					$mail_title_sanitize = sanitize_text_field( $mail_title );
					$mail_title_sanitize = str_replace( ' ', '_', $mail_title_sanitize );
					if ( strlen( $body_text ) < 5000 ) {
						require( 'view/mail-box.php' );
					} else {
						require( 'view/mail_box_failure.php' );
					}
				}
			}
		}
	}
		/**
		 *  Create task or leave it be and mark email as read.
		 *
		 * @return void tableau task_mail qui contient si la tache et créer ainsi que l'id de la div pour pouvoir la supprimer.
		 */
	public function callback_ask_task() {
		include( 'imapcall.php' );
		$array_task = array();
		$task_man_test = \task_manager\Task_Comment_Class::g()->get_schema();
		wp_verify_nonce( $_POST['mail_id'] );
		$mail_uid = $_POST['mail_id'];
		$mail_id = imap_msgno( $imap, $mail_uid );
		$task_mail = array();
		$task_mail['mail_id'] = $_POST['mail_div'];
		if ( 'create_task' === $_POST['management_mail'] ) {
			$header = imap_header( $imap, $mail_id );
			$from_info = $header->from[0];
			$reply_info = $header->reply_to[0];
			$client_name = $from_info->personal;
			$client_mail = $from_info->mailbox . '@' . $from_info->host;
			$mail_title = $header->subject;
			$mail_test = _get_body_attach( $imap, $mail_id );
			$body_text = $mail_test['body'];
			$body_text = strip_tags( $body_text, '<img>' );
			$search = '/(<img[^>]+>)/';
			$replace = '';
			$body_text = preg_replace( $search, $replace, $body_text, -1, $count );
			// do work....
			$user_id = email_exists( $client_mail );
			if ( empty( $user_id ) ) {
				$randompw = wp_generate_password( 10, false );
				$userdata = array(
					'user_login'  => $client_name,
					'user_nicename' => $client_name,
					'user_email' => $client_mail,
					'user_pass' => $randompw,
					'role' => 'Client',
				);
				$user_id = wp_insert_user( $userdata );
			}
			global $wpdb;
			$list_task = $wpdb->get_results( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_name=%s", array( 'ask-task-' . $user_id ) ) );
			if ( 0 === count( $list_task ) ) {
				$task = \task_manager\Task_Class::g()->update(
					array(
						'title' => __( 'Ask', 'task-manager' ),
						'slug' => 'ask-task-' . $user_id,
						'parent_id' => \wps_customer_ctr::get_customer_id_by_author_id( $user_id ),
						)
				);
				$task_id = $task->id;
			} else {
				$task_id = $list_task[0]->ID;
			}
			$task = \task_manager\Task_Class::g()->get( array(
				'include' => array( $task_id ),
			), true );
			$_POST['point']['author_id'] = $user_id;
			$_POST['point']['status'] = '-34070';
			$_POST['point']['date'] = current_time( 'mysql' );
			$_POST['point']['content'] = $body_text;
			$_POST['point']['post_id'] = $task_id;
			$point = \task_manager\Point_Class::g()->update( $_POST['point'] );
			$task->task_info['order_point_id'][] = (int) $point->id;
			\task_manager\Task_Class::g()->update( $task );
			imap_delete( $imap, $mail_id );
			$task_mail['tache'] = 'tache créée.';
			echo wp_json_encode( $task_mail );
			exit();
				// create task and delete e-mail.
		} elseif ( 'leave-be' === $_POST['management_mail'] ) {
			// $status = imap_setflag_full($imap, $mail_id, "\\Seen");
			$task_mail['tache'] = 'tache pas créée.';
			echo wp_json_encode( $task_mail );
			exit();
			// Mark mail as seen and leave it be.
		} // End if().
	}
}


new Mail_link_for_wpshop;
?>
