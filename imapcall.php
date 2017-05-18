<?php
/**
 * Crée un appel vers la boite mail pour récuperer son contenu.
 *
 * @var $imap La variable qui contient la connection a la boite mail.
 */
$task_info = get_option( 'task_info', false );
$hostname = '{imap.gmail.com:993/imap/ssl/novalidate-cert}';

if ( $task_info ) {
	try {
		$imap = imap_open( $hostname, $task_info['task_mail'], $task_info['task_pass'] );
	} catch ( Exception $e ) {
		echo 'Une erreur est survenue.' . esc_html( $e->getMessage() );
	}
}
