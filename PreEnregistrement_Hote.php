<?php
session_start();
header("Content-Type: text/plain"); // Utilisation d'un header pour spécifier le type de contenu de la page. Ici, il s'agit juste de texte brut (text/plain).
include('log.php'); // chargement de la fonction de log
addlog("Chargement PreEnregistrement_Hote.php");

include_once('connexion_sql_supervision.php');
try {
	$bdd_supervision->beginTransaction();
	$sliste_hote = (isset($_POST["liste_hote"])) ? $_POST["liste_hote"] : NULL;
	
	$liste_hote = explode("$",$sliste_hote); // découpe la chaine en tableau avec comme séparateur le $
	// chaque tableau devra être redécoupé pour mise à jour unitaire
	
	$ID_Demande = htmlspecialchars($_SESSION['ID_dem']);
	
	if ($liste_hote[0] != "") 
	{
		// Calcul du nombre d'hôte à mettre à jour
		$NbHote = count($liste_hote);
		addlog("NbHote=".$NbHote);
		for ($i = 0;$i<$NbHote;$i++)
		{
			addlog("liste_hote=".($liste_hote[$i]));
			$liste_T_hote = explode("|",$liste_hote[$i]);
			addlog("Nom_hote=".($liste_T_hote[0]));
	
			addlog("INSERT Table Hote");
			$MAJ_hote = $bdd_supervision->prepare('INSERT IGNORE INTO hote 
				(Nom_Hote, ID_Demande, Description, IP_Hote, Type_Hote, ID_Localisation, OS, Architecture, Langue, Fonction, Controle_Actif, Commentaire, Consigne, Detail_Consigne, Type_Action, selection)
				VALUES (:Nom_Hote, :ID_Demande, :Description, :IP_Hote, :Type_Hote, :ID_Localisation, :OS, :Architecture, :Langue, :Fonction, :Controle_Actif, :Commentaire, :Consigne, :Detail_Consigne, :Type_Action, :selection)');
			$MAJ_hote->execute(array(
				'Nom_Hote' => htmlspecialchars($liste_T_hote[0]),
				'ID_Demande' => $ID_Demande,
				'Description' => htmlspecialchars($liste_T_hote[2]),
				'IP_Hote' => htmlspecialchars($liste_T_hote[1]),
				'Type_Hote' => htmlspecialchars($liste_T_hote[4]),
				'ID_Localisation' => htmlspecialchars($liste_T_hote[3]),
				'OS' => htmlspecialchars($liste_T_hote[5]), // récupérer la valeur texte pour affichage
				'Architecture' => htmlspecialchars($liste_T_hote[6]),
				'Langue' => htmlspecialchars($liste_T_hote[7]),
				'Fonction' => htmlspecialchars($liste_T_hote[8]),
				'Controle_Actif' => htmlspecialchars($liste_T_hote[11]),
				'Commentaire' => htmlspecialchars($liste_T_hote[13]),
				'Consigne' => htmlspecialchars($liste_T_hote[9]),
				'Detail_Consigne' => htmlspecialchars($liste_T_hote[10]),
				'Type_Action' => htmlspecialchars($liste_T_hote[12]),
				'selection' => "false"
				)) or die(print_r($MAJ_hote->errorInfo()));
			addlog("Nom_Hote=" . htmlspecialchars($liste_T_hote[0]));
			addlog("IP_Hote=" . htmlspecialchars($liste_T_hote[1]));
		};
	};
	
	$MAJ_Hote = $bdd_supervision->prepare('UPDATE hote SET Etat_Parametrage= "A Traiter" WHERE ID_Demande= :ID_Demande;');
	$MAJ_Hote->execute(array(
		'ID_Demande' => $ID_Demande
		)) or die(print_r($MAJ_Hote->errorInfo()));
	
	$req_ID_Hote = $bdd_supervision->prepare('SELECT ID_Hote FROM hote WHERE Nom_Hote= :nom_hote AND IP_Hote= :ip_hote AND ID_Demande= :id_demande');
	$req_ID_Hote->execute(array(
			'nom_hote' => htmlspecialchars($liste_T_hote[0]),
			'ip_hote' => htmlspecialchars($liste_T_hote[1]),
			'id_demande' => htmlspecialchars($ID_Demande)
	)) or die(print_r($req_ID_Hote->errorInfo()));
	
	$res_ID_Hote = $req_ID_Hote->fetchall();
	foreach ($res_ID_Hote AS $valeur)
	{ // on boucle sur les valeurs remontée par la requête 
		$ID_Hote=$valeur[0];
	};
	/**
	 * Mise à jour de la variable Timer
	 */
	$date=date_create();
	$_SESSION['Timer']=date_timestamp_get($date);
	echo $ID_Hote;
	$bdd_supervision->commit();
} catch (Exception $e) {
	$bdd_supervision->rollBack();
	die('Erreur PreEnregistrement_Hote: ' . $e->getMessage());
};
