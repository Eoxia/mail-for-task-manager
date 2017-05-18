<?php
require_once('../../../wp-load.php');
require_once(ABSPATH.'wp-includes/pluggable.php');
if ( isset( $_POST['mail_id'] ) ) {
  include( 'imapcall.php' );
  $mail_id = $_POST['mail_id'];
  if ( $_POST['management_mail'] == 'create_task' ) {
    $header = imap_header($imap, $mail_id);
    $from_info = $header->from[0];
    $reply_info = $header->reply_to[0];
    $client_name = $from_info->personal;
    $client_mail = $from_info->mailbox . '@' . $from_info->host;
    $mail_title = $header->subject;
    $body_text = imap_fetchbody($imap,$mail_id,1.2, FT_PEEK);
    if(!strlen($body_text)>0){
        $body_text = imap_fetchbody($imap,$mail_id,1, FT_PEEK);
    }
    $mail_content = $body_text;
    // do work....
  $user_id = email_exists( $client_mail );
  if ( empty( $user_id ) ) {
    $randompw = wp_generate_password( 10, false );
    $userdata = array(
    'user_login'  =>  $client_name,
    'user_nicename' => $client_name,
    'user_email' => $client_mail,
    'user_pass' =>  $randompw,
    'role' =>	'Client',
    );
    $user_id = wp_insert_user( $userdata );
    }
    global $wpdb;

    $query = "SELECT ID FROM {$wpdb->posts} WHERE post_name=%s";
    $list_task = $wpdb->get_results( $wpdb->prepare( $query, array( 'ask-task-' . $user_id ) ) );
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
    $_POST['point']['content'] = $mail_content;
    $_POST['point']['post_id'] = $task_id;
    $point = \task_manager\Point_Class::g()->update( $_POST['point'] );
    $task->task_info['order_point_id'][] = (int) $point->id;
    \task_manager\Task_Class::g()->update( $task );
    imap_delete( $imap, $mail_id );
    echo 'hi';
    // create task and delete e-mail.
  } elseif ( $_POST['management_mail'] == 'leave_be' ) {
    $status = imap_setflag_full($imap, $mail_id, "\\Seen \\Flagged");
    echo 'ha';
    // mark mail as seen and leave it be
  }
}
