<!DOCTYPE html>
<?php
if (session_id()=='')
{
	session_start();
};
?>
<html>
<head>
	<?php
		include('top.php');
		include('head.php');
	?>
</head>
<body>
<?php
	include('log.php'); // chargement de la fonction de log
	addlog("chargement documentation.");

?>	
<div id="principal">
	<header id="en-tete">
		<?php
			include('menu.php');
		?>
	</header>
	<section>
	<h1>Guide d'utilisation et bonnes pratiques du gestionnaire des changements CENTREON.</h1>
	<div id="tabs_Doc">
		<ul>
			<li><a href="#tabs_Doc-1">Présentation générale</a></li>
			<li><a href="#tabs_Doc-2">Formuler une nouvelle demande</a></li>
			<li><a href="#tabs_Doc-3">Reprise d'un brouillon</a></li>
			<li><a href="#tabs_Doc-4">Consultation d'une demande</a></li>
			<li><a href="#tabs_Doc-5">Trucs et astuces</a></li>
		</ul>
		<div id="tabs_Doc-1">
		<h2>Présentation générale</h2>
			<div id="accordionDoc_gen">
				<h3>Un peu d'histoire...</h3>
				<div id="histoire">
					<p>TESSI Technologies a choisi pour la supervision de son SI et des plateformes de production des clients la suite logicielle CENTREON développée par Merethis.</p>
					<p>Dès le début du projet il a fallu mettre en place des règles afin d'organiser de manière rationnelle et efficace les mises en supervision des équipements.
					 Le SI et les plateformes de production étant en constantes évolutions, il fallait pouvoir suivre ces changements.<br>
					 Une première version de la matrice a été rapidement rédigée afin de rassembler au sein d'un seul et même document l'ensemble des informations nécessaires à la supervision.
					 Assez rapidement cette version "doc" s'est avérée problématique en terme de gestion et de fiabilité des informations qu'elle contenait.</p>
					<p>Plusieurs sources de ce document étaient mises à jour simultanément sans qu'il y ai systématiquement de consultation préalable entre les différents acteurs.
					 Une évolution de cette matrice était donc nécessaire; le paramétrage de CENTREON étant stocké dans une base de données, une extraction en était donc possible et une version Web de la matrice s'est alors imposée d'elle même.</p>
					<p>Une interface centralisée avec la garantie d'avoir les dernières informations du paramétrage au moment de la demande, le développement du <em>gestionnaire des changements CENTREON</em> pouvait commencer...</p> 
                                </div>
				<h3>Objet du gestionnaire</h3>
				<div id="objet">
						<p>Le gestionnaire des changements CENTREON (ci-après nommée "l'interface") a pour objet la formalisation des demandes de supervision de l'infrastructure du SI Tessi Technologies ainsi que des prestations client.</p>
                                </div>
				<h3>Objectifs</h3>
				<div id="objectif">
					<p>Objectifs principaux:</p>
					<p>	- formalisation et standardisation des demandes<br>
						- centralisation des données<br>
						- garantie de la disponibilité des derniers types de contrôle réalisables<br>
					</p>
                                </div>
				<h3>Glossaire</h3>
				<div id="glossaire">
					<p>CENTREON utilise des termes bien spécifiques pour désigner les éléments de configuration qui le compose. Voici les principaux que vous retrouverez tant au travers de cette interface que dans CENTREON.</p>
					<p><b>Prestation</b>: C'est le service vendu et rendu au client de manière contractuelle. Tout équipement concerné par la production de cette prestation rentre dans le périmètre de la supervision. Le schéma d'architecture rédigé lors de la phase projet est une bonne base pour la rédaction de la demande de supervision.</p>
					<p><b>Hôte</b>: équipement interrogeable via le réseau IP. Tout équipement ayant une adresse IP peut sur le principe être supervisé au travers de différents agents (SNMP et NRPE pour les principaux).</p>
					<p><b>Service</b>: Il s'agit d'un point de contrôle rattaché à l'hôte; également appelé indicateur ou sonde. Tout élément d'un équipement pouvant être "mesuré" par quelque moyen que ce soit au travers des agents ci-dessus peut être un indicateur.</p>
					<p><b>Période temporelle</b>: également appelé plage horaire. c'est la période durant laquelle un service va être contrôlé. Les disques, par exemple, sont contrôlés 24h sur 24 7j/7.</p>
					<p><b>Fréquence</b>: c'est l'intervalle de temps entre deux contrôles au sein de la période temporelle.<br> Il faut distinguer deux fréquences:<br> <b>La fréquence normale</b> qui correspond à l'intervalle de temps entre les contrôles lorsque le service est dans un statut OK;<br> et <b>la fréquence non régulière</b> (ou de re-essai) qui correspond aux contrôles complémentaires permettant de valider l'état d'anomalie (dégradé critique ou inconnu) d'un service.<br>
					En règle générale deux contrôles sont paramétrés par service avec un délai plus ou moins important selon la fréquence principale; le second permettant d'éviter les faux positifs (time-out lors du premier contrôle par exemple).</p>
					<p><b>Controle actif ou inactif</b>: Il s'agit ici d'indiquer si l'hôte ou le service paramétré dans CENTREON est actuellement supervisé ou non.<br> Un contrôle actif permettra de collecter les informations sur cet indicateur pour ensuite générer les graphes de performance (traffic réseau, occupation disque, etc...) ou produire les indicateurs de disponibilité (ping, etc...).</p>
					<p><b>Seuil</b>: Valeur permettant de déclencher le changement de statut d'un service donc les alertes. Ces seuils permettent de gérer les statuts dégradé (WARNING) et critique (CRITIQUE).<br>
					 Le statut INCONNU (UNKNOWN) est indépendant de ces seuils et remonte en général si le service renvoi une information inattendue ou incohérente.<br>
					 Le statut CRITIQUE peut également remonter en cas de time-out sur l'exécution du service. Cela indique généralement une surcharge de l'hôte.</p>
					<p><b>Les icônes</b>:<br>
					 Dans les différents formulaires, vous trouverez des icônes vous indiquant les endroits requérant la saisie d'une information obligatoire.<br>
					 Les champs n'ayant pas cet icône sont facultatifs, il s'agit de champs d'informations complémentaires.</p>
					<p>Le crayon <img alt="crayon" src="images/img_edit.png"> vous indique cette obligation.<br>
					 La croix rouge <img alt="croix" src="images/img_ko.png"> vous indique une erreur de saisie; en général c'est que le champ est vide.<br>
					 La coche verte <img alt="coche verte" src="images/img_ok.png"> vous indique que la saisie de ce champ est valide.<br>
					 Le cadenas avec la coche verte <img alt="cadenas" src="images/img_ver.png"> vous indique que le champ est valide mais qu'il ne peut être modifié.</p>
					<p>Vous retrouverez ces icônes un peu partout dans les différents formulaires aux endroits nécessitant votre attention.</p>
					<p>Vous remarquerez également à différents endroits des <img alt="point-interrogation-16" src="images/point-interrogation-16.png">, cliquez dessus pour afficher des informations spécifiques au champ concerné.</p>
                                </div>
				<h3>Informations importantes</h3>
				<div id="important">
					<p>Avant de commencer la rédaction d'une demande de supervision, vous devez savoir ce que vous voulez superviser et comment. L'interface n'est qu'un moyen de transcrire cette demande, elle ne peut en aucun cas choisir à votre place ce que vous voulez faire.<br>
					 Pour toute question sur les méthodes de supervision et sur ce qu'il est possible de faire, veuillez contacter Cedric Meschin au 05.57.22.77.13 ou par mail à centreon_tt@tessi.fr.</p> <br>
					 
					<p>Cette interface est developpée en HTML / PHP et utilise Javascript. Les messages de confirmation utilisent exclusivement des boîtes de dialogue que les navigateurs ont tendance à vouloir masquer.<br>
					 Rassurez vous il n'y en a pas énorménent. Ne cochez pas la case suivante sinon vous serez bloqués dans la rédaction de votre demande.</p>
					<img alt="popup_message" src="images/popup_message.png" width=400 border="1">
					<p>Pas de panique, si vous avez cocher la case par habitude, il vous suffira de recharger la page pour que les messages réapparaissent. Toutefois, selon où vous en êtes dans la rédaction de votre demande, certaines informations pourraient être perdues, il vous faudra les ressaisir.</p>
				</div>
			</div>		
		</div>
		<div id="tabs_Doc-2">
		<h2>Formuler une nouvelle demande</h2>
			<div id="accordionDoc_nouveau">
				<h3>onglet Informations générales</h3>
                                <div id="info_gen">
				<fieldset id="f_info_gen">
					<p>Cet onglet a pour but d'identifier de manière précise et unique la demande de changement CENTREON.</p>
					<p>Elle contient le nom du demandeur, la date à laquelle la demande a été initialisée, son statut, sa référence.
					 Ces informations sont automatiquement générées grâce à l'authentification qui vous a été demandée au préalable.</p>
					<img alt="information générale sur la demande" src="images/info_gen_auto.png" border="1">
					<p>Elle contient également la prestation et la date de supervision souhaitée; ces deux champs sont obligatoirement à compléter pour pouvoir poursuivre la demande.</p>
					<img alt="information sur la prestation" src="images/info_gen_prestation.png" border="1">
					<p>Le champ prestation est une liste déroulante contenant l'ensemble des prestations actuellement déclarée dans CENTREON. Cela ne veut pas pour autant dire qu'une supervision est en place, certaines prestations ont été crées de manière anticipées.<br>
					Vous pouvez soit:<br>
					- choisir un nom dans la liste<br> <img alt="sélection prestation" src="images/info_gen_prestation_selec.png" width=400 border="1"> <br> <br>
					- saisir tout ou partie du nom d'une prestation pour en réduire le choix puis cliquer sur le nom souhaité <br> <img alt="sélection prestation" src="images/info_gen_prestation_saisie.png" width=400 border="1"> <br> <br>
<!-- 					- sélectionner "nouveau" et saisir le nom de la nouvelle prestation.<br> <img alt="sélection prestation" src="images/info_gen_prestation_nouveau.png" width=600 border="1"> <br> <br> -->
					- Si votre prestation n'existe pas, contactez l'administrateur (Tél: 05.57.22.77.13 ou par mail centreon_tt@tessi.fr) afin que la prestation soit créée.
					<p>La date de supervision souhaitée vous propose tout simplement un calendrier.</p>
					<img alt="sélection date" src="images/info_gen_calendrier.png" border="1"> <br>
					<p>Nous tacherons de traiter la demande pour que la supervision soit opérationnelle à la date demandée. Plus la demande sera anticipée, plus il sera possible d'en respecter les délais.</p>
					<p>Deux autres champs peuvent être complétés:<br> 
					 - La liste de notification utilisée par SUSI pour informer de l'état d'avancement de la demande. Elle est pré-remplie avec votre adresse mail, vous pouvez rajouter les destinataires que vous souhaitez voir notifiés.<br>
					 - Le champ commentaire, libre et facultatif, permet d'indiquer brièvement l'objet de la demande. Son contenu sera envoyé dans le premier mail de notification résumant la demande.
					</p>
					<p>Une fois ces quelques informations fournies, vous pouvez cliquer sur le bouton suivant.</p>
					<p>assurez-vous simplement d'avoir les trois(3) coches vertes comme indiqué ci-dessous</p><br>
					<img alt="info_gen_ok" src="images/info_gen_ok.png" width=600 border="1"> <br>
					<p>Si ce n'est pas le cas vous ne pourrez pas continuer.</p>
					<img alt="info_gen_ko" src="images/info_gen_ko.png" width=400 border="1"> <br>
					<p>Corrigez et poursuivez... un nouvel onglet apparait.</p>
					<img alt="info_gen_suivant" src="images/info_gen_suivant.png" width=300 border="1"> <br>
				</fieldset>
				</div>
				<h3>onglet Liste des hôtes et services</h3>
                                <div id="liste_hote_service">
				<fieldset id="f_liste_hote_service">
					<p>Voici une vue générale de cet onglet avec les différentes listes de sélection.<br>
					<img alt="liste_hote_service_general" src="images/liste_hote_service_general.png" width=700 border="1"> <br> <br>
					 par défaut l'interface affiche la liste des services de la prestation puisque cela correspond à la majorité des demandes de changement.
					 <img alt="liste_hote_service_defaut" src="images/liste_hote_service_defaut.png" width=700 border="1"> <br> <br></p>
					<p>Détaillons maintenant chacun des menus</p>
					<h4>Liste des services</h4>
					<fieldset id="liste_service">
						<p>Cette liste recense l'ensemble des services paramétrés et identifiés par la prestation sélectionnée. Elle permet de sélectionner les indicateurs dont vous souhaitez modifier le paramétrage (seuil, période de controle, désactivation etc...)</p>
						<p>Selon l'importance de la prestation, la liste peut être longue.</p>
						 <img alt="liste_hote_service_lst_service" src="images/liste_hote_service_lst_service.png" width=600 border="1"> <br> <br>
						<p><b>Dans cette liste vous ne devez sélectionner que les services que vous souhaitez modifier</b>. Si vous souhaitez simplement ajouter un nouveau service, ne sélectionner rien ou bien un service similaire qui pourra vous servir de modèle. Nous verrons cela plus en détail tout à l'heure.</p>
						<p>Dans le cas d'ajout simple de service, <b>vous devez juste vous assurer que l'hôte est déjà supervisé et rattaché à cette prestation</b>. Si ce n'est pas le cas, rendez-vous dans la section suivante (liste des hôtes) pour plus d'informations.</p>
						<p>Vous pouvez y voir le nom de l'hôte, le nom du service, la fréquence de contrôle, la période temporelle ainsi que l'état du contrôle.</p>
						<p>Sélectionnez les services que vous souhaitez modifier et passez aux listes suivantes.</p>
						<p>Voici quelques informations complémentaires sur les champs de cette liste.</p>
						<p><b>Selection</b>: cochez les cases correspondantes aux services que vous souhaitez modifier.</p>
						<p><b>Hôte</b>: Nom de l'hôte auquel est rattaché le service. Ce nom est le nom paramétré dans le système (Nom de l'ordinateur pour les serveurs Windows par ex.)<br>
						 Si vous passez votre souris dessus, vous verrez apparaitre son adresse IP; cela permet de lever le doute avec certains hôtes nommés de manière identique sur différents centres de production.</p>
						 <img alt="liste_hote_service_ip" src="images/liste_hote_service_ip.png" width=200 border="1"> <br> <br>
						<p><b>Service</b>: Nom du service tel qu'il apparait dans CENTREON. Une convention de nommage a été établie dans le but évident d'éviter au maximum les doublons de supervision. Nous reviendrons plus tard sur ce point.</p>
						<img alt="liste_hote_service_nom" src="images/liste_hote_service_nom.png" width=200 border="1"> <br> <br>
						<p><b>Fréquence</b>: C'est le délai qui sera appliqué entre chaque controle de la sonde. La première valeur indique la fréquence normale de contrôle en cas de résultat OK; la seconde valeur correspond au délai de validation d'un résultat non OK.<br>
						 Par exemple pour le Controle_Date_Systeme, la fréquence de controle normale est toute les heures; en cas d'anomalie un second contrôle sera effectué 5 minutes plus tard.
						 Si l'anomalie est confirmée à l'issue de ce second contrôle, la sonde remontera dans la console CENTREON avec le détail de l'erreur.
						 Si l'anomalie n'est pas confirmée (statut OK), la sonde ne remonte pas d'alerte et la fréquence normale est replanifiée.</p>
						<img alt="liste_hote_service_frequence" src="images/liste_hote_service_frequence.png" width=100 border="1"> <br> <br>
						<p><b>Plage Horaire</b>: c'est la période durant laquelle les contrôles vont être effectués selon la fréquence paramétrée.<br>
						 Ainsi, la date système sera contrôlée du Dimanche au Samedi de 0h à Minuit; en d'autres termes 24h/24 7j/7.</p>
						<img alt="liste_hote_service_plage" src="images/liste_hote_service_plage.png" width=200 border="1"> <br> <br>
						<p><b>Controle</b>: Ce champ indique si le service est actuellement actif ou inactif.</p>
						<img alt="liste_hote_service_controle" src="images/liste_hote_service_controle.png" width=75 border="1"> <br> <br>
						
					</fieldset>
					<h4>Liste des hôtes</h4>
					<fieldset id="liste_hote">
						<p>Vous aurez remarqué que le nom qui apparait dans CENTREON est différent du nom dans l'interface et de celui des équipements.</p>
						<p>Le nom des hôtes dans CENTREON est composé de la manière suivante: [Localisation]-[Type]-[Nom_equipement]</p>
						<p>Il y a deux raisons à cela; la première c'est que nous souhaitions pouvoir identifier rapidement où se situait l'hôte supervisé (dans quel centre de production par exemple; la règle de nommage n'étant pas encore généralisée) et quel équipement était concerné (serveur, firewall, routeur, etc...).<br>
						 La seconde pour éviter les doublons de nom car la règle de nommage des hôtes n'existait pas encore lorsque les premiers serveurs ont été mis en production. Il n'est pas rare encore de trouver des serveurs nommés "SERVEUR" dans différents centres de prod.</p>
						<p>La règle de nommage TT fait donc un peu doublon mais tant qu'elle n'aura pas été appliquée à l'ensemble des hôtes en production la règle spécifique à CENTREON perdurera.</p>
						<img alt="liste_hote" src="images/liste_hote.png" width=400 border="1"> <br>
						<p>On retrouve ici les informations principales d'identification de l'hôte: son nom, sa description, son IP et s'il est actif ou inactif.</p>
						<p><b>Dans cette liste vous ne devez sélectionner que les hôtes dont vous souhaitez modifier les caractéristiques propres (IP, description, fonction, etc...)</b>. Si vous souhaitez simplement ajouter un nouveau service, il est inutile de sélectionner l'hôte dans cette liste, il sera d'office disponible pour le paramétrage tout à l'heure.</p>
						<p>Dans le cas d'ajout simple de service, <b>vous devez juste vous assurer que l'hôte apparait bien dans cette liste</b>. Si ce n'est pas le cas, utilisez la fonction du menu "Rechercher des hôtes" pour l'importer afin que ses éléments soient référencés dans les différentes listes.</p>
						
					</fieldset>
					<h4>Liste des plages horaires</h4>
					<fieldset id="liste_plage">
						<p>On retrouve ici le détail des périodes temporelles: son nom, et pour chaque jour de la semaine la plage paramétrée.</p>
						<p><b>Dans cette liste vous ne devez sélectionner que les plages dont vous souhaitez modifier l'étendue</b>.<br>
						 Il vous sera possible d'ajouter de nouvelles plages au niveau du paramétrage. Attention toutefois à l'impact potentiel d'une modification de plage horaire pour les autres services qui l'utiliserai (par ex, tous les disques utilisent la même période 24/7).</p>
						<p>Il n'existe pas de notion d'actif ou inactif pour les périodes temporelles; elles sont utilisées ou ne le sont pas.</p>
						<p>Leur utilisation est exclusivement réservée aux services.</p>
						<img alt="liste_plage" src="images/liste_plage.png" width=800 border="1"> <br>
						<p>Prochainement, sera indiqué le nombre de service utilisant chaque période afin de faciliter la décision d'une modification de période ou d'une création.</p>
					</fieldset>
					<h4>Rechercher des hôtes</h4>
					<fieldset id="recherche_hote">
						<img alt="recherche_hote" src="images/recherche_hote.png" width=800 border="1"> <br>
						<p>Cette fonction vous permet de rechercher un hôte déjà en supervision mais pour lequel aucun service n'est rattaché à la prestation sélectionnée.</p>
						<p>Si un hôte est configuré dans CENTREON, il doit au moins avoir un service rattaché. Ce service doit avoir au moins une prestation rattachée.<br>
						 La recherche peut être effectuée à partir du nom ou de l'IP de l'hôte en saisissant tout ou partie d'une de ces deux informations.</p>
						<p>Ainsi vous pouvez rechercher tous les hôtes dont l'IP contient "10.33.12", inutile de rajouter une étoile (*) puis cliquez simplement sur "Rechercher".</p>
						<p>Une liste de ce type apparaitra, vous verrez au premier coup d'oeil les hôtes actifs et inactifs.</p>
						<img alt="recherche_hote_liste" src="images/recherche_hote_liste.png" width=700 border="1"> <br>
						<p>Il ne vous reste plus qu'à sélectionner le ou les hôtes que vous souhaitez ajouter à votre liste de sélection puis cliquer sur le bouton éponyme.<br>
						 Un message de confirmation s'affichera avant l'ajout effectif.</p>
						<p>Rebalayez alors les différentes listes pour faire votre sélection si nécessaire.</p>
					</fieldset>
					<p>Une fois votre sélection effectuée sur les différentes listes,cliquez sur le bouton "Valider votre sélection" tout en bas de la page. Patientez quelques instants, un message de confirmation de chargement vous invitera à passer à l'onglet suivant le paramétrage.</p>
					<img alt="confirmation_selection" src="images/confirmation_selection.png" width=600 border="1"> <br>
					<p>Comme indiqué plus haut si vous n'avez que des éléments à ajouter, cliquez simplement sur le bouton "Valider votre sélection"; les listes des hôtes et des périodes seront tout de même enregistrées afin que vous puissiez en sélectionner les éléments dans la configuration des services.</p>
					<p>Vous pourrez à tout moment dans votre demande revenir sur cet onglet pour rajouter de nouveaux éléments. Il est toutefois préférable de bien faire votre sélection dès le départ afin de ne pas perdre trop de temps avec de multiples aller/retour.</p>
				</fieldset>
				</div>
				<h3>onglet Paramétrage</h3>
				<div id="param">
				<fieldset id="f_param">
					<p>Selon la sélection que vous aurez faite précédemment, cet onglet sera plus ou moins chargé.<br>
					 Pour les besoins de la documentation, un hôte, une période et quelques services ont été sélectionnés.</p>
					<p>Voici une vue du contenu de l'onglet Paramétrage avec tous les menus repliés.</p>
					<img alt="onglet_parametrage" src="images/onglet_parametrage.png" width=800 border="1"> <br> <br>
					<p>Vous retrouvez la notion de plage horaire, d'hôtes et de services.<br>
					 Afin de vous guider au maximum dans le déroulement de la demande, les menus ont été placés dans cet ordre précis. Pour paramétrer correctement un service, il faut au préalable avoir une période et un hôte.</p>
					<p>Si les plages horaires listées précédemment vous conviennent, inutile d'en créer de nouvelles. Idem pour les hôtes.</p><br>
					<p> Vous pouvez à tout moment revenir sur l'onglet "Liste hôtes et services" afin d'ajouter un hôte ou un service que vous auriez oublié de sélectionner auparavant.<br>Dans ce cas <b>un brouillon est enregistré automatiquement</b> lorsque vous cliquez sur l'onglet.</p>
					<p>Passons maintenant au détail de chaque menu</p>
					<h4>Paramétrage des plages horaires</h4>
					<fieldset>
						<img alt="param_plage" src="images/param_plage.png" width=800 border="1"> <br> <br>
						<p>Les champs nom  et jours de la semaine ne méritent pas d'explication particulière ni même le champ commentaire qui comme vous pouvez le voir n'a pas de crayon, il est donc... facultatif! c'est bien vous avez suivi :).<br>
						 Comme vous pouvez le constater le nom de la plage horaire n'est pas modifiable, il a le cadenas. Les jours de la semaine non plus sauf qu'ils ont la coche verte. Il y a donc possibilité de les modifier.<br>
						 Pour cela, il faut double cliquer sur la coche verte pour déverrouiller le champ. Cette contrainte a été mise en place pour éviter des modifications malencontreuse des informations. Vous n'en voyez peut-être pas l'utilité maintenant mais vous comprendrez plus tard.</p>
						<img alt="param_plage_deverrouille" src="images/param_plage_deverrouille.png" width=400 border="1"> <br>
						<p>Le crayon est désormais affiché vous pouvez modifier l'information.</p>
						<p>Passons maintenant aux actions disponibles en bas de ce formulaire.</p>
						<img alt="param_plage_action" src="images/param_plage_action.png" width="700" border="1">
						<p>La liste déroulante pour les plages horaires ne vous propose qu'un seul choix: Modifier. En effet comme indiqué plus haut, il n'y a pas de notion d'activation ou de désactivation. Différents choix vous seront proposés pour les hôtes et services.</p>
						<p>Le bouton "Dupliquer" vous permet d'ajouter une nouvelle plage horaire en conservant les informations déjà renseignée; il vous suffira donc simplement de modifier le nom et les heures de cette nouvelle période pour qu'elle corresponde à votre besoin.</p>
						<p>Le bouton "Retirer de la demande" vous permet simplement d'enlever cette période de la demande; utile si vous l'avez sélectionnée par erreur précédemment.</p>
						<p>Le bouton "Ajouter une Plage horaire", vous permet de créer une nouvelle période; des valeurs par défaut vous sont proposées, adaptez les selon votre besoin.</p>
						<p> ce qui donne ceci:<br>
						<img alt="param_plage_ajouter_nouvelle" src="images/param_plage_ajouter_nouvelle.png" width="700" border="1"> <br>
						 Vous remarquerez que le contenu de la liste déroulante a changé et qu'un nouveau bouton "Pré-enregistrer cette plage" est apparu.<br>
						 Ce bouton est très important pour la suite du paramétrage. En effet, il permet de rendre disponible cette nouvelle période dans le paramétrage des services.</p>
						<p>Modifiez cette nouvelle plage à votre convenance puis cliquez sur ce bouton. Attention une fois la période pré-enregistrée, son nom ne sera plus modifiable.<br>
						 Une fois pré-enregistrée, le bouton est grisé.</p>
					</fieldset>
					<h4>Paramétrage des hôtes</h4>
					<fieldset>
						<p>Le paramétrage des hôtes se présente sous cette forme.
						<img alt="param_hote" src="images/param_hote.png" width=800 border="1"> <br>
						 Vous y retrouverez les caractéristiques principales nécessaires à leur mise en supervision:
						 Nom, IP, description brève, localisation, type, système d'exploitation, architecture, ainsi que la langue du système sont des informations obligatoires.<br>
						 il vous sera également demandé quelques informations facultatives mais toutefois utiles comme la fonction principale ainsi que d'éventuelles consignes à appliquer en cas d'évènement remonté par CENTREON.</p>
						<p>Un champ commentaire est à votre disposition pour y indiquer toute informations complémentaire que vous jugez nécessaire pour la mise en supervision (mode de connexion, user/mdp, etc...).</p>
						<p>La liste des actions à effectuer diffère des périodes<br>
						<img alt="param_hote_action" src="images/param_hote_action.png" width=100 border="1"> <br>
						Selon la valeur du champ Contrôle de l'hôte, la valeur "A Désactiver" sera remplacée par "A Activer" et inversement.</p>
						
						<p>La différence entre Désactiver et Supprimer tient dans le fait qu'un équipement désactivé pourra être réactivé rapidement sans paramétrage supplémentaire alors qu'un équipement supprimé devra faire l'objet d'un reparamétrage complet.<br>
						La désactivation est à privilégier si l'équipement ne doit plus être supervisé pour une période assez longue et s'il a vocation à être supervisé de nouveau.<br>
						Si un serveur doit être reconfiguré (changement de l'OS par ex.) il convient de demander la désactivation du premier et la création du nouveau; le paramétrage des sondes étant différents selon les OS.<br>
						Une fois la supervision validée sur le nouvel hôte, l'ancien sera supprimé.</p>
						<br>
						
						<p>Vous retrouverez les mêmes boutons que pour les périodes à savoir: Dupliquer, Retirer de la demande, Pré-enregistrer cet hôte si vous en avez ajouté ou dupliqué un.</p>
						<p>Le principe est le même que pour les périodes pour les champs obligatoires de ce formulaire. Les champs avec la coche verte sont deverrouillables en double-cliquant dessus.<br>
						 Le champ description bien que déjà renseigné reste avec le crayon pour vous obliger à vérifier les informations de l'hôte avant la validation finale.<br>
						 Certaines listes déroulantes sont vides car ces catégories sont apparues après les premières mises en supervision et seront complétées au fur et à mesure.
						 L'objectif de ces catégories est de pouvoir par la suite éditer différents rapports de disponibilité ou de maintenabilité en fonctions des caractéristiques de chaque hôte.<br>
						 Ainsi il sera par exemple possible d'identifier les configurations les plus génératrices d'incidents ou au contraire celles qui sont les plus fiables pour ensuite faire évoluer les plateformes pour en améliorer les la maintenabilité et la disponibilité.</p>
						<p>Ces rapports seront édités avec les modules CENTREON BAM et CENTREON BI récemment acquis par TESSI Technologies.</p> 
						<p>Le formulaire vierge d'un nouvel hôte se présente sous cette forme<br>
						<img alt="param_hote_ajout" src="images/param_hote_ajout.png" width=800 border="1"> <br>
						Vous retrouverez également le bouton "Pré-enregistrer cet hôte" comme indiqué ci-dessus</p>
					</fieldset>
					<h4>Paramétrage des services</h4>
					<fieldset>
						<p>Le paramétrage des services se présente sous cette forme.
						<img alt="param_service" src="images/param_service.png" width=800 border="1"> <br>
						 Vous y retrouverez les caractéristiques principales nécessaires à leur mise en supervision:
						 Nom (à ne pas confondre avec les services windows), hôte rattaché, période de contrôle, le modèle, fréquence et les arguments liés au modèle.<br>
						 il vous sera également demandé d'éventuelles consignes à appliquer en cas d'évènement remonté par CENTREON.</p>
						<p>Selon le modèle de service choisi, la description et le nombre d'arguments à fournir changera.<br>
						 Pour chaque argument, un exemple de ce qu'il faut saisir est affiché mais le champ reste vide tant que vous n'y avez rien saisi.<br>
						 Certains arguments sont optionnels, saisissez alors "NC" si l'option ne vous intéresse pas.</p>
						<p>Un champ commentaire est à votre disposition pour y indiquer toute informations complémentaire que vous jugez nécessaire pour la mise en supervision ou indications que vous n'auriez pas su transcrire au travers d'un modèle.</p>
						<p>Pour une modification d'un service, il vous sera systématiquement demandé de valider les arguments du services même si c'est la période de contrôle que vous souhaitez modifier.</p>
						<p>Vous retrouverez les mêmes actions et bouton que pour les hôtes à l'exception du bouton "Pré-enregistrer" qui n'a aucune utilité sur un service.</p>
						<p>Veuillez remarquez que la période et l'hôte pré-enregistré précédemment sont disponible dans les listes déroulantes respectives du service.
						<img alt="param_service_plage" src="images/param_service_plage.png" width=400 border="1"> <img alt="param_service_hote" src="images/param_service_hote.png" width=300 border="1"> <br>
						<p>Le formulaire vierge d'un service se présente sous cette forme<br>
						<img alt="param_service_ajout" src="images/param_service_ajout.png" width=800 border="1"> <br></p>
					</fieldset>
				</fieldset>
				</div>
				<h3>Brouillon et validation</h3>
                <div id="brouillon">
				<fieldset id="f_brouillon_validation">
					<p>Une fois l'ensemble des périodes, hôtes et services renseignés selon les besoins de la supervision à mettre en place, il ne vous reste plus qu'à cliquer sur "Valider votre demande".</p>
					<img alt="param_enregistrement.png" src="images/param_enregistrement.png" width=400 border="1"> <br>
					<p>Vous avez toutefois la possibilité d'enregistrer un brouillon de votre demande afin d'y revenir ultérieurement pour la compléter ou la terminer.<br>
					  Tant que la demande sera en statut "Brouillon" elles restera modifiable par son rédacteur et ne sera pas prise en compte par l'équipe Centreon.</p>
					<p>Une fois validée vous ne pourrez plus la modifier, un message de confirmation sera donc affiché afin de confirmer cette action.<br>
					 La demande sera alors publiée dans les "demandes à traiter" et un mail sera envoyé à SUSI pour enregistrement et notification des personnes concernées.</p>
				</fieldset>
				</div>
			</div>
		</div>
		<div id="tabs_Doc-3">
		<h2>Reprise d'un brouillon</h2>
			<div id="accordionDoc_reprise">
				<h3>Lister les demandes</h3>
                <div id="lister_dem">
                <fieldset id="f_lister_demande">
                	<p>Nous avons vu précédemment comment enregistrer un brouillon afin de pouvoir revenir travailler dessus le moment venu.</p>
                	<p> Il n'y a rien de plus simple. Il vous suffit de vous rendre sur la "liste des demandes en cours" dans le menu "Lister des demandes".<br>
                	 Une liste de ce type apparait:<br>
                	 <img alt="lister_demande.png" src="images/lister_demande.png" width=800 border="1"> <br>
                	 Recherchez votre demande et cliquez simplement sur le lien "Brouillon" à droite de la liste.
                	 Afin de vous assurer qu'il s'agit bien de votre demande, vous pouvez en afficher le contenu en cliquant sur le bouton "Afficher/Masquer" à gauche.<br>
                	 Le lien "Brouillon" ne sera accessible que si vous êtes l'auteur de la demande comme vous pouvez le constater sur la copie d'écran avec la demande de "ddurand" pour laquelle le lien n'est pas actif.</p>
                	<p>Le fait de cliquer sur le lien "Brouillon" rechargera votre demande avec tout son contenu comme si vous êtiez en train de la rédiger via le menu "Formuler une demande".</p>
                </fieldset>
				</div>
				<h3>Vérifications du contenu</h3>
                <div id="verif">
					<p>Après quelques instants, votre demande s'affiche à l'écran sur l'onglet "Informations générales".<br>
					<img alt="reprise_brouillon.png" src="images/reprise_brouillon.png" width=800 border="1"> <br>
					Vous remarquerez l'entête de la page avec le terme "Reprise".<br>
					Tous les champs précédemment saisi sont renseignés et certains sont verrouillés comme la prestation. La date de supervision peut être modifiée si besoin.<br>
					Sur l'onglet "Liste hôtes et services" vous retrouverez l'ensemble des hôtes et services comme lors de la rédaction initiale à la différence près que les éléments événtuellement sélectionnés sont grisés pour éviter de les réimporter.<br>
					<img alt="reprise_brouillon_liste.png" src="images/reprise_brouillon_liste.png" width=800 border="1"> <br>
					Un brouillon est automatiquement enregistré lorsque vous cliquez sur cet onglet. cela vous permet de passer de l'onglet paramétrage à la liste en étant sûr de ne rien perdre comme information.<br>
					Enfin sur l'onglet paramétrage, vous devrez valider chaque champ ayant un crayon avant de pouvoir valider définitivement votre demande.<br>
					<img alt="reprise_brouillon_param.png" src="images/reprise_brouillon_param.png" width=800 border="1"> <br>
                </div>
				<h3>Validation</h3>
                <div id="validation">
					<p>Lorsque vous avez ajouter et paramétré tous les éléments, vous devez valider définitivement votre demande en cliquant sur le bouton éponyme.<br>
					 Si certains champs ne sont pas valides, un message comme celui-ci vous l'indiquera:<br>
					 <img alt="brouillon_erreur.png" src="images/brouillon_erreur.png" width=400 border="1"> <br>
					 Dans le cas présent les champs suivants n'ont pas été validés:<br>
					 - Hôte n°1, champ description<br>
					 <img alt="brouillon_hote1.png" src="images/brouillon_hote1.png" width=500 border="1"> <br>
					 - Service n°1, champ Argument 1<br>
					 <img alt="brouillon_service1.png" src="images/brouillon_service1.png" width=500 border="1"> <br>
					 - Service n°7, champ Argument 2<br>
					 <img alt="brouillon_service2.png" src="images/brouillon_service2.png" width=500 border="1"> <br>
					Corrigez les champs indiquez puis valider votre demande.</p>
                </div>
			</div>
		</div>
		<div id="tabs_Doc-4">
		<h2>Consultation d'une demande</h2>
			<div id="accordionDoc_liste">
				<h3>Lister les demandes</h3>
				<div id="liste_demande">
					<p>Vous avez la possibilité de consulter l'ensemble des demandes quelque soit le rédacteur, qu'elles soient en cours de rédaction, à traiter, en cours de traitement, traitées ou annulées.<br>
					 Comme vu dans les chapitres précédant vous ne pourrez éditer que vos propres demandes mais vous pouvez tout à fait consulter les autres.<br>
					 Cela vous permet de voir l'avancement des différentes demandes mais également de ne pas refaire une demande identique ou bien d'en faire une nouvelle afin de la compléter si elle a déjà été validée.</p>
					<p>Pour consulter le contenu d'une demande, il vous suffit d'afficher la liste souhaitée ("en cours" ou "traitées") puis de cliquer sur le bouton "Afficher/Masquer".<br>
					 L'ensemble de la demande sera alors affichée avec l'intégralité des informations la concernant<br>
					 <img alt="liste_demande_afficher.png" src="images/liste_demande_afficher.png" width=800 border="1"> <br>
					 Cliquer à nouveau sur le bouton "Afficher/Masquer" pour... la masquer!</p>
				</div>
			</div>
		</div>
		<div id="tabs_Doc-5">
		<h2>Trucs et astuces</h2>
			<div id="accordionDoc_truc">
				<h3>Afficher l'aide</h3>
				<div id="afficher_aide">
					<p>La plupart des informations nécessaires à la rédaction ou la consultation des demandes se trouve sur ces 5 onglets du menu docmentation.<br>
					 Vous trouverez néanmoins sur les différents formulaire cet icône <img alt="point-interrogation-16" src="images/point-interrogation-16.png"> qui vous permettra d'afficher quelques informations complémentaires concernant le champ auquel il est rattaché.<br>
					 Si toutefois il vous manque des informations quelles qu'elles soient, n'hésitez pas à me contacter:<br>
					 par tél: 05.57.22.77.13<br>
					 par mail: centreon_tt@tessi.fr</p>
				</div>
				<h3>Choisir un modèle de service</h3>
				<div id="choisir_modele">
					<p>Le choix d'un modèle de service peut paraître difficile la liste étant assez longue.<br>
					 Les modèles sont regroupés par thème:<br>
					  - <b>Windows</b>: concerne les sondes spécifiques aux environnements Windows<br>
					  - <b>Linux</b>: concerne les sondes spécifiques aux environnements... euh... Linux et dérivés. Tiens comme c'est bizarre!<br>
					  - <b>Système</b>: les sondes relatives aux contrôles systèmes quelque soit l'environnement.<br>
					  - <b>Web</b>: tout ce qui concerne les sites web et leurs dérivés<br>
					  - <b>BOS</b>: tout les contrôles spécifiques à ... Bos (Manager, Video, Document, etc...)<br>
					  - et ainsi de suite...<br>
					  Le nom du modèle essai d'être une brève description du contrôle; toutefois cela n'est pas suffisant pour bien expliquer ce qu'il fait.<br>
					   Vous trouverez donc une description plus détaillée du contrôle en le sélectionnant. Cela fera apparaitre sa description complète ainsi que la liste des arguments attendus avec des exemples.<br>
					   Voici quelques exemples ci-dessous:<br>
					   <img alt="service_modele_1.png" src="images/service_modele_1.png" width=600 border="1"> <br>
					   <img alt="service_modele_2.png" src="images/service_modele_2.png" width=600 border="1"> <br>
					   <img alt="service_modele_3.png" src="images/service_modele_3.png" width=600 border="1"> <br>
					   Cette liste évolue sans cesse; les noms et descriptions peuvent changer sans préavis selon les besoins du paramétrage.</p>
				</div>
			</div>
			<p>D'autres astuces viendront compléter cette liste, n'hésitez donc pas à y revenir de temps en temps.</p>
		</div>
	</div>
	</section>
	<footer>
		<?php
			include('PiedDePage.php');
			echo '<a href="http://www.phptherightway.com">
    				<img src="http://www.phptherightway.com/images/banners/btn1-120x90.png" alt="PHP: The Right Way"/><br/>PHP: The Right Way
				</a>';
		?>
	</footer>
</div>
	<!-- section des script javascript -->
<?php
	include('section_script_JS.php');
?>	
</body>
</html>
