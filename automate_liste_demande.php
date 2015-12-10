<?php
//session_start();
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
	//include('log.php'); // chargement de la fonction de log
// 	include_once('connexion_sql_centreon.php'); // connexion à la base centreon
	include_once('connexion_sql_supervision.php'); // connexion à la base changement
	$bdd_supervision->beginTransaction();
	$heure_envoi = date("d/m/Y H:i");

//	$date8J = new DateTime();
//	date_add($date8J,"8 DAYS");
//	$date8J->add(new DateInterval("P8M"));
//	$date8J->add(new DateInterval('P8D'));
	// Dates SQL
	$dateM28J=date("Y-m-d", strtotime("-28 day"));
	$dateM21J=date("Y-m-d", strtotime("-21 day"));
	$dateM14J=date("Y-m-d", strtotime("-14 day"));
	$dateM7J=date("Y-m-d", strtotime("-7 day"));
	$dateJ = date("Y-m-d");
	
	$dateP7J=date("Y-m-d", strtotime("+7 day"));
	// Date Mail
	$date_mailM28J=date("d/m", strtotime("-28 day"));
	$date_mailM21J=date("d/m", strtotime("-21 day"));
	$date_mailM14J=date("d/m", strtotime("-14 day"));
	$date_mailM7J=date("d/m", strtotime("-7 day"));
	$date_mailJ = date("d/m");
	$date_mailP1J=date("d/m", strtotime("+1 day"));
	$date_mailP7J=date("d/m", strtotime("+7 day"));
	echo "dateM28J=".$dateM28J;
	echo "\n";
	echo "dateM21J=".$dateM21J;
	echo "\n";
	echo "dateM14J=".$dateM14J;
		echo "\n";
	echo "dateM7J=".$dateM7J;
		echo "\n";
	echo "dateJ=".$dateJ;
		echo "\n";
	echo "dateP7J=".$dateP7J;
	echo "\n";
	
// 	$date_make = date("m,d,Y");
// 	$jour_semaine = date("N");
// 	//$heure = date("H:i");
// 	$heure = time(); // heure actuelle au format timestamp
// 	$lundi=false;
// 	$mardi=false;
// 	$mercredi=false;
// 	$jeudi=false;
// 	$vendredi=false;
// 	$samedi=false;
// 	$dimanche=false;
	
// 	addlog("jour_semaine=" . $jour_semaine);
// 	switch ($jour_semaine){
// 		case 1: $lundi = true; break;
// 		case 2: $mardi = true; break;
// 		case 3: $mercredi = true; break;
// 		case 4: $jeudi = true; break;
// 		case 5: $vendredi = true; break;
// 		case 6: $samedi = true; break;
// 		case 7: $dimanche = true; break;
// 		default: echo 'Oups, nous ne sommes pas un jour de la semaine! jour_semaine=' . $jour_semaine;
// 	};
		
/**
 * Récupération de la liste des demandes en cours
 */
// 	$req_lst_J = $bdd_supervision->prepare('
// 		SELECT
// 			 Demandeur,
// 			 Date_Demande,
// 			 Date_Supervision_Demandee,
// 			 Code_Client,
// 			 Etat_Demande
// 		 FROM demande
// 		 WHERE Date_Supervision_Demandee<="' . $dateJ . '" AND Etat_Demande IN ("A Traiter","En cours","Validation")
// 		 ORDER BY Date_Supervision_Demandee, Code_Client;');
	$req_lst_J = $bdd_supervision->prepare('
		SELECT
			 Demandeur,
			 Date_Demande,
			 Date_Supervision_Demandee,
			 Code_Client,
			 Etat_Demande,
			 (SELECT CONCAT(FLOOR(sum(temps_hote + temps_service)/60),"h",LPAD(sum(temps_hote + temps_service)%60,2,"00")) AS Temps_Global
				FROM demande
				WHERE Etat_Demande IN ("A Traiter","En cours","Validation")) as Temps_Global
		 FROM demande
		 WHERE Etat_Demande IN ("A Traiter","En cours","Validation")
		 ORDER BY Date_Supervision_Demandee, Code_Client;');
	$req_lst_J->execute(array()) or die(print_r($req_lst_J->errorInfo()));

	
	$req_S3 = $bdd_supervision->prepare('
		SELECT
			count(Etat_demande) AS Nbre
		FROM demande
		WHERE Date_Fin_Traitement>"' . $dateM28J . '" AND Date_Fin_Traitement<= "' . $dateM21J . '" AND Etat_Demande="Traité";');
	$req_S3->execute(array()) or die(print_r($req_S3->errorInfo()));
	$req_S2 = $bdd_supervision->prepare('
		SELECT
			count(Etat_demande) AS Nbre
		FROM demande
		WHERE Date_Fin_Traitement>"' . $dateM21J . '" AND Date_Fin_Traitement<= "' . $dateM14J . '" AND Etat_Demande="Traité";');
	$req_S2->execute(array()) or die(print_r($req_S2->errorInfo()));
	$req_S1 = $bdd_supervision->prepare('
		SELECT
			count(Etat_demande) AS Nbre
		FROM demande
		WHERE Date_Fin_Traitement>"' . $dateM14J . '" AND Date_Fin_Traitement<= "' . $dateM7J . '" AND Etat_Demande="Traité";');
	$req_S1->execute(array()) or die(print_r($req_S1->errorInfo()));
	$req_S0 = $bdd_supervision->prepare('
		SELECT
			count(Etat_demande) AS Nbre
		FROM demande
		WHERE Date_Fin_Traitement>"' . $dateM7J . '" AND Date_Fin_Traitement<= "' . $dateJ . '" AND Etat_Demande="Traité";');
	$req_S0->execute(array()) or die(print_r($req_S0->errorInfo()));

	/**
	 * indicateur anticipation
	 */
	$req_anticipJ_S3 = $bdd_supervision->prepare('
		SELECT
			count(Etat_demande) AS Nbre
		FROM demande
		WHERE Date_Supervision_Demandee > "' . $dateM28J . '" AND Date_Supervision_Demandee <= "' . $dateM21J . '" AND DATEDIFF(Date_Supervision_Demandee,Date_Demande) = 0;');
	$req_anticipJ_S3->execute(array()) or die(print_r($req_anticipJ_S3->errorInfo()));

	$req_anticipJ7_S3 = $bdd_supervision->prepare('
		SELECT
			count(Etat_demande) AS Nbre
		FROM demande
		WHERE Date_Supervision_Demandee > "' . $dateM28J . '" AND Date_Supervision_Demandee <= "' . $dateM21J . '" AND DATEDIFF(Date_Supervision_Demandee,Date_Demande) between 1 AND 7;');
	$req_anticipJ7_S3->execute(array()) or die(print_r($req_anticipJ7_S3->errorInfo()));
	
	$req_anticipJP7_S3 = $bdd_supervision->prepare('
		SELECT
			count(Etat_demande) AS Nbre
		FROM demande
		WHERE Date_Supervision_Demandee > "' . $dateM28J . '" AND Date_Supervision_Demandee <= "' . $dateM21J . '" AND DATEDIFF(Date_Supervision_Demandee,Date_Demande) > 7;');
	$req_anticipJP7_S3->execute(array()) or die(print_r($req_anticipJP7_S3->errorInfo()));
	
	$req_anticipJ_S2 = $bdd_supervision->prepare('
		SELECT
			count(Etat_demande) AS Nbre
		FROM demande
		WHERE Date_Supervision_Demandee > "' . $dateM21J . '" AND Date_Supervision_Demandee <= "' . $dateM14J . '" AND DATEDIFF(Date_Supervision_Demandee,Date_Demande) = 0;');
	$req_anticipJ_S2->execute(array()) or die(print_r($req_anticipJ_S2->errorInfo()));

	$req_anticipJ7_S2 = $bdd_supervision->prepare('
		SELECT
			count(Etat_demande) AS Nbre
		FROM demande
		WHERE Date_Supervision_Demandee > "' . $dateM21J . '" AND Date_Supervision_Demandee <= "' . $dateM14J . '" AND DATEDIFF(Date_Supervision_Demandee,Date_Demande) between 1 AND 7;');
	$req_anticipJ7_S2->execute(array()) or die(print_r($req_anticipJ7_S2->errorInfo()));
	
	$req_anticipJP7_S2 = $bdd_supervision->prepare('
		SELECT
			count(Etat_demande) AS Nbre
		FROM demande
		WHERE Date_Supervision_Demandee > "' . $dateM21J . '" AND Date_Supervision_Demandee <= "' . $dateM14J . '" AND DATEDIFF(Date_Supervision_Demandee,Date_Demande) > 7;');
	$req_anticipJP7_S2->execute(array()) or die(print_r($req_anticipJP7_S2->errorInfo()));
								
	$req_anticipJ_S1 = $bdd_supervision->prepare('
		SELECT
			count(Etat_demande) AS Nbre
		FROM demande
		WHERE Date_Supervision_Demandee > "' . $dateM14J . '" AND Date_Supervision_Demandee <= "' . $dateM7J . '" AND DATEDIFF(Date_Supervision_Demandee,Date_Demande) = 0;');
	$req_anticipJ_S1->execute(array()) or die(print_r($req_anticipJ_S1->errorInfo()));
	
	$req_anticipJ7_S1 = $bdd_supervision->prepare('
		SELECT
			count(Etat_demande) AS Nbre
		FROM demande
		WHERE Date_Supervision_Demandee > "' . $dateM14J . '" AND Date_Supervision_Demandee <= "' . $dateM7J . '" AND DATEDIFF(Date_Supervision_Demandee,Date_Demande) between 1 AND 7;');
	$req_anticipJ7_S1->execute(array()) or die(print_r($req_anticipJ7_S1->errorInfo()));
	
	$req_anticipJP7_S1 = $bdd_supervision->prepare('
			SELECT
			count(Etat_demande) AS Nbre
			FROM demande
			WHERE Date_Supervision_Demandee > "' . $dateM14J . '" AND Date_Supervision_Demandee <= "' . $dateM7J . '" AND DATEDIFF(Date_Supervision_Demandee,Date_Demande) > 7;');
	$req_anticipJP7_S1->execute(array()) or die(print_r($req_anticipJP7_S1->errorInfo()));
	
	$req_anticipJ_S0 = $bdd_supervision->prepare('
		SELECT
			count(Etat_demande) AS Nbre
		FROM demande
		WHERE Date_Supervision_Demandee > "' . $dateM7J . '" AND Date_Supervision_Demandee <= "' . $dateJ . '" AND DATEDIFF(Date_Supervision_Demandee,Date_Demande) = 0;');
	$req_anticipJ_S0->execute(array()) or die(print_r($req_anticipJ_S0->errorInfo()));
	
	$req_anticipJ7_S0 = $bdd_supervision->prepare('
		SELECT
			count(Etat_demande) AS Nbre
		FROM demande
		WHERE Date_Supervision_Demandee > "' . $dateM7J . '" AND Date_Supervision_Demandee <= "' . $dateJ . '" AND DATEDIFF(Date_Supervision_Demandee,Date_Demande) between 1 AND 7;');
	$req_anticipJ7_S0->execute(array()) or die(print_r($req_anticipJ7_S0->errorInfo()));
	
	$req_anticipJP7_S0 = $bdd_supervision->prepare('
			SELECT
			count(Etat_demande) AS Nbre
			FROM demande
			WHERE Date_Supervision_Demandee > "' . $dateM7J . '" AND Date_Supervision_Demandee <= "' . $dateJ . '" AND DATEDIFF(Date_Supervision_Demandee,Date_Demande) > 7;');
	$req_anticipJP7_S0->execute(array()) or die(print_r($req_anticipJP7_S0->errorInfo()));
				
	$req_J = $bdd_supervision->prepare('
		SELECT
			 Etat_Demande,
			 count(Etat_demande) AS Nbre 
		FROM demande 
		WHERE (Date_Supervision_Demandee<= "' . $dateJ . '" AND Etat_Demande IN ("A Traiter","En cours","Validation")) OR (Date_Fin_Traitement>"' . $dateM7J . '" AND Date_Fin_Traitement<= "' . $dateJ . '" AND Etat_Demande="Traité")  
		GROUP BY Etat_Demande;');
	$req_J->execute(array()) or die(print_r($req_J->errorInfo()));

	$req_7J = $bdd_supervision->prepare('
		SELECT
			 Etat_Demande,
			 count(Etat_demande) AS Nbre
		FROM demande 
		WHERE Date_Supervision_Demandee>"' . $dateJ . '" AND  Date_Supervision_Demandee<= "' . $dateP7J . '" AND Etat_Demande IN ("A Traiter","En cours","Validation","Traité") 
		GROUP BY Etat_Demande;');
	$req_7J->execute(array()) or die(print_r($req_7J->errorInfo()));

	//echo "requete7J=" . 'SELECT Etat_Demande, count(Etat_demande) AS Nbre FROM demande WHERE Date_Supervision_Demandee>"' . $dateJ . '" AND  Date_Supervision_Demandee<= "' . $dateP7J . '" AND Etat_Demande IN ("A Traiter","En cours","Validation","Traité") ORDER BY Etat_Demande;';
	$req_P7J = $bdd_supervision->prepare('
		SELECT
			 Etat_Demande,
			 count(Etat_demande) AS Nbre
		FROM demande 
		WHERE Date_Supervision_Demandee>= "' . $dateP7J . '" AND Etat_Demande IN ("A Traiter","En cours","Validation","Traité") 
		GROUP BY Etat_Demande;');
	$req_P7J->execute(array()) or die(print_r($req_P7J->errorInfo()));
	
	//initialisation mail
	$adresse_mail = "jean-marc.raud@tessi.fr;pascal.picchiottino@tessi.fr;nicolas.schmitt@tessi.fr;cedric.meschin@tessi.fr";
	//$adresse_mail = "cedric.meschin@tessi.fr";
	// 				$adresse_mail .= " " . htmlspecialchars($res_lst_notif['gb_mail_liste']);
	//addlog("liste_mail initiale=" . $adresse_mail);
	$adresse_mail = str_replace(";", ",", $adresse_mail); // converti les ; en , et ajoute un espace
	// 				$adresse_mail = str_replace(",", ", ", $adresse_mail); // ajoute un espace après les virgules
	// 			    $adresse_mail = str_replace(",", " ", $adresse_mail); // converti la virgule en espace
	// 				$adresse_mail = str_replace("  ", " ", $adresse_mail); // supprime les espaces en double.
	//addlog("liste_mail corrigee=" . $adresse_mail);
	if (!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $adresse_mail)) // On filtre les serveurs qui rencontrent des bogues.
	{
		$passage_ligne = "\r\n";
	}
	else
	{
		$passage_ligne = "\n";
	};
	
//	$contenu_brut="";
	$contenu_html="";
	
/**
 * Initialisation de la boucle sur chaque demande
 */
	$atraiterJ=0;
	$encoursJ=0;
	$validationJ=0;
	$traiteJ=0;
	$atraiter7J=0;
	$encours7J=0;
	$validation7J=0;
	$traite7J=0;
	$atraiterP7J=0;
	$encoursP7J=0;
	$validationP7J=0;
	$traiteP7J=0;
	
	$atraiterJ_prct=0;
	$encoursJ_prct=0;
	$validationJ_prct=0;
	$traiteJ_prct=0;
	$atraiter7J_prct=0;
	$encours7J_prct=0;
	$validation7J_prct=0;
	$traite7J_prct=0;
	$atraiterP7J_prct=0;
	$encoursP7J_prct=0;
	$validationP7J_prct=0;
	$traiteP7J_prct=0;
	
	while ($res_J = $req_J->fetch())
	{
		echo "Etat_demandeJ=".$res_J['Etat_Demande']."\n";
		echo "NbreJ=".$res_J['Nbre']."\n";
		if ($res_J['Etat_Demande'] == 'A Traiter'){
			$atraiterJ=htmlspecialchars($res_J['Nbre']);
		}elseif ($res_J['Etat_Demande'] == 'En cours'){
			$encoursJ=htmlspecialchars($res_J['Nbre']);
		}elseif ($res_J['Etat_Demande'] == 'Validation'){
			$validationJ=htmlspecialchars($res_J['Nbre']);
		}elseif ($res_J['Etat_Demande'] == 'Traité'){
			$traiteJ=htmlspecialchars($res_J['Nbre']);
		};
	};
	while ($res_7J = $req_7J->fetch())
	{
		echo "Etat_demande7J=".$res_7J['Etat_Demande']."\n";
		echo "Nbre7J=".$res_7J['Nbre']."\n";
		if ($res_7J['Etat_Demande'] == 'A Traiter'){
			$atraiter7J=htmlspecialchars($res_7J['Nbre']);
		}elseif ($res_7J['Etat_Demande'] == 'En cours'){
			$encours7J=htmlspecialchars($res_7J['Nbre']);
		}elseif ($res_7J['Etat_Demande'] == 'Validation'){
			$validation7J=htmlspecialchars($res_7J['Nbre']);
		}elseif ($res_7J['Etat_Demande'] == 'Traité'){
			$traite7J=htmlspecialchars($res_7J['Nbre']);
		};
	};
	while ($res_P7J = $req_P7J->fetch())
	{
		echo "Etat_demandeP7J=".$res_P7J['Etat_Demande']."\n";
		echo "NbreP7J=".$res_P7J['Nbre']."\n";
		if ($res_P7J['Etat_Demande'] == 'A Traiter'){
			$atraiterP7J=htmlspecialchars($res_P7J['Nbre']);
		}Elseif ($res_P7J['Etat_Demande'] == 'En cours'){
			$encoursP7J=htmlspecialchars($res_P7J['Nbre']);
		}elseif ($res_P7J['Etat_Demande'] == 'Validation'){
			$validationP7J=htmlspecialchars($res_P7J['Nbre']);
		}elseif ($res_P7J['Etat_Demande'] == 'Traité'){
			$traiteP7J=htmlspecialchars($res_P7J['Nbre']);
		};
	};
	
	while ($res_S3 = $req_S3->fetch())
	{
		$Nb_S3=htmlspecialchars($res_S3['Nbre']);
	};
	while ($res_S2 = $req_S2->fetch())
	{
		$Nb_S2=htmlspecialchars($res_S2['Nbre']);
	};
	while ($res_S1 = $req_S1->fetch())
	{
		$Nb_S1=htmlspecialchars($res_S1['Nbre']);
	};
	while ($res_S0 = $req_S0->fetch())
	{
		$Nb_S0=htmlspecialchars($res_S0['Nbre']);
	};

	while ($res_anticipJ_S3 = $req_anticipJ_S3->fetch())
	{
		$NbJ_S3=htmlspecialchars($res_anticipJ_S3['Nbre']);
	};
	while ($res_anticipJ_S2 = $req_anticipJ_S2->fetch())
	{
		$NbJ_S2=htmlspecialchars($res_anticipJ_S2['Nbre']);
	};
	while ($res_anticipJ_S1 = $req_anticipJ_S1->fetch())
	{
		$NbJ_S1=htmlspecialchars($res_anticipJ_S1['Nbre']);
	};
	while ($res_anticipJ_S0 = $req_anticipJ_S0->fetch())
	{
		$NbJ_S0=htmlspecialchars($res_anticipJ_S0['Nbre']);
	};
	
	while ($res_anticipJ7_S3 = $req_anticipJ7_S3->fetch())
	{
		$NbJ7_S3=htmlspecialchars($res_anticipJ7_S3['Nbre']);
	};
	while ($res_anticipJ7_S2 = $req_anticipJ7_S2->fetch())
	{
		$NbJ7_S2=htmlspecialchars($res_anticipJ7_S2['Nbre']);
	};
	while ($res_anticipJ7_S1 = $req_anticipJ7_S1->fetch())
	{
		$NbJ7_S1=htmlspecialchars($res_anticipJ7_S1['Nbre']);
	};
	while ($res_anticipJ7_S0 = $req_anticipJ7_S0->fetch())
	{
		$NbJ7_S0=htmlspecialchars($res_anticipJ7_S0['Nbre']);
	};
	
	while ($res_anticipJP7_S3 = $req_anticipJP7_S3->fetch())
	{
		$NbJP7_S3=htmlspecialchars($res_anticipJP7_S3['Nbre']);
	};
	while ($res_anticipJP7_S2 = $req_anticipJP7_S2->fetch())
	{
		$NbJP7_S2=htmlspecialchars($res_anticipJP7_S2['Nbre']);
	};
	while ($res_anticipJP7_S1 = $req_anticipJP7_S1->fetch())
	{
		$NbJP7_S1=htmlspecialchars($res_anticipJP7_S1['Nbre']);
	};
	while ($res_anticipJP7_S0 = $req_anticipJP7_S0->fetch())
	{
		$NbJP7_S0=htmlspecialchars($res_anticipJP7_S0['Nbre']);
	};
	
	/**
	 * Calcul du nombre de demande Total à J
	 */
	
	
	/**
	 * Calcul des totaux et pourcentages
	 */
	$total = $atraiterJ + $encoursJ + $validationJ + $atraiter7J + $encours7J + $validation7J + $atraiterP7J + $encoursP7J + $validationP7J + $traiteJ + $traite7J + $traiteP7J;
	$total_atraiter = $atraiterJ + $atraiter7J + $atraiterP7J;
	$total_encours = $encoursJ + $encours7J + $encoursP7J;
	$total_validation = $validationJ + $validation7J + $validationP7J;
	$total_traite = $traiteJ + $traite7J + $traiteP7J;
	
	$total_J = $atraiterJ + $encoursJ + $validationJ + $traiteJ;
	$total_7J = $atraiter7J + $encours7J + $validation7J + $traite7J;
	$total_P7J = $atraiterP7J + $encoursP7J + $validationP7J + $traiteP7J;
	
	$atraiterJ_prct = round($atraiterJ *100 / $total,1);
	$encoursJ_prct = round($encoursJ *100 / $total,1);
	$validationJ_prct = round($validationJ *100 / $total,1);
	$traiteJ_prct = round($traiteJ *100 / $total,1);
	$atraiter7J_prct = round($atraiter7J *100 / $total,1);
	$encours7J_prct = round($encours7J *100 / $total,1);
	$validation7J_prct = round($validation7J *100 / $total,1);
	$traite7J_prct = round($traite7J *100 / $total,1);
	$atraiterP7J_prct = round($atraiterP7J *100 / $total,1);
	$encoursP7J_prct = round($encoursP7J *100 / $total,1);
	$validationP7J_prct = round($validationP7J *100 / $total,1);
	$traiteP7J_prct = round($traiteP7J *100 / $total,1);
	
	$total_atraiter_prct = round($total_atraiter * 100 / $total,1);
	$total_encours_prct = round($total_encours * 100 / $total,1);
	$total_validation_prct = round($total_validation * 100 / $total,1);
	$total_traite_prct = round($total_traite * 100 / $total,1);
	
	$total_J_prct = round($total_J * 100 / $total,1);
	$total_7J_prct = round($total_7J * 100 / $total,1);
	$total_P7J_prct = round($total_P7J * 100 / $total,1);
	$total_prct = $total * 100 / $total;
	
	$contenu_html .="<p>Etat des demandes à traiter et en cours.</p>";
	$contenu_html .= "<table border='0' cellspacing='0' cellpadding='0' class='Tableau1'>
							<tr><th class='Tableau1_A1'>Statut</th>
								<th class='Tableau1_A1'>échéance<br/>au plus tard le " . $date_mailJ . "</th>
								<th class='Tableau1_A1'>échéance<br/>entre le " . $date_mailP1J . " et le " . $date_mailP7J . "</th>
								<th class='Tableau1_A1'>échéance<br/>après le " . $date_mailP7J . "</th>
								<th class='Tableau1_A1'>Total des demandes par statut</th>
							</tr>";
	$contenu_html .= "<tr>
	 				<td class='Tableau1_A1'>A traiter</td>
	 				<td class='Tableau1_A1'>" . $atraiterJ . " (" . $atraiterJ_prct . "%)</td>
	 				<td class='Tableau1_A1'>" . $atraiter7J . " (" . $atraiter7J_prct . "%)</td>
	 				<td class='Tableau1_A1'>" . $atraiterP7J . " (" . $atraiterP7J_prct . "%)</td>
	 				<td class='Tableau1_A1'>" . $total_atraiter . " (" . $total_atraiter_prct . "%)</td>
				</tr>";
	$contenu_html .= "<tr>
	 				<td class='Tableau1_A1'>En cours</td>
	 				<td class='Tableau1_A1'>" . $encoursJ . " (" . $encoursJ_prct . "%)</td>
	 				<td class='Tableau1_A1'>" . $encours7J . " (" . $encours7J_prct . "%)</td>
	 				<td class='Tableau1_A1'>" . $encoursP7J . " (" . $encoursP7J_prct . "%)</td>
	 				<td class='Tableau1_A1'>" . $total_encours . " (" . $total_encours_prct . "%)</td>
	 			</tr>";
	$contenu_html .= "<tr>
	 				<td class='Tableau1_A1'>Validation</td>
	 				<td class='Tableau1_A1'>" . $validationJ . " (" . $validationJ_prct . "%)</td>
	 				<td class='Tableau1_A1'>" . $validation7J . " (" . $validation7J_prct . "%)</td>
	 				<td class='Tableau1_A1'>" . $validationP7J . " (" . $validationP7J_prct . "%)</td>
	 				<td class='Tableau1_A1'>" . $total_validation . " (" . $total_validation_prct . "%)</td>
	 			</tr>";
	$contenu_html .= "<tr>
	 				<td class='Tableau1_A1'>Traité</td>
	 				<td class='Tableau1_A1'>" . $traiteJ . " (" . $traiteJ_prct . "%)</td>
	 				<td class='Tableau1_A1'>" . $traite7J . " (" . $traite7J_prct . "%)</td>
	 				<td class='Tableau1_A1'>" . $traiteP7J . " (" . $traiteP7J_prct . "%)</td>
	 				<td class='Tableau1_A1'>" . $total_traite . " (" . $total_traite_prct . "%)</td>
	 			</tr>";
	
	$contenu_html .= "<tr>
	 				<td class='Tableau1_A1'>Total des demandes par échéance</td>
	 				<td class='Tableau1_A1'>" . $total_J . " (" . $total_J_prct . "%)</td>
	 				<td class='Tableau1_A1'>" . $total_7J . " (" . $total_7J_prct . "%)</td>
	 				<td class='Tableau1_A1'>" . $total_P7J . " (" . $total_P7J_prct . "%)</td>
	 				<td class='Tableau1_A1'>" . $total . " (" . $total_prct . "%)</td>
	 			</tr>";
	
	
	$contenu_html .= "</table><br />";
	$contenu_html .="<p class='P1'>Evolution du nombre de demandes traitées sur les quatres dernières semaines</p>";
	$contenu_html .= "<table border='0' cellspacing='0' cellpadding='0' class='Tableau1'>
                                   <tr><th class='Tableau1_A1'>semaine S-3</th>
                                   <th class='Tableau1_A1'>semaine S-2</th>
                                   <th class='Tableau1_A1'>semaine S-1</th>
                                   <th class='Tableau1_A1'>semaine S0</th>
                                   </tr>";
	$contenu_html .= "<tr>
	 				<td class='Tableau1_A1'>" . $Nb_S3 . "</td>
	 				<td class='Tableau1_A1'>" . $Nb_S2 . "</td>
	 				<td class='Tableau1_A1'>" . $Nb_S1 . "</td>
	 				<td class='Tableau1_A1'>" . $Nb_S0 . "</td>
	 			</tr>";
	$contenu_html .= "</table><br />";

	$contenu_html .="<p class='P1'>Indicateur d'anticipation des demandes</p>";
	$contenu_html .= "<table border='0' cellspacing='0' cellpadding='0' class='Tableau1'>
                                <tr>
                                        <th class='Tableau1_A1'>Délai entre la date de la demande <br/>et la date de supervision souhaitée</th>
                                        <th class='Tableau1_A1'>semaine S-3</th>
                                        <th class='Tableau1_A1'>semaine S-2</th>
                                        <th class='Tableau1_A1'>semaine S-1</th>
                                        <th class='Tableau1_A1'>semaine S0</th>
                                </tr>";
	$contenu_html .= "<tr>
                                        <td class='Tableau1_A1'>J</td>
                                        <td class='Tableau1_A1'>" . $NbJ_S3 . "</td>
                                        <td class='Tableau1_A1'>" . $NbJ_S2 . "</td>
                                        <td class='Tableau1_A1'>" . $NbJ_S1 . "</td>
                                        <td class='Tableau1_A1'>" . $NbJ_S0 . "</td>
                                </tr>";
	$contenu_html .= "<tr>
                                        <td class='Tableau1_A1'>J+1 à J+7</td>
                                        <td class='Tableau1_A1'>" . $NbJ7_S3 . "</td>
                                        <td class='Tableau1_A1'>" . $NbJ7_S2 . "</td>
                                        <td class='Tableau1_A1'>" . $NbJ7_S1 . "</td>
                                        <td class='Tableau1_A1'>" . $NbJ7_S0 . "</td>
                                </tr>";
	$contenu_html .= "<tr>
                                        <td class='Tableau1_A1'>J>7</td>
                                        <td class='Tableau1_A1'>" . $NbJP7_S3 . "</td>
                                        <td class='Tableau1_A1'>" . $NbJP7_S2 . "</td>
                                        <td class='Tableau1_A1'>" . $NbJP7_S1 . "</td>
                                        <td class='Tableau1_A1'>" . $NbJP7_S0 . "</td>
                                </tr>";
	$contenu_html .= "</table><br />";
		
	$contenu_html .= "<p>Liste des demandes à traiter</p>";
	$contenu_html .= "<table border='0' cellspacing='0' cellpadding='0' class='Tableau1'>
							<tr><th class='Tableau1_A1'>Date Supervision Demandée</th>
								<th class='Tableau1_A1'>Demandeur</th>
								<th class='Tableau1_A1'>Prestation</th>
								<th class='Tableau1_A1'>Etat demande</th>
							</tr>";
	
	While($res_lst_J = $req_lst_J->fetch())
	{
		$Temps_Global = $res_lst_J['Temps_Global'];
		$contenu_html .= "<tr>
	 				<td class='Tableau1_A1'>" . $res_lst_J['Date_Supervision_Demandee'] . "</td>
	 				<td class='Tableau1_A1'>" . $res_lst_J['Demandeur'] . "</td>
	 				<td class='Tableau1_A1'>" . $res_lst_J['Code_Client'] . "</td>
	 				<td class='Tableau1_A1'>" . $res_lst_J['Etat_Demande'] . "</td>
	 			</tr>";
	};
	$contenu_html .= "</table><br />";
	$contenu_html .= "<p class=\"P1\">Le temps global de traitement estimé pour ces demandes est de ". $Temps_Global . "</p>";
	
//	$contenu_html = $contenu_htmlJ ."<br />" . $contenu_html8J . "<br />" . $contenu_htmlPLUS8J;
				/**
				 * Constitution du corps du mail
				 */
				//=====Définition de l'ogjet.
				$sujet = "Gestion des changements CENTREON: Recapitulatif des demande en cours au ". $heure_envoi;
				//=========
				//=====Déclaration des messages au format texte et au format HTML.
// 				$message_txt = "Liste des demandes de changement à traiter ou en cours de traitement le " . $heure_envoi . "\n
// 						\n
// 						\n
// 						" . $contenu_brut . "\n
// 						Ce message est envoyé au(x) destinataire(s) suivant(s):" . str_replace(',',' ',$adresse_mail) . ".\n
// 						\n
// 						\n
// 						\n
// 						\n
// 						";
				$message_html = "
					<!DOCTYPE html>
					<html>
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
						<body>
							<header>
								<p class=\"P5\">
									<span class=\"T1\">Liste des demandes de changement à traiter ou en cours de traitement le " . $heure_envoi . ".</span>
								</p>
							</header>
							<section>
								" . $contenu_html . "
								<br />
								<p>Ce message est envoyé au(x) destinataire(s) suivant(s): " . str_replace(',',' ',$adresse_mail) . ".</p>
							</section>
							<footer>
								<p class=\"P3\">
									<span class=\"T6\">Ce message est envoyé par un robot, merci de ne pas y répondre. Pour toute information complémentaire veuillez contacter cedric.meschin@tessi.fr</span>
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
// 				$message.= "Content-Type: text/plain; charset=\"UTF-8\"".$passage_ligne;
// 				$message.= "Content-Transfer-Encoding: 8bit".$passage_ligne;
				//$message.= $passage_ligne.$message_txt.$passage_ligne;
				//==========
// 				$message.= $passage_ligne."--".$boundary.$passage_ligne;
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
				//mail("c.zic@free.fr c.meschin@free.fr",$sujet,$message,$header);
				//addlog("mail envoyé à " . $adresse_mail);
				//==========
				// flag envoi mail
// 				$maj_notif = $bdd_supervision->prepare('UPDATE gestion_bam_notification
// 						 SET gb_date_notif= :heure_envoi
// 						 WHERE gb_nom= :gb_nom');
// 				$maj_notif->execute(Array(
// 					'heure_envoi' => $heure_envoi,
// 					'gb_nom' => htmlspecialchars($res_lst_notif['gb_nom'])
// 				)) or die(print_r($maj_notif->errorInfo()));
//			};// fin condition heure atteinte
//		}; // fin condition jour OK
//	};// finde la boucle des notifs
 
	$bdd_supervision->commit();
} catch (Exception $e) {
 	$bdd_supervision->rollBack();
 	//addlog('Erreur traitement envoi mail'. $e->getMessage());
 	die('Erreur traitement envoi_mail: '. $e->getMessage());
};
