<?php
session_start();
header("Content-Type: text/plain"); // Utilisation d'un header pour spécifier le type de contenu de la page. Ici, il s'agit juste de texte brut (text/plain).
include('log.php'); // chargement de la fonction de log
addlog("Chargement PreEnregistrement_Plage.php");

include_once('connexion_sql_supervision.php');
try {
	$bdd_supervision->beginTransaction();
	$sliste_plage = (isset($_POST["liste_plage"])) ? $_POST["liste_plage"] : NULL;
	
	$liste_plage = explode("$",$sliste_plage); // découpe la chaine en tableau avec comme séparateur le $
	// chaque tableau devra être redécoupé pour mise à jour unitaire
	
	$ID_Demande = htmlspecialchars($_SESSION['ID_dem']);
	
	if ($liste_plage[0] != "") 
	{
		// Calcul du nombre de plage à mettre à jour
		$NbPlage = count($liste_plage);
		addlog("NbPlage=".$NbPlage);
		for ($i = 0;$i<$NbPlage;$i++)
		{
			addlog("liste_plage=".($liste_plage[$i]));
			$liste_T_plage = explode("|",$liste_plage[$i]);
			addlog("Nom_plage=".($liste_T_plage[0]));
	
			addlog("INSERT Table Periode Temporelle");
			$MAJ_plage = $bdd_supervision->prepare('INSERT IGNORE INTO periode_temporelle 
				(Nom_Periode, Lundi, Mardi, Mercredi, Jeudi, Vendredi, Samedi, Dimanche, Type_Action, ID_Demande)
				VALUES (:Nom_Periode, :Lundi, :Mardi, :Mercredi, :Jeudi, :Vendredi, :Samedi, :Dimanche, :Type_Action, :ID_Demande)');
			$MAJ_plage->execute(array(
				'Nom_Periode' => htmlspecialchars($liste_T_plage[0]),
				'Lundi' => htmlspecialchars($liste_T_plage[1]),
				'Mardi' => htmlspecialchars($liste_T_plage[2]),
				'Mercredi' => htmlspecialchars($liste_T_plage[3]),
				'Jeudi' => htmlspecialchars($liste_T_plage[4]),
				'Vendredi' => htmlspecialchars($liste_T_plage[5]),
				'Samedi' => htmlspecialchars($liste_T_plage[6]),
				'Dimanche' => htmlspecialchars($liste_T_plage[7]),
				'Type_Action' => htmlspecialchars($liste_T_plage[8]),
				'ID_Demande' => htmlspecialchars($ID_Demande)
				)) or die(print_r($MAJ_plage->errorInfo()));
			addlog(print_r($MAJ_plage));
		};
	};
	
	$MAJ_plage = $bdd_supervision->prepare('UPDATE periode_temporelle SET Etat_Parametrage= "A Traiter" WHERE ID_Demande= :ID_Demande;');
	$MAJ_plage->execute(array(
		'ID_Demande' => $ID_Demande
		)) or die(print_r($MAJ_Plage->errorInfo()));
	/**
	 * Mise à jour de la variable Timer
	 */
	$date=date_create();
	$_SESSION['Timer']=date_timestamp_get($date);
	$bdd_supervision->commit();
} catch (Exception $e) {
	$bdd_supervision->rollBack();
	die('Erreur PreEnregistrement_Plage: ' . $e->getMessage());
};
