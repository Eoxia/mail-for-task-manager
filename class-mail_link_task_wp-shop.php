<?php
/**
 * Plugin Name: Mail link Task wp-shop
 * Description: Add your tasks automatically on wp-shop
 * Version: 1.0.1
 * Author: Bactery
 * License: GPL2
 */

 DEFINE( 'PLUGIN_MAIL_FOR_TASK_MANAGER_PATH', realpath( plugin_dir_path( __FILE__ ) ) . '/' );
 DEFINE( 'PLUGIN_MAIL_FOR_TASK_MANAGER_URL', plugins_url( basename( __DIR__ ) ) . '/' );
 DEFINE( 'PLUGIN_MAIL_FOR_TASK_MANAGER_DIR', basename( __DIR__ ) );

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

		add_action( 'admin_enqueue_scripts', array( $this, 'wpb_adding_scripts' ) );
		add_action( 'admin_init' , array( &$this, 'register_fields' ) );
		add_action( 'wp_ajax_ask_task_mail' , array( &$this, 'callback_ask_task' ) );
		add_action( 'admin_menu', array( &$this, 'add_admin_menu' ) );
	}

	/**
	 * Ajoute le script qui contient toute les fonctions que j'ai codé en javascript et lui donne le nom 'form-mail'.
	 *
	 * @return void
	 */
	public function wpb_adding_scripts() {
		wp_register_script(
			'form-mail', // nom du script que l'on enregistre.
			PLUGIN_MAIL_FOR_TASK_MANAGER_URL . '/ajax_task_call.js', // où il se trouve
			array( 'jquery' ) // Sa dépendance.
		);
		$translation_array = array(
			'text_confirm_popup' => __( 'êtes-vous sure de vouloir créer la tache ? Toute pièce(s) jointe(s) non télécharger sera perdu.', 'mail-for-task-manager' ),
		);
		wp_localize_script( 'form-mail', 'MailForTaskManager', $translation_array ); // Envoie le tableau dans notr fichier javascript :: ne marche pas.
		wp_enqueue_script( 'form-mail' );
	}

	/**
	 * Enregistre et ajoute tout les champs definie plus bas dans parametres->Ecriture.
	 *
	 * @return void.
	 */
	public function register_fields() {
		$task_info = get_option( 'task_info', array() ); // récupere le tableau task_info dans wp_options et si elle n'existe pas, crée un tableau vide.

		add_settings_section( // Ajoute la description du service.
			'email_for_task_manager',
			__( 'Génération de tache par e-mail' , 'task_mail' ),
			array( $this, 'add_setting_using_task_mail' ),
			'writing'
		);

		add_settings_field( // Ajoute le champ E-mail.
			'task_mail',
			'<label for="task_mail" >' . __( 'E-mail' , 'task_mail' ) . '</label>',
			array( $this, 'add_setting_task_mail' ),
			'writing',
			'email_for_task_manager',
			$task_info
		);

		add_settings_field( // Ajoute le champ mot de passe.
			'task_pass',
			'<label for="task_pass" >' . __( 'Mot de passe' , 'task_mail' ) . '</label>',
			array( $this, 'add_setting_task_pass' ),
			'writing',
			'email_for_task_manager',
			$task_info
		);
		register_setting( 'writing', 'task_info', array( $this, 'update_setting_task_field' ) ); // Si les champs sont remplis, met à jour la base de données avec les nouvelles valeurs.
	}

	/**
	 * Ajoute le champ d'avertissement dans le menu 'Ecriture' dans les parametres de Wordpress.
	 */
	public function add_setting_using_task_mail() {
		esc_html_e( 'Avant d\'utiliser ce service, soyez sure que l\'e-mail que vous allez utiliser est activé en imap. Ce service ne marche que pour les boite Gmail.' , 'using_task_mail' ); // Message pour le champ de 'Génération de tache par email.
	}

	/**
	 * Ajoute le champ email dans le menu 'Ecriture' dans les parametres de Wordpress.
	 *
	 * @param array $task_info tableau contenant la variable email et mot de passe si ils exist.
	 */
	public function add_setting_task_mail( $task_info ) {
		require( 'view/settings_task_mail.php' ); // HTML - champ E-mail.
	}

	/**
	 * Ajoute le champ mot de passe dans le menu 'Ecriture' dans les parametres de Wordpress.
	 *
	 * @param array $task_info tableau contenant la variable email et mot de passe si ils exist.
	 */
	public function add_setting_task_pass( $task_info ) {
		require( 'view/settings_task_pass.php' ); // HTML - champ mot de passe.
	}

	/**
	 * Met a jour l'email et le mot de passe à utiliser pour Mail_for_task_manager pour le site.
	 *
	 * @param array $settings un tableau qui contient les variable task_mail et task_pass.
	 * @return array $settings un tableau qui contient les variable task_mail et task_pass.
	 */
	public function update_setting_task_field( $settings ) {
		// Récupere le tableau de email et mot de passe, les sanitize puis les renvoie.
		if ( ! empty( $settings['task_mail'] ) ) {
			$settings['task_mail'] = sanitize_text_field( $settings['task_mail'] );
		}
		if ( ! empty( $settings['task_pass'] ) ) {
			$settings['task_pass'] = sanitize_text_field( $settings['task_pass'] );
		}

		return $settings;
	}

	/**
	 * Ajoute un champ dans le menu 'Ecriture' dans les parametres de Wordpress.
	 */
	public function add_admin_menu() {
		add_menu_page( 'Test', 'Mail for task manager', 'manage_options', 'mailfortask', array( &$this, 'mail_to_task_html' ) ); // le menu Mail for task manager dans la barre de gauche de l'interface admin.
	}
	/**
	 * Boite html qui va montrer le mail sous forme de box.
	 *
	 * @return void nothing.
	 */
	public function mail_to_task_html() {
		require( 'imapcall.php' ); // Appel à la connection à la boite e-mail. retourne $imap.
		if ( $imap ) {
			$num_mails = imap_num_msg( $imap ); // nombre de message dans la boite
			for ( $i = 1 ; $i <= $num_mails ; $i++ ) { // boucle qui lit tout les messages
				$header = imap_header( $imap, $i ); // récupere l'objet header.
				if ( 'U' === $header->Unseen ) { // Si le message est 'non-lu'.
					$from_info = $header->from[0];
					$client_name = $from_info->personal; // le nom du client
					$client_mail = $from_info->mailbox . '@' . $from_info->host; // son email.
					$mail_title = $header->subject;
					$mail_test = _get_body_attach( $imap, $i ); // Retourne $mail_test['attachment'] & $mail_test['body'].
					if ( ! empty( $mail_test['attachment'] ) ) { // Si l'email possède une pièce jointe.
						$attachs = 1;
					} else {
						$attachs = 0;
					}
					$body_text = $mail_test['body']; // le corps du mail décodé.
					$body_text = strip_tags( $body_text, '<img>' );
					$search = '/(<img[^>]+>)/';
					$replace = '';
					$body_text = preg_replace( $search, $replace, $body_text, -1, $count ); // compte combien d'images sont dans le corps.
					$mail_uid = imap_uid( $imap, $i ); // récupere l'uid du mail qui est unique par boite mail seuilent pour pouvoir traiter le bon mail même si des mails sont supprimer entre temps.
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
		 *  Crée la tache et detruit l'email ou ne crée pas la tache et marque l'email comme lu
		 *
		 * @return void tableau task_mail qui contient si la tache et créer ainsi que l'id de la div pour pouvoir la supprimer.
		 */
	public function callback_ask_task() {
		// voir la fonction mail_to_task_htm pour commentaire plus détaillé.
		if ( isset( $_POST['mail_id'] ) ) {
			require( 'imapcall.php' );
			$array_task = array();
			$mail_uid = sanitize_text_field( $_POST['mail_id'] );
			$mail_id = imap_msgno( $imap, $mail_uid ); // récupere l'id du bail dans la boite grace a son uid.
			$task_mail = array();
			$task_mail['mail_id'] = sanitize_text_field( $_POST['mail_div'] );
			if ( 'create_task' === $_POST['management_mail'] ) {
				$header = imap_header( $imap, $mail_id );
				$from_info = $header->from[0];
				$client_name = $from_info->personal;
				$client_mail = $from_info->mailbox . '@' . $from_info->host;
				$mail_title = $header->subject;
				$mail_test = _get_body_attach( $imap, $mail_id );
				$body_text = $mail_test['body'];
				$body_text = strip_tags( $body_text, '<img>' );
				$search = '/(<img[^>]+>)/';
				$replace = '';
				$body_text = preg_replace( $search, $replace, $body_text, -1, $count );
				$user_id = email_exists( $client_mail );
				if ( empty( $user_id ) ) { // si le client n'existe pas, il le crée.
					$random_pw = wp_generate_password( 10, false );
					$userdata = array(
						'user_login'  => $client_name,
						'user_nicename' => $client_name,
						'user_email' => $client_mail,
						'user_pass' => $random_pw,
						'role' => 'Client',
					);
					$user_id = wp_insert_user( $userdata );
				}
				global $wpdb;
				$list_task = $wpdb->get_results( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_name=%s", array( 'ask-task-' . $user_id ) ) );
				if ( 0 === count( $list_task ) ) { // si la tache n'existe pas, il l'a crée.
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
				$point = \task_manager\Point_Class::g()->update( $_POST['point'] );  // rajoute le point dans la tache.
				$task->task_info['order_point_id'][] = (int) $point->id;
				\task_manager\Task_Class::g()->update( $task );
				imap_delete( $imap, $mail_id );
				$task_mail['tache'] = 'tache créée.';
				echo wp_json_encode( $task_mail ); // renvoie ['tache'] et ['mail_id'].
				exit();
					// crée la tache et supprime l'e-mail.
			} elseif ( 'leave-be' === $_POST['management_mail'] ) {
				$status = imap_setflag_full( $imap, $mail_id, '\\Seen' ); // Marque l'email comme lu.
				$task_mail['tache'] = 'tache pas créée.';
				echo wp_json_encode( $task_mail );  // renvoie ['tache'] et ['mail_id'].
				exit();
				// Marque l'email comme lu et ne crée pas la tache.
			} // End if().
		} // End if().
	}
}


new Mail_link_for_wpshop;
