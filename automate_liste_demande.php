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
$debug=true; // activation du mode debug
//initialisation mail
$adresse_mail = "jean-marc.raud@tessi.fr;stephane.boulanger@tessi.fr;sophie.pourtau@tessi.fr;veronique.genay@tessi.fr;cedric.meschin@tessi.fr";
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
	$jour7=$jour_semaine+7;
	$jour14=$jour_semaine+14;
	$jour21=$jour_semaine+21;

	// Dates SQL
	$dateM28J = date("Y-m-d", strtotime("-28 day"));
	$dateM21J = date("Y-m-d", strtotime("-21 day"));
	$dateM14J = date("Y-m-d", strtotime("-14 day"));
	$dateM7J = date("Y-m-d", strtotime("-7 day"));
	$dateJ = date("Y-m-d");
	$dim_S0 = date("Y-m-d", strtotime("-$jour_semaine day"));
	$dim_S1 = date("Y-m-d", strtotime("-$jour7 day"));
	$dim_S2 = date("Y-m-d", strtotime("-$jour14 day"));
	$dim_S3 = date("Y-m-d", strtotime("-$jour21 day"));
	$dateP7J = date("Y-m-d", strtotime("+7 day"));
	$moisNum0 = date("Y-m-d", mktime(0, 0, 0, date('n'), 1));
	$moisNum1 = date("Y-m-d", mktime(0, 0, 0, date('n')-1, 1));
	$moisNum2 = date("Y-m-d", mktime(0, 0, 0, date('n')-2, 1));
	$moisNum3 = date("Y-m-d", mktime(0, 0, 0, date('n')-3, 1));
	
	// Date Mail
	$date_mailM28J=date("d/m", strtotime("-28 day"));
	$date_mailM21J=date("d/m", strtotime("-21 day"));
	$date_mailM14J=date("d/m", strtotime("-14 day"));
	$date_mailM7J=date("d/m", strtotime("-7 day"));
	$date_mailJ = date("d/m");
	$dim_mailS0 = date("d/m", strtotime("-$jour_semaine day"));
	$dim_mailS1 = date("d/m", strtotime("-$jour7 day"));
	$dim_mailS2 = date("d/m", strtotime("-$jour14 day"));
	$dim_mailS3 = date("d/m", strtotime("-$jour21 day"));
	$date_mailP1J=date("d/m", strtotime("+1 day"));
	$date_mailP7J=date("d/m", strtotime("+7 day"));

	if ($debug==true)
	{
		echo "dateM28J=".$dateM28J."\n";
		echo "dateM21J=".$dateM21J."\n";
		echo "dateM14J=".$dateM14J."\n";
		echo "dateM7J=".$dateM7J."\n";
		echo "dateJ=".$dateJ."\n";
		echo "dateP7J=".$dateP7J."\n";
		echo "dim_S0=".$dim_S0."\n";
		echo "dim_S1=".$dim_S1."\n";
		echo "dim_S2=".$dim_S2."\n";
		echo "dim_S3=".$dim_S3."\n";
		echo "dim_mailS0=".$dim_mailS0."\n";
		echo "dim_mailS1=".$dim_mailS1."\n";
		echo "dim_mailS2=".$dim_mailS2."\n";
		echo "dim_mailS3=".$dim_mailS3."\n";
		echo "Mois=".$moisNum0."\n";
		echo "Mois-1=".$moisNum1."\n";
		echo "Mois-2=".$moisNum2."\n";
		echo "Mois-3=".$moisNum3."\n";
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
		 WHERE Etat_Demande IN ("A Traiter","En cours","Validation")
		 ORDER BY Date_Supervision_Demandee, Code_Client;');
	$req_lst_J->execute(array()) or die(print_r($req_lst_J->errorInfo()));

	$req_Temps_Global = $bdd_supervision->prepare('
		SELECT CONCAT(FLOOR(sum(temps_hote + temps_service)/60),"h",LPAD(sum(temps_hote + temps_service)%60,2,"00")) AS Temps_Global
				FROM demande
				WHERE Etat_Demande IN ("A Traiter","En cours","Validation");');
	$req_Temps_Global->execute(array()) or die(print_r($req_Temps_Global->errorInfo()));
	
	$req_S3 = $bdd_supervision->prepare('
		SELECT
			CONCAT(FLOOR(sum(temps_hote + temps_service)/60),"h",LPAD(sum(temps_hote + temps_service)%60,2,"00")) AS Temps_Global,
			count(Etat_demande) AS Nbre
		FROM demande
		WHERE Date_Fin_Traitement>"' . $dim_S3 . '" AND Date_Fin_Traitement<= "' . $dim_S2 . '" AND Etat_Demande="Traité";');
	$req_S3->execute(array()) or die(print_r($req_S3->errorInfo()));
	$req_S2 = $bdd_supervision->prepare('
		SELECT
			CONCAT(FLOOR(sum(temps_hote + temps_service)/60),"h",LPAD(sum(temps_hote + temps_service)%60,2,"00")) AS Temps_Global,
			count(Etat_demande) AS Nbre
		FROM demande
		WHERE Date_Fin_Traitement>"' . $dim_S2 . '" AND Date_Fin_Traitement<= "' . $dim_S1 . '" AND Etat_Demande="Traité";');
	$req_S2->execute(array()) or die(print_r($req_S2->errorInfo()));
	$req_S1 = $bdd_supervision->prepare('
		SELECT
			CONCAT(FLOOR(sum(temps_hote + temps_service)/60),"h",LPAD(sum(temps_hote + temps_service)%60,2,"00")) AS Temps_Global,
			count(Etat_demande) AS Nbre
		FROM demande
		WHERE Date_Fin_Traitement>"' . $dim_S1 . '" AND Date_Fin_Traitement<= "' . $dim_S0 . '" AND Etat_Demande="Traité";');
	$req_S1->execute(array()) or die(print_r($req_S1->errorInfo()));
	$req_S0 = $bdd_supervision->prepare('
		SELECT
			CONCAT(FLOOR(sum(temps_hote + temps_service)/60),"h",LPAD(sum(temps_hote + temps_service)%60,2,"00")) AS Temps_Global,
			count(Etat_demande) AS Nbre
		FROM demande
		WHERE Date_Fin_Traitement>"' . $dim_S0 . '" AND Etat_Demande="Traité";');
	$req_S0->execute(array()) or die(print_r($req_S0->errorInfo()));

	/**
	 * indicateur anticipation
	 */
	$req_anticipJ_S3 = $bdd_supervision->prepare('
		SELECT
			count(Etat_demande) AS Nbre
		FROM demande
		WHERE Date_Supervision_Demandee > "' . $dim_S3 . '" AND Date_Supervision_Demandee <= "' . $dim_S2 . '" AND DATEDIFF(Date_Supervision_Demandee,Date_Demande) = 0;');
	$req_anticipJ_S3->execute(array()) or die(print_r($req_anticipJ_S3->errorInfo()));

	$req_anticipJ7_S3 = $bdd_supervision->prepare('
		SELECT
			count(Etat_demande) AS Nbre
		FROM demande
		WHERE Date_Supervision_Demandee > "' . $dim_S3 . '" AND Date_Supervision_Demandee <= "' . $dim_S2 . '" AND DATEDIFF(Date_Supervision_Demandee,Date_Demande) between 1 AND 7;');
	$req_anticipJ7_S3->execute(array()) or die(print_r($req_anticipJ7_S3->errorInfo()));
	
	$req_anticipJP7_S3 = $bdd_supervision->prepare('
		SELECT
			count(Etat_demande) AS Nbre
		FROM demande
		WHERE Date_Supervision_Demandee > "' . $dim_S3 . '" AND Date_Supervision_Demandee <= "' . $dim_S2 . '" AND DATEDIFF(Date_Supervision_Demandee,Date_Demande) > 7;');
	$req_anticipJP7_S3->execute(array()) or die(print_r($req_anticipJP7_S3->errorInfo()));
	
	$req_anticipJ_S2 = $bdd_supervision->prepare('
		SELECT
			count(Etat_demande) AS Nbre
		FROM demande
		WHERE Date_Supervision_Demandee > "' . $dim_S2 . '" AND Date_Supervision_Demandee <= "' . $dim_S1 . '" AND DATEDIFF(Date_Supervision_Demandee,Date_Demande) = 0;');
	$req_anticipJ_S2->execute(array()) or die(print_r($req_anticipJ_S2->errorInfo()));

	$req_anticipJ7_S2 = $bdd_supervision->prepare('
		SELECT
			count(Etat_demande) AS Nbre
		FROM demande
		WHERE Date_Supervision_Demandee > "' . $dim_S2 . '" AND Date_Supervision_Demandee <= "' . $dim_S1 . '" AND DATEDIFF(Date_Supervision_Demandee,Date_Demande) between 1 AND 7;');
	$req_anticipJ7_S2->execute(array()) or die(print_r($req_anticipJ7_S2->errorInfo()));
	
	$req_anticipJP7_S2 = $bdd_supervision->prepare('
		SELECT
			count(Etat_demande) AS Nbre
		FROM demande
		WHERE Date_Supervision_Demandee > "' . $dim_S2 . '" AND Date_Supervision_Demandee <= "' . $dim_S1 . '" AND DATEDIFF(Date_Supervision_Demandee,Date_Demande) > 7;');
	$req_anticipJP7_S2->execute(array()) or die(print_r($req_anticipJP7_S2->errorInfo()));
								
	$req_anticipJ_S1 = $bdd_supervision->prepare('
		SELECT
			count(Etat_demande) AS Nbre
		FROM demande
		WHERE Date_Supervision_Demandee > "' . $dim_S1 . '" AND Date_Supervision_Demandee <= "' . $dim_S0 . '" AND DATEDIFF(Date_Supervision_Demandee,Date_Demande) = 0;');
	$req_anticipJ_S1->execute(array()) or die(print_r($req_anticipJ_S1->errorInfo()));
	
	$req_anticipJ7_S1 = $bdd_supervision->prepare('
		SELECT
			count(Etat_demande) AS Nbre
		FROM demande
		WHERE Date_Supervision_Demandee > "' . $dim_S1 . '" AND Date_Supervision_Demandee <= "' . $dim_S0 . '" AND DATEDIFF(Date_Supervision_Demandee,Date_Demande) between 1 AND 7;');
	$req_anticipJ7_S1->execute(array()) or die(print_r($req_anticipJ7_S1->errorInfo()));
	
	$req_anticipJP7_S1 = $bdd_supervision->prepare('
			SELECT
			count(Etat_demande) AS Nbre
			FROM demande
			WHERE Date_Supervision_Demandee > "' . $dim_S1 . '" AND Date_Supervision_Demandee <= "' . $dim_S0 . '" AND DATEDIFF(Date_Supervision_Demandee,Date_Demande) > 7;');
	$req_anticipJP7_S1->execute(array()) or die(print_r($req_anticipJP7_S1->errorInfo()));
	
	$req_anticipJ_S0 = $bdd_supervision->prepare('
		SELECT
			count(Etat_demande) AS Nbre
		FROM demande
		WHERE Date_Supervision_Demandee > "' . $dim_S0 . '" AND DATEDIFF(Date_Supervision_Demandee,Date_Demande) = 0;');
	$req_anticipJ_S0->execute(array()) or die(print_r($req_anticipJ_S0->errorInfo()));
	
	$req_anticipJ7_S0 = $bdd_supervision->prepare('
		SELECT
			count(Etat_demande) AS Nbre
		FROM demande
		WHERE Date_Supervision_Demandee > "' . $dim_S0 . '" AND DATEDIFF(Date_Supervision_Demandee,Date_Demande) between 1 AND 7;');
	$req_anticipJ7_S0->execute(array()) or die(print_r($req_anticipJ7_S0->errorInfo()));
	
	$req_anticipJP7_S0 = $bdd_supervision->prepare('
			SELECT
			count(Etat_demande) AS Nbre
			FROM demande
			WHERE Date_Supervision_Demandee > "' . $dim_S0 . '" AND DATEDIFF(Date_Supervision_Demandee,Date_Demande) > 7;');
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

	$req_P7J = $bdd_supervision->prepare('
		SELECT
			 Etat_Demande,
			 count(Etat_demande) AS Nbre
		FROM demande 
		WHERE Date_Supervision_Demandee>= "' . $dateP7J . '" AND Etat_Demande IN ("A Traiter","En cours","Validation","Traité") 
		GROUP BY Etat_Demande;');
	$req_P7J->execute(array()) or die(print_r($req_P7J->errorInfo()));

	/*
	 * Traitement backload
	 */
	// demandes datant de plus de 2 mois
	$req_M3 = $bdd_supervision->prepare('
		SELECT
			 CONCAT(FLOOR(sum(temps_hote + temps_service)/60),"h",LPAD(sum(temps_hote + temps_service)%60,2,"00")) AS Temps_Global,
			 count(Etat_demande) AS Nbre
		FROM demande 
		WHERE Date_Supervision_Demandee<"' . $moisNum2 . '" AND Etat_Demande IN ("A Traiter","En cours","Validation");');
	$req_M3->execute(array()) or die(print_r($req_M3->errorInfo()));

	// demandes datant de 2 mois
	$req_M2 = $bdd_supervision->prepare('
		SELECT
			 CONCAT(FLOOR(sum(temps_hote + temps_service)/60),"h",LPAD(sum(temps_hote + temps_service)%60,2,"00")) AS Temps_Global,
			 count(Etat_demande) AS Nbre
		FROM demande
		WHERE Date_Supervision_Demandee>="' . $moisNum2 . '" AND  Date_Supervision_Demandee< "' . $moisNum1 . '" AND Etat_Demande IN ("A Traiter","En cours","Validation");');
	$req_M2->execute(array()) or die(print_r($req_M2->errorInfo()));

	// demandes datant de 1 mois
	$req_M1 = $bdd_supervision->prepare('
		SELECT
			 CONCAT(FLOOR(sum(temps_hote + temps_service)/60),"h",LPAD(sum(temps_hote + temps_service)%60,2,"00")) AS Temps_Global,
			 count(Etat_demande) AS Nbre
		FROM demande
		WHERE Date_Supervision_Demandee>="' . $moisNum1 . '" AND  Date_Supervision_Demandee< "' . $moisNum0 . '" AND Etat_Demande IN ("A Traiter","En cours","Validation");');
	$req_M1->execute(array()) or die(print_r($req_M1->errorInfo()));

	// demandes datant du mois courant
	$req_M0 = $bdd_supervision->prepare('
		SELECT
			 CONCAT(FLOOR(sum(temps_hote + temps_service)/60),"h",LPAD(sum(temps_hote + temps_service)%60,2,"00")) AS Temps_Global,
			 count(Etat_demande) AS Nbre
		FROM demande
		WHERE Date_Supervision_Demandee>="' . $moisNum0 . '" AND Etat_Demande IN ("A Traiter","En cours","Validation");');
	$req_M0->execute(array()) or die(print_r($req_M0->errorInfo()));
	
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
	
	$Nb_M3=0;
	$Nb_M2=0;
	$Nb_M1=0;
	$Nb_M0=0;
	$Tps_M3=0;
	$Tps_M2=0;
	$Tps_M1=0;
	$Tps_M0=0;
	
	while ($res_J = $req_J->fetch())
	{
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
		$Tps_S3=htmlspecialchars($res_S3['Temps_Global']);
	};
	while ($res_S2 = $req_S2->fetch())
	{
		$Nb_S2=htmlspecialchars($res_S2['Nbre']);
		$Tps_S2=htmlspecialchars($res_S2['Temps_Global']);
	};
	while ($res_S1 = $req_S1->fetch())
	{
		$Nb_S1=htmlspecialchars($res_S1['Nbre']);
		$Tps_S1=htmlspecialchars($res_S1['Temps_Global']);
	};
	while ($res_S0 = $req_S0->fetch())
	{
		$Nb_S0=htmlspecialchars($res_S0['Nbre']);
		$Tps_S0=htmlspecialchars($res_S0['Temps_Global']);
	};

	/*
	 * Boucle traitmeent mensuel
	 */
	while ($res_M3 = $req_M3->fetch())
	{
		$Nb_M3=htmlspecialchars($res_M3['Nbre']);
		$Tps_M3=htmlspecialchars($res_M3['Temps_Global']);
	};
	while ($res_M2 = $req_M2->fetch())
	{
		$Nb_M2=htmlspecialchars($res_M2['Nbre']);
		$Tps_M2=htmlspecialchars($res_M2['Temps_Global']);
	};
	while ($res_M1 = $req_M1->fetch())
	{
		$Nb_M1=htmlspecialchars($res_M1['Nbre']);
		$Tps_M1=htmlspecialchars($res_M1['Temps_Global']);
	};
	while ($res_M0 = $req_M0->fetch())
	{
		$Nb_M0=htmlspecialchars($res_M0['Nbre']);
		$Tps_M0=htmlspecialchars($res_M0['Temps_Global']);
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
	
	$contenu_html .="<p class='P2'>Etat des demandes à traiter et en cours.</p> <br />";
	$contenu_html .= "<table border='0' cellspacing='0' cellpadding='3'>
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

	$contenu_html .="<p class='P2'>Nombre de demandes restant à traiter sur les quatres dernières mois</p> <br />";
	$contenu_html .= "<table border='0' cellspacing='0' cellpadding='3'>
                                   <tr><th class='Tableau1_A1'>mois M-3 et plus</th>
                                   <th class='Tableau1_A1'>mois M-2</th>
                                   <th class='Tableau1_A1'>mois M-1</th>
                                   <th class='Tableau1_A1'>mois courant</th>
                                   </tr>";
	$contenu_html .= "<tr>
	 				<td class='Tableau1_A1'>" . $Nb_M3 . "(" . $Tps_M3 . ")</td>
	 				<td class='Tableau1_A1'>" . $Nb_M2 . "(" . $Tps_M2 . ")</td>
	 				<td class='Tableau1_A1'>" . $Nb_M1 . "(" . $Tps_M1 . ")</td>
	 				<td class='Tableau1_A1'>" . $Nb_M0 . "(" . $Tps_M0 . ")</td>
	 			</tr>";
	$contenu_html .= "</table><br />";
	
	$contenu_html .="<p class='P2'>Evolution du nombre de demandes traitées sur les quatres dernières semaines</p> <br />";
	$contenu_html .= "<table border='0' cellspacing='0' cellpadding='3'>
                                   <tr><th class='Tableau1_A1'>semaine S-3</th>
                                   <th class='Tableau1_A1'>semaine S-2</th>
                                   <th class='Tableau1_A1'>semaine S-1</th>
                                   <th class='Tableau1_A1'>semaine S0</th>
                                   </tr>";
	$contenu_html .= "<tr>
	 				<td class='Tableau1_A1'>" . $Nb_S3 . "(" . $Tps_S3 . ")</td>
	 				<td class='Tableau1_A1'>" . $Nb_S2 . "(" . $Tps_S2 . ")</td>
	 				<td class='Tableau1_A1'>" . $Nb_S1 . "(" . $Tps_S1 . ")</td>
	 				<td class='Tableau1_A1'>" . $Nb_S0 . "(" . $Tps_S0 . ")</td>
	 			</tr>";
	$contenu_html .= "</table><br />";

	$contenu_html .="<p class='P2'>Indicateur d'anticipation des demandes</p> <br />";
	$contenu_html .= "<table border='0' cellspacing='0' cellpadding='3'>
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
	While($res_Temps_Global = $req_Temps_Global->fetch())
	{
		$Temps_Global = $res_Temps_Global['Temps_Global'];
	};
	
	$contenu_html .= "<p class='P2'>Liste des demandes à traiter (temps total de traitement estimé " . $Temps_Global . ")</p> <br />";
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
	
				/**
				 * Constitution du corps du mail
				 */
				//=====Définition de l'ogjet.
				$sujet = "[CENTREON] Recapitulatif des demandes de changement en cours.";
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
								<p class='P6'>Liste des demandes de changement à traiter ou en cours de traitement au " . $heure_envoi . ".</p>
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
				$header = "From: \"changement_centreon\"<admin_centreon@tessi.fr>".$passage_ligne;
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
