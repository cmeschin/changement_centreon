<?php
if (session_id()=='')
{
session_start();
};
//try {
	include_once('connexion_sql_supervision.php');
	//$bdd_supervision->beginTransaction();
	addlog("Chargement envoi mail");
	$mail = 'spoc_susi@tessi.fr'; // Déclaration de l'adresse de destination.
	if (!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $mail)) // On filtre les serveurs qui rencontrent des bogues.
	{
		$passage_ligne = "\r\n";
	}
	else
	{
		$passage_ligne = "\n";
	}
	
	// Récupération des informations pour la constitution du mail
	$req_mail = $bdd_supervision->prepare(
	'SELECT D.ID_Demande as id_demande,
			 D.Ref_Demande as ref_demande,
			 D.Date_Demande as date_demande,
			 D.Demandeur as demandeur,
			 D.Date_Supervision_Demandee as date_supervision_demandee,
			 Code_Client as prestation,
			 (SELECT count(ID_Demande) FROM hote AS H WHERE H.Type_Action NOT IN ("NC") AND H.ID_Demande=D.ID_Demande) AS nb_hote,
			 (SELECT count(ID_Demande) FROM service AS S WHERE S.ID_Demande=D.ID_Demande) AS nb_service,
			 (SELECT count(ID_Demande) FROM periode_temporelle AS P WHERE P.Type_Action IN ("Modifier","Creer") AND P.ID_Demande=D.ID_Demande) AS nb_plage,
			 D.Etat_Demande as etat_demande,
			 D.Commentaire as commentaire,
			 D.email as email,
			 D.ticket_susi as ticket_susi 
			FROM demande AS D 
			WHERE D.Etat_Demande = :Etat_Demande
			 AND D.ID_Demande = :ID_Demande 
			GROUP BY D.ID_Demande 
			ORDER BY D.Date_Demande, D.Date_Supervision_Demandee');
	$req_mail->execute(array(
		'Etat_Demande' => 'A Traiter',
		'ID_Demande' => htmlspecialchars($ID_Demande)
	)) or die(print_r($req_mail->errorInfo()));
	
	//$result_mail = $req_mail->fetchAll();
	//$liste_mail = split(",",$mail);$nombre=sizeof($table2)
	foreach ($req_mail as $res_mail)
	{
		//initialisation mail
		$adresse_mail = "";
		$adresse_mail .= " " . htmlspecialchars($res_mail['email']);
		$adresse_mail = str_replace(";", ", ", $adresse_mail); // converti les ; en , et ajoute un espace
		$adresse_mail = str_replace(",", ", ", $adresse_mail); // ajoute un espace après les virgules
	    $adresse_mail = str_replace(",", " ", $adresse_mail); // converti la virgule en espace
		$adresse_mail = str_replace("  ", " ", $adresse_mail); // supprime les espaces en double.
	
		// récupération num ticket pour création ou mise à jour
		$num_ticket = htmlspecialchars($res_mail['ticket_susi']);
		if ( $num_ticket == NULL)
		{
			$issue = "";
		} else 
		{
			$issue = "ISSUE=" . $num_ticket;
		};
	//addlog("liste mail=" . htmlspecialchars($res_mail['email']));
		//=====Définition du sujet.
		$sujet = "CENTREON: Demande de changement ref: " . htmlspecialchars($res_mail['ref_demande']) . " - " . htmlspecialchars($res_mail['prestation']) . " PROJ=1 " . $issue . "";
		addlog("Sujet=" . $sujet);
		//=========
		 
	
		//=====Déclaration des messages au format texte et au format HTML.
		$lien_html = "http://intra01.tessi-techno.fr/changement_centreon/lister_demande.php?id_dem=" . $res_mail['id_demande'] . ""; 
//		$message_txt = "Type DEM = Autre\nTitulaires = Changement__bCentreon\nPrestation = INFRA_TT_{INT}\nClient Bénéficiaire = Tessi Technologies\nService = TESSI-TECHNO DSI\nRéférentiel SLA = Convention STD\nContrat = -\nTypeService = CENTREON\nListe de diffusion = " . htmlspecialchars($adresse_mail) . "\n\nRéférence Demande: " . htmlspecialchars($res_mail['ref_demande']) . "\nLien vers le gestionnaire des changements Centreon: " . htmlspecialchars($lien_html) . "\nDate de la demande: " . htmlspecialchars($res_mail['date_demande']) . "\nDemandeur: " . htmlspecialchars($res_mail['demandeur']) . "\nDate activation souhaitée: " . htmlspecialchars($res_mail['date_supervision_demandee']) . "\nPrestation concernée: " . htmlspecialchars($res_mail['prestation']) . "\nParamétrage à effectuer:\n    - " . htmlspecialchars($res_mail['nb_hote']) . " hôte(s)\n      - " . htmlspecialchars($res_mail['nb_service']) . " service(s)\n   - " . htmlspecialchars($res_mail['nb_plage']) . " plage(s) horaire(s)\nCommentaire: " . htmlspecialchars($res_mail['commentaire']) . "";
		$message_txt = "Type DEM = Autre\nRéférence Ticket Client = " . htmlspecialchars($res_mail['ref_demande']) . "\nUrgence = Faible\nImpact = Aucun\nTitulaires = Changement__bCentreon\nPrestation = INFRA_TT_{INT}\nClient Bénéficiaire = Tessi Technologies\nService = TESSI-TECHNO DSI\nRéférentiel SLA = Convention STD\nContrat = -\nTypeService = CENTREON\nListe de diffusion = " . htmlspecialchars($adresse_mail) . "\n\nRéférence Demande: " . htmlspecialchars($res_mail['ref_demande']) . "\nLien vers le gestionnaire des changements Centreon: " . htmlspecialchars($lien_html) . "\nDate de la demande: " . htmlspecialchars($res_mail['date_demande']) . "\nDemandeur: " . htmlspecialchars($res_mail['demandeur']) . "\nDate activation souhaitée: " . htmlspecialchars($res_mail['date_supervision_demandee']) . "\nPrestation concernée: " . htmlspecialchars($res_mail['prestation']) . "\nParamétrage à effectuer:\n    - " . htmlspecialchars($res_mail['nb_hote']) . " hôte(s)\n      - " . htmlspecialchars($res_mail['nb_service']) . " service(s)\n   - " . htmlspecialchars($res_mail['nb_plage']) . " plage(s) horaire(s)\nCommentaire: " . htmlspecialchars($res_mail['commentaire']) . "";
		
		//==========
	}; 
	//=====Création de la boundary
	$boundary = "-----=".md5(rand());
	//==========
	 
	//=====Création du header de l'e-mail.
	$header = "From: \"changement_centreon\"<centreon_tt@tessi.fr>".$passage_ligne;
	$header.= "Reply-to: \"PasDeReponse\" <PasDeReponse@tessi.fr>".$passage_ligne;
	$header.= "MIME-Version: 1.0".$passage_ligne;
	$header .= "X-Priority: 3".$passage_ligne;
	$header.= "Content-Type: multipart/alternative;".$passage_ligne." boundary=\"$boundary\"".$passage_ligne; // envoie du format text et HTML
	//==========
	 
	//=====Création du message.
	$message = $passage_ligne."--".$boundary.$passage_ligne;
	//=====Ajout du message au format texte.
	//$message.= "Content-Type: text/plain; charset=\"ISO-8859-1\"".$passage_ligne;
	$message.= "Content-Type: text/plain; charset=\"UTF-8\"".$passage_ligne;
	$message.= "Content-Transfer-Encoding: 8bit".$passage_ligne;
	$message.= $passage_ligne.$message_txt.$passage_ligne;
	//==========
	$message.= $passage_ligne."--".$boundary.$passage_ligne;
	//=====Ajout du message au format HTML
	//$message.= "Content-Type: text/html; charset=\"ISO-8859-1\"".$passage_ligne;
	//$message.= "Content-Type: text/html; charset=\"UTF-8\"".$passage_ligne;
	$message.= "Content-Transfer-Encoding: 8bit".$passage_ligne;
	//$message.= $passage_ligne.$message_html.$passage_ligne;
	//==========
	$message.= $passage_ligne."--".$boundary."--".$passage_ligne;
	$message.= $passage_ligne."--".$boundary."--".$passage_ligne;
	//==========
	addlog("message constitué"); 
	//=====Envoi de l'e-mail.
	mail($mail,$sujet,$message,$header);
	//mail("c.zic@free.fr c.meschin@free.fr",$sujet,$message,$header);
	addlog("mail envoyé");
	//==========
	// flag envoi mail
	$maj_demande = $bdd_supervision->prepare('UPDATE demande
			 SET mail_creation=1, mail_encours=0, mail_finalise=0, mail_traite=0, mail_annule=0
			 WHERE ID_Demande= :ID_Demande AND Etat_Demande= :Etat_Demande');
	$maj_demande->execute(Array(
		'Etat_Demande' => 'A Traiter',
		'ID_Demande' => htmlspecialchars($ID_Demande)
	)) or die(print_r($maj_demande->errorInfo()));
	//$bdd_supervision->commit();
//} catch (Exception $e) {
//	$bdd_supervision->rollBack();
//	die('Erreur traitement envoi_mail: '. $e->getMessage());
//};
