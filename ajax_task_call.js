/**
 * Télécharge les pieces jointes du mail.
 *
 * @param  {int} mailUid   l'id unique du message.
 * @param  {string} mailTitle le title du mail avec des under-score.
 * @return {void}            rien.
 */
function dlAttachs( mailUid, mailTitle ) {
  var uid = mailUid;
  var title = mailTitle;
  jQuery.ajax({
      url: ajaxurl, // Le nom du fichier indiqué dans le formulaire
      type: 'POST', // La méthode indiquée dans le formulaire (get ou post)
      data: 'mail_uid=' + uid + '&mail_title=' + title + '&action=download_mail_attachs', // Je sérialise les données (j'envoie toutes les valeurs présentes dans le formulaire)
      success: function( url ) { // Je récupère la réponse du fichier PHP
        window.open( url );
      }
  });
}

/**
 * Création de la tache
 *
 * @param  {int} idMail           l'id de l'email en quetion
 * @param  {int} idDiv            l'id de la div
 * @param  {string} clientMail  l'email du client.
 * @param  {string} mailContent le contenue du corps du mail.
 * @param  {int} attachs     Si il y a un attachement ou pas.
 * @param  {string} mailComment Le commentaire, si il y en a un.
 * @return {void}             rien
 */
function createTask( idMail, idDiv, clientMail, mailContent, attachs, mailComment ) {

  // Pour l'instant client_mail et mail_cotent ne sont pas utilisé. En attentes de directives.
	var mailId = idMail;
	var divId = idDiv;
  var comment = mailComment;
	var attachment = attachs;
	var managementMail = 'create_task';
		if ( confirm( MailForTaskManager.text_confirm_popup ) ) {
			jQuery.ajax({
				url: ajaxurl, // Fonction ajax de wordpress
				type: 'POST', // La méthode indiquée dans le formulaire (get ou post)
				data: 'mail_id=' + mailId + '&management_mail=' + managementMail + '&mail_div=' + divId + '&action=ask_task_mail&attachment=' + attachment + '&comment=' + comment, // Je sérialise les données (j'envoie toutes les valeurs présentes dans le formulaire), // Envoie en post de toute les données, action est l'action que j'ai donné a mon wp_ajax.
				success: function( data ) { // Data ici est égal a task_mail qui contient si la tache a été créée.
								var task = JSON.parse( data );
								alert( task.tache );
								jQuery( '#mail_' + task.mail_id ).remove(); // Je supprime le message qui vient dêtre traité de l'interface.
							}
						});
			}
}

/**
 * Ne crée pas la tache, marque l'email comme lu
 *
 * @param  {int} idMail           l'id de l'email en quetion
 * @param  {int} idDiv            l'id de la div
 * @return {void}             rien
 */
function leaveBe( idMail, idDiv ) {
	var mailId = idMail;
	var divId = idDiv;
	var managementMail = 'leave_be';
	jQuery.ajax({
		url: ajaxurl, // Fonction ajax de wordpres.s
		type: 'POST', // La méthode indiquée dans le formulaire (get ou post)
		data: 'mail_id=' + mailId + '&management_mail=' + managementMail + '&mail_div=' + divId + '&action=ask_task_mail', // Envoie en post de toute les données, action est l'action que j'ai donné a mon wp_ajax.
		success: function( data ) { // Data ici est égal a task_mail qui contient si la tache a été créée.
			var task = JSON.parse( data );
			alert( task.tache );
			jQuery( '#mail_' + task.mail_id ).remove(); // Je supprime le message qui vient dêtre traité de l'interface.
		}
	});
}
