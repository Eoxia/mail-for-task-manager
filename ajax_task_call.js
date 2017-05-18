  function dlAttachs(mail_uid, mail_title) {
    var id = mail_uid
    var title = mail_title
    jQuery.ajax({
        url: '', // Le nom du fichier indiqué dans le formulaire
        type: 'POST', // La méthode indiquée dans le formulaire (get ou post)
        data: 'mail_uid=' + id + '&mail_title=' + title, // Je sérialise les données (j'envoie toutes les valeurs présentes dans le formulaire)
        success: function(url) { // Je récupère la réponse du fichier PHP
          window.open(url);
        }
    });
  }
  function createTask(id, i, client_mail, mail_content, attachs, mail_comment){
    // pour l'instant client_mail et mail_cotent ne sont pas utilisé. En attentes de directives.
    if (confirm('êtes-vous sure de vouloir créer la tache ? Toute pièce(s) jointe(s) non télécharger sera perdu.') ) {
    var mail_id = id;
    var management_mail = 'create_task';
    var attachment = attachs;
    var comment = mail_comment;
    jQuery.ajax({
        url: '', // Le nom du fichier indiqué dans le formulaire
        type: 'POST', // La méthode indiquée dans le formulaire (get ou post)
        data: 'mail_id=' + mail_id + '&management_mail=' + management_mail + '&mail_div=' + i + '&attachment=' + attachment + '&comment=' + comment, // Je sérialise les données (j'envoie toutes les valeurs présentes dans le formulaire)
        success: function(data) { // Je récupère la réponse du fichier PHP
          var task = JSON.parse(data);
          alert(task.tache);
          alert(task.attach);
          jQuery("#mail_"+task.mail_id).remove();
        }
    });
    }
  }
  function leaveBe(id, i){
    var mail_id = id;
    var management_mail = 'leave_be';
    jQuery.ajax({
        url: '', // Le nom du fichier indiqué dans le formulaire
        type: 'POST', // La méthode indiquée dans le formulaire (get ou post)
        data: 'mail_id=' + mail_id + '&management_mail=' + management_mail + '&mail_div=' + i, // Je sérialise les données (j'envoie toutes les valeurs présentes dans le formulaire)
        success: function(data) { // Je récupère la réponse du fichier PHP
            var task = JSON.parse(data);
            alert(task.tache);
            jQuery("#mail_"+task.mail_id).remove();
             // J'affiche cette réponse
        }
    });
  }
