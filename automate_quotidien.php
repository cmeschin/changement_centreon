<?php
//session_start();
/**
 * script d'envoi des demandes à traiter à J et J+1
 */

/**
 * Initialisation des constantes
 * Date du jour => pour le stockage de l'heure d'envoi du mail
 * Jour de la semaine => pour la vérification sur la calendrier => De 1 (pour Lundi) à 7 (pour Dimanche)
 * Heure actuelle => pour la vérification sur l'heure de notification
 */
$debug=true; // activation du mode debug
//initialisation mail
$adresse_mail = "centreon_tt@tessi.fr";
//$adresse_mail = "cedric.meschin@tessi.fr";
$adresse_mail = str_replace(";", ",", $adresse_mail); // converti les ; en , et ajoute un espace
if (!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $adresse_mail)) // On filtre les serveurs qui rencontrent des bogues.
{
	$passage_ligne = "\r\n";
}
else
{
	$passage_ligne = "\n";
};

try {
	include_once('connexion_sql_supervision.php'); // connexion à la base changement
	$bdd_supervision->beginTransaction();
	$heure_envoi = date("d/m/Y H:i");
	$jour_semaine = date("N");
	$jour1=$jour_semaine+1;

	// Dates SQL
	$dateJ = date("Y-m-d");
 	$dateP1J = date("Y-m-d", strtotime("+1 day"));
	
	// Date Mail
	$date_mailJ = date("d/m");
	$date_mailP1J=date("d/m", strtotime("+1 day"));

	if ($debug==true)
	{
		echo "dateJ=".$dateJ."\n";
 		echo "dateP1J=".$dateP1J."\n";
	};
	
		
/**
 * Récupération de la liste des demandes en cours
 */

	$req_lst_J = $bdd_supervision->prepare('
		SELECT
			 Demandeur,
			 Date_Demande,
			 Date_Supervision_Demandee,
			 Code_Client AS Prestation,
			 Etat_Demande,
			 Type_Demande,
			 Ref_Demande,
			 ID_Demande			 
		 FROM demande
		 WHERE Etat_Demande IN ("A Traiter","En cours","Validation") AND Date_Supervision_Demandee = "' . $dateJ . '"
		 ORDER BY Date_Supervision_Demandee, Code_Client;');
	$req_lst_J->execute(array()) or die(print_r($req_lst_J->errorInfo()));

	$req_lst_J1 = $bdd_supervision->prepare('
		SELECT
			 Demandeur,
			 Date_Demande,
			 Date_Supervision_Demandee,
			 Code_Client AS Prestation,
			 Etat_Demande,
			 Type_Demande,
			 Ref_Demande,
			 ID_Demande
		 FROM demande
		 WHERE Etat_Demande IN ("A Traiter","En cours","Validation") AND Date_Supervision_Demandee = "' . $dateP1J . '"
		 ORDER BY Date_Supervision_Demandee, Code_Client;');
	$req_lst_J1->execute(array()) or die(print_r($req_lst_J1->errorInfo()));
	
	$req_Temps_J = $bdd_supervision->prepare('
		SELECT CONCAT(FLOOR(sum(temps_hote + temps_service)/60),"h",LPAD(sum(temps_hote + temps_service)%60,2,"00")) AS Temps_Global
				FROM demande
				WHERE Etat_Demande IN ("A Traiter","En cours","Validation") AND Date_Supervision_Demandee = "' . $dateJ . '";');
	$req_Temps_J->execute(array()) or die(print_r($req_Temps_J->errorInfo()));

	$req_Temps_J1 = $bdd_supervision->prepare('
		SELECT CONCAT(FLOOR(sum(temps_hote + temps_service)/60),"h",LPAD(sum(temps_hote + temps_service)%60,2,"00")) AS Temps_Global
				FROM demande
				WHERE Etat_Demande IN ("A Traiter","En cours","Validation") AND Date_Supervision_Demandee = "' . $dateP1J . '";');
	$req_Temps_J1->execute(array()) or die(print_r($req_Temps_J1->errorInfo()));
	
				
	$req_J = $bdd_supervision->prepare('
		SELECT
			 Etat_Demande,
			 count(Etat_demande) AS Nbre 
		FROM demande 
		WHERE Date_Supervision_Demandee= "' . $dateJ . '" AND Etat_Demande IN ("A Traiter","En cours","Validation")  
		GROUP BY Etat_Demande;');
	$req_J->execute(array()) or die(print_r($req_J->errorInfo()));

	$req_J1 = $bdd_supervision->prepare('
		SELECT
			 Etat_Demande,
			 count(Etat_demande) AS Nbre
		FROM demande 
		WHERE Date_Supervision_Demandee="' . $dateP1J . '" AND Etat_Demande IN ("A Traiter","En cours","Validation","Traité") 
		GROUP BY Etat_Demande;');
	$req_J1->execute(array()) or die(print_r($req_J1->errorInfo()));

	
	$contenu_html="";
	
/**
 * Initialisation de la boucle sur chaque demande
 */
    $traiteJ = 0;
    $traiteJ1 = 0;
	while ($res_J = $req_J->fetch())
	{
		$traiteJ=htmlspecialchars($res_J['Nbre']);
	};
	while ($res_J1 = $req_J1->fetch())
	{
		$traiteJ1=htmlspecialchars($res_J1['Nbre']);
	};
	
	While($res_Temps_J = $req_Temps_J->fetch())
	{
		$Temps_J = $res_Temps_J['Temps_Global'];
	};

	While($res_Temps_J1 = $req_Temps_J1->fetch())
	{
	    $Temps_J1 = $res_Temps_J1['Temps_Global'];
	};
	
	If ( $traiteJ <> 0)
	{
    	$contenu_html .= "<p class='P2'>Liste des demandes à traiter aujourd'hui (temps total de traitement estimé " . $Temps_J . ")</p> <br />";
    	$contenu_html .= "<table border='0' cellspacing='0' cellpadding='3'>
    		<tr>
    			<th class='Tableau1_A1'>Demandeur</th>
    			<th class='Tableau1_A1'>Type de demande</th>
    			<th class='Tableau1_A1'>Prestation</th>
    			<th class='Tableau1_A1'>Etat demande</th>
    			<th class='Tableau1_A1'>Date Supervision Demandée</th>
    			<th class='Tableau1_A1'>Référence de la demande</th>
    		</tr>";
    	
    	While($res_lst_J = $req_lst_J->fetch())
    	{
    		$contenu_html .= "
    			<tr>
    	 			<td class='Tableau1_A1'>" . $res_lst_J['Demandeur'] . "</td>
    	 			<td class='Tableau1_A1'>" . $res_lst_J['Type_Demande'] . "</td>
    	 			<td class='Tableau1_A1'>" . $res_lst_J['Prestation'] . "</td>
    	 			<td class='Tableau1_A1'>" . $res_lst_J['Etat_Demande'] . "</td>
    	 			<td class='Tableau1_A1'>" . $res_lst_J['Date_Supervision_Demandee'] . "</td>
    	 			<td class='Tableau1_A1'><a href='https://changement-centreon.interne.tessi-techno.fr/changement_centreon/lister_demande.php?id_dem=" . $res_lst_J['ID_Demande'] . "'>" . $res_lst_J['Ref_Demande'] . "</a></td>
    	 		</tr>";
    	};
    	$contenu_html .= "</table><br />";
	} else {
	    $contenu_html .= "<p class='P2'>Aucune demande à traiter pour aujourd'hui</p> <br />";
	};
	
	If ( $traiteJ1 <> 0 ){
    	$contenu_html .= "<p class='P2'>Liste des demandes à traiter demain (temps total de traitement estimé " . $Temps_J1 . ")</p> <br />";
    	$contenu_html .= "<table border='0' cellspacing='0' cellpadding='3'>
    		<tr>
    			<th class='Tableau1_A1'>Demandeur</th>
    			<th class='Tableau1_A1'>Type de demande</th>
    			<th class='Tableau1_A1'>Prestation</th>
    			<th class='Tableau1_A1'>Etat demande</th>
    			<th class='Tableau1_A1'>Date Supervision Demandée</th>
    			<th class='Tableau1_A1'>Référence de la demande</th>
    		</tr>";
    	
    	While($res_lst_J1 = $req_lst_J1->fetch())
    	{
    	    $contenu_html .= "
    			<tr>
    	 			<td class='Tableau1_A1'>" . $res_lst_J1['Demandeur'] . "</td>
    	 			<td class='Tableau1_A1'>" . $res_lst_J1['Type_Demande'] . "</td>
    	 			<td class='Tableau1_A1'>" . $res_lst_J1['Prestation'] . "</td>
    	 			<td class='Tableau1_A1'>" . $res_lst_J1['Etat_Demande'] . "</td>
    	 			<td class='Tableau1_A1'>" . $res_lst_J1['Date_Supervision_Demandee'] . "</td>
    	 			<td class='Tableau1_A1'><a href='https://changement-centreon.interne.tessi-techno.fr/changement_centreon/lister_demande.php?id_dem=" . $res_lst_J1['ID_Demande'] . "'>" . $res_lst_J1['Ref_Demande'] . "</a></td>
    	 		</tr>";
    	};
    	$contenu_html .= "</table><br />";
	} else {
	    $contenu_html .= "<p class='P2'>Aucune demande à traiter pour demain</p> <br />";
	};
	
				/**
				 * Constitution du corps du mail
				 */
				//=====Définition de l'ogjet.
				$sujet = "[CENTREON-GCC] Liste quotidienne des demandes à traiter.";
				//=========
				//=====Déclaration des messages au format HTML.
				$message_html = "
					<!DOCTYPE html>
					<html>
						<style type=\"text/css\">
							@page {  }
							table { border-collapse:collapse; border-spacing:0; empty-cells:show; display:flex; justify-content: space-around; flex-border: none}
							td, th { vertical-align:top; text-align:center; font-size:11pt;}
							h1, h2, h3, h4, h5, h6 { clear:both }
							ol, ul { margin:0; padding:0;}
							li { list-style: none; margin:0; padding:0;}
							<!-- \"li span.odfLiEnd\" - IE 7 issue-->
							li span. { clear: both; line-height:0; width:0; height:0; margin:0; padding:0; }
							span.footnodeNumber { padding-right:1em; }
							span.annotation_style_by_filter { font-size:95%; font-family: Helvetica Neue, arial, sans-serif; background-color:#fff000;  margin:0; border:0; padding:0;  }
							* { margin:0;}
							.P1 { font-size:12pt; font-family: Helvetica Neue, arial, sans-serif; writing-mode:page; }
							.P2 { font-size:12pt; font-family: Helvetica Neue, arial, sans-serif; writing-mode:page; text-align:center; text-decoration:underline; font-weight:bold; }
							.P3 { font-size:8pt; font-family: Helvetica Neue, arial, sans-serif; writing-mode:page; background-color:transparent; }
							.P4 { font-size:8pt; font-family: Helvetica Neue, arial, sans-serif; writing-mode:page; color:#33cc66; background-color:transparent; }
							.P5 { font-size:14pt; margin-bottom:0.212cm; margin-top:0.423cm; font-family: Helvetica Neue, arial, sans-serif; writing-mode:page; text-align:center ! important; text-decoration:underline; font-weight:bold; }
							.P6 { font-size:14pt; margin-bottom:0.212cm; margin-top:0.423cm; font-family: Helvetica Neue, arial, sans-serif; writing-mode:page; text-align:center ! important; font-weight:bold; }
							<!-- ODF styles with no properties representable as CSS -->
							.T1 .T6  { }
							.Tableau1_A1 { border: 1px solid #000; }
						</style>
						</head>
						<body>
							<header>
								<p class='P6'>Liste des demandes de changement à traiter au " . $heure_envoi . ".</p>
							</header>
							<section>
								" . $contenu_html . "
								<br />
<!--								<p>Ce message est envoyé au(x) destinataire(s) suivant(s): " . str_replace(',',' ',$adresse_mail) . ".</p>
								<br />
 	-->
							</section>
							<footer>
 								<p class='P3'>Ce message est envoyé par un robot, merci de ne pas y répondre. Pour toute information complémentaire veuillez contacter cedric.meschin@tessi.fr</p>
 								<p class='P4'>Pensez à l'environnement, n'imprimer ce mail que si nécessaire.</p>
<!-- 								<p class='P3'>
 									<span class=\"T6\">Ce message est envoyé par un robot, merci de ne pas y répondre. Pour toute information complémentaire veuillez contacter cedric.meschin@tessi.fr</span>
 								</p>
 								<p class='P4'>
 									<span class=\"T6\">Pensez à l'environnement, n'imprimer ce mail que si nécessaire.</span>
 								</p> -->
							</footer>
						</body>
					</html>
				";
				//==========
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
 				$message= $passage_ligne."--".$boundary.$passage_ligne; // Ouverture Boundary HTML
				//=====Ajout du message au format HTML
				//$message.= "Content-Type: text/html; charset=\"ISO-8859-1\"".$passage_ligne;
				$message.= "Content-Type: text/html; charset=\"UTF-8\"".$passage_ligne;
				$message.= "Content-Transfer-Encoding: 8bit".$passage_ligne;
				$message.= $passage_ligne.$message_html.$passage_ligne;
				//==========
				$message.= $passage_ligne."--".$boundary."--".$passage_ligne; // Fermeture Boundary HTML
				//==========
				//=====Envoi de l'e-mail.
				mail($adresse_mail,$sujet,$message,$header);
				//mail("c.zic@free.fr",$sujet,$message,$header);
				//==========
 
	$bdd_supervision->commit();
} catch (Exception $e) {
 	$bdd_supervision->rollBack();
 	die('Erreur traitement envoi_mail: '. $e->getMessage());
};
