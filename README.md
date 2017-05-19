# mail-for-task-manager
Créez vos tâches depuis les emails de vos clients (extension task_manager_wpshop)

# Important : Les plugins Task Manager ainsi que WP shop sont requis pour l'utilisation de ce plugin. #

## Ce plugin vous permet de connecter votre boite mail à votre site wordpress pour
qu'il traite vos mails dans une interface où vous pourrez les transformer en tache. ##

## Réglages (Gmail) ##

	*Réglages Boite mail:
		*Connéctez-vous à la boite mail où vous allez recevoir les mails que vous pourrez transformer en taches.
		*Cliquez sur la roue denté en haut à droite de votre boite mail puis sur 'Paramètres'.
		*Choisissez 'Transport et POP/IMAP' dans le menu en haut de la page.
		*Dans la rubrique 'Accès IMAP', sélectionné 'Activer IMAP' dans 'Etat'.
	*Réglages Wordpress
		*Rendez-vous sur l'interface admin de votre site Wordpress.
		*Dans la barre de gauche, cliquez sur 'Réglages' puis 'Ecriture'.
		*Vous trouverez une rubrique 'Génération de tache par e-mail'
		*Remplissez le champ 'E-mail' et 'Mot de passe' avec les informations de la boite mail vous venez de configurer.

	Pour l'instant l'utilisation de ce plugin ne marche qu'avec une boite gmail.

## Utilisation ##

	L'utilisation de ce plugin ce fait directement sur le menu Mail for task manager dans votre bar de droite de la l'interface admin.

	Chaque e-mail **non-lu** sera afficher sous la forme 'Client' (soit la personne qui vous a envoyé le mail), 'Intitulé' (le titre du mail), 'Demande' (corps du mail).
	Si l'e-mail contient une ou des images dans le corps du mail ou des pièces jointes, vous serrez averti en dessous de 'Demande' et vous aurez accès à un lien pour vous rendre sur votre boite mail.

	Pour chaque mail vous aurez deux chois :

	*Créer la tache ( créera une tache pour le client en question ):
		*Si l'email de l'expediteur n'est pas enregistré en tant que client, il sera créer.
		*Puis un point sera créer sur la tache 'Demande' de ce client.
		*Si la tache 'Demande' n'existe pas elle sera créer.
		*l'email en question sera supprimé ( c'est pour sa qu'il est important d'aller le consulter si il contient des images ou des pièces jointes ).

	*Ne pas créer la tache ( ne fera rien a part marqué l'email comme lu ):
		*Marque l'email comme lu.

	Note : si le mail contient une image dans le corps ainsi qu'une pièce jointe ou que le corps du mail contient plus de 5000 charactères il ne pourra pas être traité.

## Pour Developpeurs ##

*Les données 'E-mail' et 'Mot de passe' qui sont rentré dans le menu d'écriture sont ajouté dans wp_options, le nom de la clé est 'task_info', c'est un tableau qui contient deux paramètres : 'task_mail' et 'task_pass'.
*La fonction pour se connecter sur l'adresse gmail de l'utilisateur est la bibliothèque 'imap' de PHP ( http://php.net/manual/fr/book.imap.php	).
*L'image dans le corps + un piece jointes génère une chaine de charactère de ~30 000 lignes donc on ne le gère pas.
*Les logs ne sont pas encore fait.
