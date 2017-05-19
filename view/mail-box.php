<div id="mail_<?php echo $i; ?>">
  <form class='task_mail' id='task_mail_<?php echo $i; ?>' action='' method='post'>
  <input style='display:none;' type='text' id='mail_id' name='mail_id' value=<?php echo $mail_uid; ?> />
  <p><label for="client_mail">Client :</label><input id="client_mail" type="text" value='<?php echo $client_name . ' ( ' . $client_mail . ' )'; ?>' /></p>
  <p>Intitulé : <?php echo imap_utf8($mail_title); ?></p>
  <p><label for="mail_content">Demande :</label><textarea id="mail_content"><?php echo imap_utf8($body_text); ?></textarea></p>
  <p><label for="mail_comment">Commentaire :</label><input type="text" id="mail_comment"></input></p>
  <?php if ( $count ) {?>
    <p> Cette e-mail contient <?php echo $count; ?> image(s) qui ne peuvent pas être affiché, vous ne pourrez pas voir ces images une fois la tache créée.</p>
  <?php  }
  if ( ! empty( $mail_test['attachment'] ) ) {?>
    <p> Cette e-mail contient des attachements, vous pouvez les télécharger :   <button id='btn_download' type='button' name='download_attachments' onclick='dlAttachs(<?php echo $mail_uid . ',"' . $mail_title_sanitize . '"'; ?>);'>Télécharger pièce(s) jointe(s)</button></p>
  <?php } ?>
	<p> vous pouvez vous consulter votre mail directement sur votre boite mail <a href='http://www.gmail.com' target='__blank' >ici</a></p>
  <button id='btn_create' type='button' name='management_mail' onclick='createTask(<?php echo $mail_uid . ',' . $i; ?>,document.getElementById("client_mail").value,document.getElementById("mail_content").value,<?php echo $attachs; ?>, document.getElementById("mail_comment").value);' value="create_task">Créer la tache</button>
  <button id='btn_not_create' type='button' name='management_mail' onclick='leaveBe(<?php echo $mail_uid . ',' . $i;  ?>);' value="leave_be">Ne pas créer la tache</button>
  </form>
</div>
