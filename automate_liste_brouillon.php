<?php
//session_start();
/**
 * Lancement
 */

/**
 * Initialisation des constantes
 * information à 15 jours
 * Suppression à 60 jours
 * Date du jour => pour le stockage de l'heure d'envoi du mail
 * Jour de la semaine => pour la vérification sur la calendrier => De 1 (pour Lundi) à 7 (pour Dimanche)
 * Heure actuelle => pour la vérification sur l'heure de notification
 */
	include_once('connexion_sql_supervision.php'); // connexion à la base changement
	$heure_envoi = date("Y-m-d H:i:s");
	$dateJ = date("Y-m-d");
		
/**
 * Récupération de la liste des demandeur dont au moins une demande est en brouillon
 */
$req_demandeur = $bdd_supervision->prepare('
	SELECT
		Distinct(Demandeur),
		SUBSTRING_INDEX(email,";",1) as email
	 FROM demande
	 WHERE Etat_Demande="Brouillon" AND Date_Demande < DATE_ADD(curdate(), INTERVAL -15 DAY)
	 ORDER BY demandeur;');
$req_demandeur->execute(array()) or die(print_r($req_demandeur->errorInfo()));

while ($res_demandeur=$req_demandeur->fetch())
{
	echo $res_demandeur['Demandeur'] . "\n";
	$req_lst = $bdd_supervision->prepare('
	SELECT
		Ref_Demande,
		Code_Client As Prestation,
		id_demande,
		Date_Supervision_Demandee,
		Date_Demande,
		Type_Demande
	 FROM demande
	 WHERE Etat_Demande="Brouillon" AND Demandeur="'. $res_demandeur['Demandeur'] .'" AND Date_Demande >= DATE_ADD(curdate(), INTERVAL -60 DAY) AND Date_Demande < DATE_ADD(curdate(), INTERVAL -15 DAY) 
	 ORDER BY demandeur;');
	$req_lst->execute(array()) or die(print_r($req_lst->errorInfo()));

	$res_lst = $req_lst->fetchAll ();
	
	$req_suppr = $bdd_supervision->prepare('
	SELECT
		Ref_Demande,
		Code_Client As Prestation,
		id_demande,
		Date_Supervision_Demandee,
		Date_Demande,
		Type_Demande
	 FROM demande
	 WHERE Etat_Demande="Brouillon" AND Demandeur="'. $res_demandeur['Demandeur'] .'" AND Date_Demande < DATE_ADD(curdate(), INTERVAL -60 DAY)
	 ORDER BY demandeur;');
	$req_suppr->execute(array()) or die(print_r($req_suppr->errorInfo()));
	
	$res_suppr = $req_suppr->fetchAll ();
	
	$contenu_html="";

	if (count($res_lst) >= 1)
	{
		$contenu_html .= "<p class='P5'>Liste des brouillons supérieurs à quinze jours</p><br />";
		$contenu_html .= "<table border='0' cellspacing='0' cellpadding='3'>
							<tr><th class='Tableau1_A1'>Type de demande</th>
								<th class='Tableau1_A1'>Prestation</th>
								<th class='Tableau1_A1'>Date de supervision souhaitée</th>
								<th class='Tableau1_A1'>Date de dernière modification</th>
								<th class='Tableau1_A1'>Référence de la demande</th>
							</tr>";
		//while($res_lst=$req_lst->fetch())
		foreach ( $res_lst as $elements )
		{
			echo $elements['Ref_Demande'] . "\n";
			$contenu_html .= "<tr>
	 				<td class='Tableau1_A1'>" . $elements['Type_Demande'] . "</td>
					<td class='Tableau1_A1'>" . $elements['Prestation'] . "</td>
	 				<td class='Tableau1_A1'>" . $elements['Date_Supervision_Demandee'] . "</td>
	 				<td class='Tableau1_A1'>" . $elements['Date_Demande'] . "</td>
	 				<td class='Tableau1_A1'><a href='https://changement-centreon.interne.tessi-techno.fr/changement_centreon/lister_demande.php?id_dem=" . $elements['id_demande'] . "'>" . $elements['Ref_Demande'] . "</a></td>
	 				</tr>";
		};
		$contenu_html .= "</table><br />";
	};
	/**
	 * Si des demandes sont à supprimer afficher le tableau.
	 */
	if (count($res_suppr) >= 1)
	{
		$contenu_html .= "<p class='P5'>Liste des brouillons supérieurs à deux mois et automatiquement supprimés</p><br />";
		$contenu_html .= "<table border='0' cellspacing='0' cellpadding='3'>
						<tr><th class='Tableau1_A1'>Type de demande</th>
							<th class='Tableau1_A1'>Prestation</th>
							<th class='Tableau1_A1'>Date de supervision souhaitée</th>
							<th class='Tableau1_A1'>Date de dernière modification</th>
							<th class='Tableau1_A1'>Référence de la demande</th>
						</tr>";
		//while($res_suppr=$req_suppr->fetch())
		foreach ( $res_suppr as $elements )
		{
			//echo $res_lst['Ref_Demande'] . "\n";
			$contenu_html .= "<tr>
 				<td class='Tableau1_A1'>" . $elements['Type_Demande'] . "</td>
				<td class='Tableau1_A1'>" . $elements['Prestation'] . "</td>
 				<td class='Tableau1_A1'>" . $elements['Date_Supervision_Demandee'] . "</td>
 				<td class='Tableau1_A1'>" . $elements['Date_Demande'] . "</td>
 				<td class='Tableau1_A1'>" . $elements['Ref_Demande'] . "</td>
 				</tr>";
			$id_demande=$elements['id_demande'];
			//echo $id_demande . "\n";
			if ( "$dateJ" >= "2016-10-03")
			{
				echo "Suppression ID_Demande=" . $id_demande . "\n";
				$req_del_time = $bdd_supervision->prepare('DELETE FROM periode_temporelle WHERE ID_Demande= :id_demande;');
				$req_del_time->execute(array(
						'id_demande' => $id_demande
				)) or die(print_r($req_del_time->errorInfo()));
				$req_del_service = $bdd_supervision->prepare('DELETE FROM service WHERE ID_Demande= :id_demande;');
				$req_del_service->execute(array(
						'id_demande' => $id_demande
				)) or die(print_r($req_del_service->errorInfo()));
				$req_del_hote = $bdd_supervision->prepare('DELETE FROM hote WHERE ID_Demande= :id_demande;');
				$req_del_hote->execute(array(
						'id_demande' => $id_demande
				)) or die(print_r($req_del_hote->errorInfo()));
				$req_del_dem = $bdd_supervision->prepare('DELETE FROM demande WHERE ID_Demande= :id_demande;');
				$req_del_dem->execute(array(
						'id_demande' => $id_demande
				)) or die(print_r($req_del_dem->errorInfo()));
			};
		};
		$contenu_html .= "</table><br />";
	};
	/**
	 * initialisation mail
	 */
	$mail = explode(';',$res_demandeur['email']);
	$adresse_mail = $mail[0];
	//$adresse_mail = "cedric.meschin@tessi.fr";
	
	if (!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $adresse_mail)) // On filtre les serveurs qui rencontrent des bogues.
	{
		$passage_ligne = "\r\n";
	}
	else
	{
		$passage_ligne = "\n";
	};
	
	/**
	 * Constitution du corps du mail
	 */
	//=====Définition de l'ogjet.
	$sujet = "[CENTREON] Recapitulatif de vos demandes de changement non finalisees au ". $heure_envoi;
	//=========
	//=====Déclaration du message au format HTML.
	$message_html = "
				<!DOCTYPE html>
				<html>
					<style type=\"text/css\">
						@page {  }
						table { border-collapse:collapse; border-spacing:0; empty-cells:show; display:flex; justify-content: space-around; flex-border: none }
						td, th { vertical-align:top; font-size:12pt;}
						h1, h2, h3, h4, h5, h6 { clear:both }
						ol, ul { margin:0; padding:0;}
						li { list-style: none; margin:0; padding:0;}
						<!-- \"li span.odfLiEnd\" - IE 7 issue-->
						li span. { clear: both; line-height:0; width:0; height:0; margin:0; padding:0; }
						span.footnodeNumber { padding-right:1em; }
						span.annotation_style_by_filter { font-size:95%; font-family: Helvetica Neue, arial, sans-serif; background-color:#fff000;  margin:0; border:0; padding:0;  }
						* { margin:0;}
						.P1 { font-size:12pt; font-family: Helvetica Neue, arial, sans-serif; writing-mode:page; }
						.P2 { font-size:12pt; font-family: Helvetica Neue, arial, sans-serif; writing-mode:page; text-decoration:underline; font-weight:bold; }
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
							<p class='P6'>Liste des demandes non finalisées au " . $heure_envoi . ".</p>
						</header>
						<section>
							<div>
							<p>Bonjour, voici la liste des demandes que vous avez initialisées il y a plus de 15 jours et qui sont toujours à l'état de brouillon aujourd'hui.<br />
							Vous avez désormais la possibilité de supprimer vous-même ces demandes directement depuis l'interface.<br />
							<i><b>Attention, les brouillons supérieurs à deux (2) mois seront automatiquement supprimés.<b></i></p><br />
							" . $contenu_html . "
							<br />
							</div>
						</section>
						<footer>
							<p class='P3'>
								<span>Ce message est envoyé par un robot. Pour toute information complémentaire veuillez contacter cedric.meschin@tessi.fr</span>
							</p>
							<p class='P4'>
								<span>Pensez à l'environnement, n'imprimer ce mail que si nécessaire.</span>
							</p>
						</footer>
					</body>
				</html>
			";
	//==========
	//=====Création de la boundary
	$boundary = "-----=".md5(rand());
	//==========
	
	//=====Création du header de l'e-mail.
	$header = "From: \"changement_centreon\"<admin_centreon@tessi.fr>".$passage_ligne;
	$header.= "Reply-to: \"Centreon_TT\" <admin_centreon@tessi.fr>".$passage_ligne;
	$header.= "MIME-Version: 1.0".$passage_ligne;
	$header .= "X-Priority: 3".$passage_ligne;
	$header.= "Content-Type: multipart/alternative;".$passage_ligne." boundary=\"$boundary\"".$passage_ligne; // envoie du format text et HTML
	//==========
	
	//=====Création du message.
	$message = $passage_ligne."--".$boundary.$passage_ligne;
	
	//=====Ajout du message au format HTML
	//$message.= "Content-Type: text/html; charset=\"ISO-8859-1\"".$passage_ligne;
	$message.= "Content-Type: text/html; charset=\"UTF-8\"".$passage_ligne;
	$message.= "Content-Transfer-Encoding: 8bit".$passage_ligne;
	$message.= $passage_ligne.$message_html.$passage_ligne;
	//==========
	$message.= $passage_ligne."--".$boundary."--".$passage_ligne;
	$message.= $passage_ligne."--".$boundary."--".$passage_ligne;
	//==========
	//addlog("message constitué");
	//=====Envoi de l'e-mail.
	mail($adresse_mail,$sujet,$message,$header);
};
