<?php
session_start();
/**
 * Lancement
 */

/**
 * Initialisation des constantes
 * Date du jour => pour le stockage de l'heure d'envoi du mail
 * Jour de la semaine => pour la vérification sur la calendrier => De 1 (pour Lundi) à 7 (pour Dimanche)
 * Heure actuelle => pour la vérification sur l'heure de notification
 */
try {
	include('log.php'); // chargement de la fonction de log
	include_once('connexion_sql_centreon.php'); // connexion à la base centreon
	include_once('connexion_sql_supervision.php'); // connexion à la base changement
	$bdd_supervision->beginTransaction();
	$heure_envoi = date("Y-m-d H:i:s");
	$date_make = date("m,d,Y");
	$jour_semaine = date("N");
	//$heure = date("H:i");
	$heure = time(); // heure actuelle au format timestamp
	$lundi=false;
	$mardi=false;
	$mercredi=false;
	$jeudi=false;
	$vendredi=false;
	$samedi=false;
	$dimanche=false;
	
	addlog("jour_semaine=" . $jour_semaine);
	switch ($jour_semaine){
		case 1: $lundi = true; break;
		case 2: $mardi = true; break;
		case 3: $mercredi = true; break;
		case 4: $jeudi = true; break;
		case 5: $vendredi = true; break;
		case 6: $samedi = true; break;
		case 7: $dimanche = true; break;
		default: echo 'Oups, nous ne sommes pas un jour de la semaine! jour_semaine=' . $jour_semaine;
	};
		
/**
 * Récupération de la liste des notifications
 */
	$req_lst_notif = $bdd_supervision->prepare('SELECT
			gb_id,
			gb_nom,
			gb_prestation,
			gb_heure,
			gb_mail_objet,
			gb_mail_titre,
			gb_mail_liste,
			gb_lundi,
			gb_mardi,
			gb_mercredi,
			gb_jeudi,
			gb_vendredi,
			gb_samedi,
			gb_dimanche,
			gb_date_notif
		FROM gestion_bam_notification
		ORDER BY gb_nom;');
	$req_lst_notif->execute(array()) or die(print_r($req_lst_notif->errorInfo()));

/**
 * Initialisation de la boucle sur chaque notif
 */
	while ($res_lst_notif = $req_lst_notif->fetch())
	{
		addlog("##### gb_nom=" . htmlspecialchars($res_lst_notif['gb_nom']) . " #####");
		/**
		 * Vérification si Jour est OK
		 * si oui on continue, si non on passe à la notif suivante
		 */
		if((htmlspecialchars($res_lst_notif['gb_lundi']) == '1' && $lundi==true) 
			|| (htmlspecialchars($res_lst_notif['gb_mardi']) == '1' && $mardi==true)
			|| (htmlspecialchars($res_lst_notif['gb_mercredi']) == '1' && $mercredi==true)
			|| (htmlspecialchars($res_lst_notif['gb_jeudi']) == '1' && $jeudi==true)
			|| (htmlspecialchars($res_lst_notif['gb_vendredi']) == '1' && $vendredi==true)
			|| (htmlspecialchars($res_lst_notif['gb_samedi']) == '1' && $samedi==true)
			|| (htmlspecialchars($res_lst_notif['gb_dimanche']) == '1' && $dimanche==true))
		{
			addlog("lundi=".$lundi);
			addlog("mardi=".$mardi);
			addlog("mercredi=".$mercredi);
			addlog("jeudi=".$jeudi);
			addlog("vendredi=".$vendredi);
			addlog("samedi=".$samedi);
			addlog("dimanche=".$dimanche);
			/**
			 * Vérification si Heure atteinte et notif non encore envoyée (date_notif < heure d'envoi du jour)
			 * si oui on continue, si non on passe à la notif suivante
			 */
// 			addlog("#################### gb_heure_avant=".substr(htmlspecialchars($res_lst_notif['gb_heure']),0,2) . "," . substr(htmlspecialchars($res_lst_notif['gb_heure']),3,2));
// 			$gb_heure_avant = substr(htmlspecialchars($res_lst_notif['gb_heure']),0,2) . "," . substr(htmlspecialchars($res_lst_notif['gb_heure']),3,2) . ",0";
// 			$date_calcul = $gb_heure_avant . "," . $date_make;
// 			addlog("#################### date_calcul=".$date_calcul);
			$gb_heure = mktime(substr(htmlspecialchars($res_lst_notif['gb_heure']),0,2),substr(htmlspecialchars($res_lst_notif['gb_heure']),3,2),0,date("m"),date("d"),date("Y")); // heure paramétrée calculée avec la date du jour au format timestamp
			addlog("gb_heure=".$gb_heure);
			$gb_date_notif = htmlspecialchars($res_lst_notif['gb_date_notif']);
			$gb_date_notif = mktime(substr($gb_date_notif,11,2),substr($gb_date_notif,14,2),substr($gb_date_notif,17,2),substr($gb_date_notif,5,2),substr($gb_date_notif,8,2),substr($gb_date_notif,0,4)); // date dernière notif au format timestamp
// 			addlog("gb_heure manuel=".date("Y-m-d H:i",mktime(01,00,0,03,06,2015)));
// 			$gb_heure_date = date("Y-m-d H:i",$gb_heure);
// 			addlog("gb_heure_date=".$gb_heure_date);
// 			$heure_date = date("Y-m-d H:i",$heure);
			addlog("heure=".$heure);
// 			addlog("heure_date=".$heure_date);
			if (($heure >= $gb_heure) && ($gb_date_notif < $gb_heure)){
				/**
				 * Récupération des ba à notifier
				 */
				$req_gba = $bdd_supervision->prepare('SELECT
						mbc_ba_nom,
						mbc_ba_description
						FROM mod_bam_centreon as mbc
						 INNER JOIN gestion_bam_associe as gba ON mbc.mbc_ba_id=gba.gba_ba_id
						WHERE gba.gba_gb_id= :gba_gb_id
						ORDER BY mbc_ba_nom;');
				$req_gba->execute(array(
						'gba_gb_id' => $res_lst_notif['gb_id']
				)) or die(print_r($res_lst_notif->errorInfo()));
				/**
				 * Récupération des infos bam actuelles dans centreon et création du mail
				 */
				$req_bam = $bdd_centreon->prepare('SELECT
							name as BA_Nom,
							level_w as BA_Seuil_Degrade,
							level_c as BA_Seuil_Critique,
							current_level as BA_Niveau,
							last_state_change as BA_Dernier_Changement,
							current_status as BA_Statut
						FROM mod_bam
						ORDER BY name;');
				$req_bam->execute(array()) or die(print_r($req_bam->errorInfo()));
				/**
				 * Initialisation des constantes du mail
				 */
				$contenu_brut="";
				$contenu_html="";
				//initialisation mail
				$adresse_mail = "";
				$adresse_mail .= " " . htmlspecialchars($res_lst_notif['gb_mail_liste']);
				addlog("liste_mail initiale=" . $adresse_mail);
				$adresse_mail = str_replace(";", ",", $adresse_mail); // converti les ; en , et ajoute un espace
// 				$adresse_mail = str_replace(",", ", ", $adresse_mail); // ajoute un espace après les virgules
// 			    $adresse_mail = str_replace(",", " ", $adresse_mail); // converti la virgule en espace
// 				$adresse_mail = str_replace("  ", " ", $adresse_mail); // supprime les espaces en double.
				//$liste_adresse_mail = str_replace(",", " ", $adresse_mail); // converti la virgule en espace
				addlog("liste_mail corrigee=" . $adresse_mail);
				if (!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $adresse_mail)) // On filtre les serveurs qui rencontrent des bogues.
				{
					$passage_ligne = "\r\n";
				}
				else
				{
					$passage_ligne = "\n";
				};
				$res_bam = $req_bam->fetchall();
				$res_gba = $req_gba->fetchall();
				foreach($res_gba as $element_gba)
				{// pour chaque ba sélectionée pour la notification
					addlog("gba_nom=".$element_gba['mbc_ba_nom']);
					foreach ($res_bam as $element)
					{
						addlog("bam_nom=".$element['BA_Nom']);
						/**
						 * Si la ba correspond à la sélection on la traite
						 */
						if (htmlspecialchars($element['BA_Nom']) == htmlspecialchars($element_gba['mbc_ba_nom'])){
							if (htmlspecialchars($element['BA_Statut']) == 0)
							{
								$BA_Statut = "OK";
								$class_couleur = "STATUT_OK";
							} else if (htmlspecialchars($element['BA_Statut']) == 1)
							{
								$BA_Statut = "DEGRADE";
								$class_couleur = "STATUT_DEGR";
							} else if (htmlspecialchars($element['BA_Statut']) == 2)
							{
								$BA_Statut = "CRITIQUE";
								$class_couleur = "STATUT_CRIT";
							} else if (htmlspecialchars($element['BA_Statut']) == 3)
							{
								$BA_Statut = "INCONNU";
								$class_couleur = "STATUT_INC";
							} else if (htmlspecialchars($element['BA_Statut']) == 4)
							{
								$BA_Statut = "EN ATTENTE";
								$class_couleur = "STATUT_ATT";
							};
						
						
							$contenu_brut .= "        Taux de disponibilité de l'activité métier " . htmlspecialchars($element['BA_Nom']) . ": " . $BA_Statut . " (" . htmlspecialchars($element['BA_Niveau']) . "%)\n\n";
						
	// 						$contenu_html .= "
	// 							<p class=\"P1\">
	// 								<span class=\"T1\">        Taux de disponibilité de l'activité métier " . $element['BA_Nom'] . ": </span>
	// 								<span class=\"" . $class_couleur . "\">" . $BA_Statut . " (" . $element['BA_Niveau'] . "%)</span>
	// 							</p><p></p>";
							$contenu_html .= "<tr>
	 							<td class='Tableau1_A1'>" . htmlspecialchars($element['BA_Nom']) . "</td>
	 							<td class='" . $class_couleur . "'>" . $BA_Statut . " (" . htmlspecialchars($element['BA_Niveau']) . "%)</td></tr>";
						}; //fin condition correspondance gb_nom
					}; // fin boucle liste bam centreon
				};
				/**
				 * Constitution du corps du mail
				 */
				//=====Définition de l'ogjet.
				$sujet = htmlspecialchars($res_lst_notif['gb_mail_objet']);
				//=========
				//=====Déclaration des messages au format texte et au format HTML.
				$message_txt = "Récapitulatif de disponibilité des plateformes BMD le " . $heure_envoi . "\n
						\n
						\n
						" . $contenu_brut . "\n
						Ce message est envoyé au(x) destinataire(s) suivant(s):" . str_replace(',',' ',$adresse_mail) . ".\n
						\n
						\n
						\n
						\n
						Ce message est envoyé par un robot, merci de ne pas y répondre. Pour toute information complémentaire veuillez contacter votre centre de service Tessi Technologies par mail à spoc_susi@tessi.fr ou par téléphone au 0825 287 825.\nPensez à l'environnement, n'imprimer ce mail que si nécessaire.\n
						";
				$message_html = "
					<html xmlns=\"http://www.w3.org/1999/xhtml\">
						<head profile=\"http://dublincore.org/documents/dcmi-terms/\">
						<meta http-equiv=\"Content-Type\" content=\"application/xhtml+xml; charset=utf-8\"/>
						<title xml:lang=\"en-US\">- no title specified</title>
						<meta name=\"DCTERMS.title\" content=\"\" xml:lang=\"en-US\"/>
						<meta name=\"DCTERMS.language\" content=\"en-US\" scheme=\"DCTERMS.RFC4646\"/>
						<meta name=\"DCTERMS.source\" content=\"http://xml.openoffice.org/odf2xhtml\"/>
						<meta name=\"DCTERMS.issued\" content=\"2015-02-25T21:16:29.90\" scheme=\"DCTERMS.W3CDTF\"/>
						<meta name=\"DCTERMS.modified\" content=\"2015-02-25T21:49:58.32\" scheme=\"DCTERMS.W3CDTF\"/>
						<meta name=\"DCTERMS.provenance\" content=\"\" xml:lang=\"en-US\"/>
						<meta name=\"DCTERMS.subject\" content=\",\" xml:lang=\"en-US\"/>
						<link rel=\"schema.DC\" href=\"http://purl.org/dc/elements/1.1/\" hreflang=\"en\"/>
						<link rel=\"schema.DCTERMS\" href=\"http://purl.org/dc/terms/\" hreflang=\"en\"/>
						<link rel=\"schema.DCTYPE\" href=\"http://purl.org/dc/dcmitype/\" hreflang=\"en\"/>
						<link rel=\"schema.DCAM\" href=\"http://purl.org/dc/dcam/\" hreflang=\"en\"/>
						<style type=\"text/css\">
							@page {  }
							table { border-collapse:collapse; border-spacing:0; empty-cells:show }
							td, th { vertical-align:top; font-size:12pt;}
							h1, h2, h3, h4, h5, h6 { clear:both }
							ol, ul { margin:0; padding:0;}
							li { list-style: none; margin:0; padding:0;}
							<!-- \"li span.odfLiEnd\" - IE 7 issue-->
							li span. { clear: both; line-height:0; width:0; height:0; margin:0; padding:0; }
							span.footnodeNumber { padding-right:1em; }
							span.annotation_style_by_filter { font-size:95%; font-family:Arial; background-color:#fff000;  margin:0; border:0; padding:0;  }
							* { margin:0;}
							.P1 { font-size:12pt; font-family:Times New Roman; writing-mode:page; }
							.P2 { font-size:12pt; font-family:Times New Roman; writing-mode:page; text-decoration:underline; font-weight:bold; }
							.P3 { font-size:8pt; font-family:Times New Roman; writing-mode:page; background-color:transparent; }
							.P4 { font-size:8pt; font-family:Times New Roman; writing-mode:page; color:#33cc66; background-color:transparent; }
							.P5 { font-size:14pt; margin-bottom:0.212cm; margin-top:0.423cm; font-family:Arial; writing-mode:page; text-align:center ! important; text-decoration:underline; font-weight:bold; }
							.P6 { font-size:14pt; margin-bottom:0.212cm; margin-top:0.423cm; font-family:Arial; writing-mode:page; text-align:center ! important; font-weight:bold; }
							.STATUT_OK { background-color:#00ff00;border: 1px solid #000; }
							.STATUT_DEGR { background-color:#ff950e;border: 1px solid #000; }
							.STATUT_CRIT { background-color:#ff0000;border: 1px solid #000; }
							.STATUT_INC { background-color:#808080;border: 1px solid #000; }
							.STATUT_ATT { background-color:#0084d1;border: 1px solid #000; }
							<!-- ODF styles with no properties representable as CSS -->
							.T1 .T6  { }
							.Tableau1_A1 { border: 1px solid #000; }
						</style>
						</head>
						<body dir=\"ltr\" style=\"max-width:21.001cm;margin-top:2cm; margin-bottom:2cm; margin-left:2cm; margin-right:2cm; writing-mode:lr-tb; \">
							<header>
								<p class=\"P5\">
									<span class=\"T1\">" . htmlspecialchars($res_lst_notif['gb_mail_titre']) . "</span>
								</p>
								<p class=\"P6\">
									<span class=\"T1\">le " . $heure_envoi . ".</span>
								</p>
							</header>
							<section>
								<table border='0' cellspacing='0' cellpadding='0' class='Tableau1'>
								<tr><th class='Tableau1_A1'>Activité Métier</th><th class='Tableau1_A1'>Taux de disponibilité</th></tr>" . $contenu_html . "
								</table>
								<br />
								<p>Ce message est envoyé au(x) destinataire(s) suivant(s):" . str_replace(',',' ',$adresse_mail) . ".</p>
							</section>
							<footer>
								<p class=\"P3\">
									<span class=\"T6\">Ce message est envoyé par un robot, merci de ne pas y répondre. Pour toute information complémentaire veuillez contacter votre centre de service Tessi Technologies par mail à spoc_susi@tessi.fr ou par téléphone au 0825 287 825.</span>
								</p>
								<p class=\"P4\">
									<span class=\"T6\">Pensez à l'environnement, n'imprimer ce mail que si nécessaire.</span>
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
				$message.= "Content-Type: text/html; charset=\"UTF-8\"".$passage_ligne;
				$message.= "Content-Transfer-Encoding: 8bit".$passage_ligne;
				$message.= $passage_ligne.$message_html.$passage_ligne;
				//==========
				$message.= $passage_ligne."--".$boundary."--".$passage_ligne;
				$message.= $passage_ligne."--".$boundary."--".$passage_ligne;
				//==========
				addlog("message constitué");
				//=====Envoi de l'e-mail.
				mail($adresse_mail,$sujet,$message,$header);
				//mail("c.zic@free.fr c.meschin@free.fr",$sujet,$message,$header);
				addlog("mail envoyé à " . $adresse_mail);
				//==========
				// flag envoi mail
				$maj_notif = $bdd_supervision->prepare('UPDATE gestion_bam_notification
						 SET gb_date_notif= :heure_envoi
						 WHERE gb_nom= :gb_nom');
				$maj_notif->execute(Array(
					'heure_envoi' => $heure_envoi,
					'gb_nom' => htmlspecialchars($res_lst_notif['gb_nom'])
				)) or die(print_r($maj_notif->errorInfo()));
				
				
				
			};// fin condition heure atteinte
		}; // fin condition jour OK
	};// finde la boucle des notifs
 
	$bdd_supervision->commit();
} catch (Exception $e) {
 	$bdd_supervision->rollBack();
 	addlog('Erreur traitement envoi mail'. $e->getMessage());
 	die('Erreur traitement envoi_mail: '. $e->getMessage());
};