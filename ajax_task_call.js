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
	if ( confirm( 'êtes-vous sure de vouloir créer la tache ? Toute pièce(s) jointe(s) non télécharger seront perdu.' ) ) {
		jQuery.ajax({
			url: ajaxurl, // Le nom du fichier indiqué dans le formulaire
			type: 'POST', // La méthode indiquée dans le formulaire (get ou post)
			data: 'mail_id=' + mailId + '&management_mail=' + managementMail + '&mail_div=' + divId + '&action=ask_task_mail', // Je sérialise les données (j'envoie toutes les valeurs présentes dans le formulaire)
			success: function( data ) { // Je récupère la réponse du fichier PHP
				var task = JSON.parse( data );
				alert( task.tache );
				jQuery( '#mail_' + task.mail_id ).remove();
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
	url: ajaxurl, // Le nom du fichier indiqué dans le formulaire
	type: 'POST', // La méthode indiquée dans le formulaire (get ou post)
	data: 'mail_id=' + mailId + '&management_mail=' + managementMail + '&mail_div=' + divId + '&action=ask_task_mail', // Je sérialise les données (j'envoie toutes les valeurs présentes dans le formulaire)
	success: function( data ) { // Je récupère la réponse du fichier PHP
		var task = JSON.parse( data );
		alert( task.tache );
		jQuery( '#mail_' + task.mail_id ).remove(); // J'affiche cette réponse
	}
});
}
