<div id="mail_<?php echo esc_html( $i ); ?>">
  <form class='task_mail' id='task_mail_<?php echo esc_html( $i ); ?>' action='' method='post'>
  <input style='display:none;' type='text' id='mail_id' name='mail_id' value=<?php echo esc_html( $mail_uid ); ?> />
  <p><label for="client_mail">Client :</label><input id="client_mail" type="text" value='<?php echo esc_html( $client_name . ' ( ' . $client_mail . ' )' ); ?>' /></p>
  <p>Intitulé : <?php echo esc_html( imap_utf8( $mail_title ) ); ?></p>
  <p><label for="mail_content">Demande :</label><textarea id="mail_content"><?php echo esc_html( imap_utf8( $body_text ) ); ?></textarea></p>
<?php if ( $count ) { ?>
		<p> Cette e-mail contient <?php echo esc_html( $count ); ?> image(s) qui ne peuvent pas être affiché, vous ne pourrez pas voir ces images une fois la tache créée. Si vous voulez les consulter avant de créer votre tache rendez-vous : <a href='http://www.gmail.com' target='__blank' >ici</a></p></p>
<?php }
if ( ! empty( $mail_test['attachment'] ) ) {?>
	<!-- Traites les cas ou il y a des attachements. le code en bas n'est qu'un placeholder et ne marche pas. -->
	<p> Cette e-mail contient des attachements, veuillez vous rendre sur votre boite mail pour les consulter :   <a href='http://www.gmail.com' target='__blank' >ici</a></p>
	<?php } ?>
  <button id='btn_create' type='button' name='management_mail' onclick='createTask(<?php echo esc_html( $mail_uid . ',' . $i ); ?>);' value="create_task" >Créer la tache</button>
  <button id='btn_not_create' type='button' name='management_mail' onclick='leaveBe(<?php echo esc_html( $mail_uid . ',' . $i );  ?>);' value="leave_be">Ne pas créer la tache</button>
  </form>
</div>
<!-- do readme, make array for task mail & pass, clean folder (both v1 & v2), do views for fields. -->
