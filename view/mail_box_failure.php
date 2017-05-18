<div id="mail_<?php echo esc_html( $i ); ?>">
  <p>Client : <?php echo esc_html( $client_name ); ?> ( <?php echo esc_html( $client_mail ); ?>)</p>
  <p>Intitulé : <?php echo esc_html( imap_utf8( $mail_title ) );  ?></p>
  <p>Cette e-mail n'a pas pu être traité, veuillez vous rendre sur votre boite mail pour visualiser cette e-mail.</p>
</div>
