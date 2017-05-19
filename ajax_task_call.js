/**
 * Création de la tache
 *
 * @param  {int} idMail           l'id de l'email en quetion
 * @param  {int} idDiv            l'id de la div
 * @return {void}             rien
 */
function createTask( idMail, idDiv ) {
	var mailId = idMail;
	var divId = idDiv;
	var managementMail = 'create_task';
	if ( confirm( MailForTaskManager.text_confirm_popup ) ) {
		jQuery.ajax({
			url: ajaxurl, // fonction ajax de wordpress
			type: 'POST', // La méthode indiquée dans le formulaire (get ou post)
			data: 'mail_id=' + mailId + '&management_mail=' + managementMail + '&mail_div=' + divId + '&action=ask_task_mail', // Envoie en post de toute les données, action est l'action que j'ai donné a mon wp_ajax.
			success: function( data ) { // data ici est égal a task_mail qui contient si la tache a été créée.
				var task = JSON.parse( data );
				alert( task.tache );
				jQuery( '#mail_' + task.mail_id ).remove(); // je supprime le message qui vient dêtre traité de l'interface.
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
		url: ajaxurl, // fonction ajax de wordpress
		type: 'POST', // La méthode indiquée dans le formulaire (get ou post)
		data: 'mail_id=' + mailId + '&management_mail=' + managementMail + '&mail_div=' + divId + '&action=ask_task_mail', // Envoie en post de toute les données, action est l'action que j'ai donné a mon wp_ajax.
		success: function( data ) { // data ici est égal a task_mail qui contient si la tache a été créée.
			var task = JSON.parse( data );
			alert( task.tache );
			jQuery( '#mail_' + task.mail_id ).remove(); // je supprime le message qui vient dêtre traité de l'interface.
		}
	});
}

/**
 * Supprime la div si le bouton 'x' de la div en question est cliqué
 *
 * @param  {int} idDiv            l'id de la div
 * @return {void}       rien
 */
function deleteDiv( idDiv ) {
	var divId = idDiv;
	jQuery( '#mail_' + divId ).remove();
}
