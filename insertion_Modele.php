<?php
session_start();
header("Content-Type: text/plain"); // Utilisation d'un header pour spécifier le type de contenu de la page. Ici, il s'agit juste de texte brut (text/plain).
include('log.php'); // chargement de la fonction de log
addlog("Chargement insertion_selection.php");
try {
	include_once('connexion_sql_supervision.php');
	$bdd_supervision->beginTransaction();
	$sModele_valeur = (isset($_POST["Modele_valeur"])) ? $_POST["Modele_valeur"] : NULL;
	$Modele_valeur = explode("$",$sModele_valeur); // découpe la chaine en tableau avec comme séparateur le $
	
	// Verification sur l'existence d'un nom identique
	$select_Modele = $bdd_supervision->prepare('SELECT count(Modele_Service) FROM modele_service WHERE Modele_Service = :Modele_Service');
	$select_Modele->execute(Array(
		'Modele_Service' => htmlspecialchars($Modele_valeur[1])
	)) or die(print_r($select_Modele->errorInfo()));
	while($res_select = $select_Modele->fetch())
	{
		$NbModele = $res_select[0];
	}
	if ($NbModele != 0 AND $Modele_valeur[0]==0) // le nom du modèle existe déjà et la sélection est "nouveau" dans la liste
	{
		echo "Erreur: Le nom [" . htmlspecialchars($Modele_valeur[1]) . "] existe déjà!";
		addlog("Erreur: Le nom [" . htmlspecialchars($Modele_valeur[1]) . "] existe déjà!");
	}else
	{
		if ($Modele_valeur[0]==0) // il s'agit d'un nouveau modèle
		{
			$insert_Modele_valeur = $bdd_supervision->prepare('INSERT INTO modele_service (Modele_Service, MS_Description, MS_Libelles, MS_Arguments, MS_Macro, MS_EST_MACRO) VALUES(:Modele_Service, :MS_Description, :MS_Libelles, :MS_Arguments, :MS_Macro, :MS_EST_MACRO)');
			$insert_Modele_valeur->execute(array(
				'Modele_Service' => htmlspecialchars($Modele_valeur[1]),
				'MS_Description' => htmlspecialchars($Modele_valeur[2]),
				'MS_Libelles' => htmlspecialchars($Modele_valeur[3]),
				'MS_Arguments' => htmlspecialchars($Modele_valeur[4]),
				'MS_Macro' => htmlspecialchars($Modele_valeur[5]),
				'MS_EST_MACRO' => htmlspecialchars($Modele_valeur[6])
			)) or die(print_r($insert_Modele_valeur->errorInfo()));
			
			echo "Le modèle de service " . htmlspecialchars($Modele_valeur[1]) . " a été correctement enregistré.";
			//addlog(print_r($Modele_valeur)); // pollue le message de sortie
			addlog("modèle " . htmlspecialchars($Modele_valeur[1]) . " enregistré OK");
		}
		else // il s'agit d'un modèle existant
		{
			$upd_Modele_valeur = $bdd_supervision->prepare('UPDATE modele_service SET Modele_Service = :Modele_Service, MS_Description = :MS_Description, MS_Libelles = :MS_Libelles, MS_Arguments = :MS_Arguments, MS_Macro = :MS_Macro, MS_EST_MACRO = :MS_EST_MACRO WHERE ID_Modele_Service = :ID_Modele_Service');
			$upd_Modele_valeur->execute(array(
				'Modele_Service' => htmlspecialchars($Modele_valeur[1]),
				'MS_Description' => htmlspecialchars($Modele_valeur[2]),
				'MS_Libelles' => htmlspecialchars($Modele_valeur[3]),
				'MS_Arguments' => htmlspecialchars($Modele_valeur[4]),
				'MS_Macro' => htmlspecialchars($Modele_valeur[5]),
				'MS_EST_MACRO' => htmlspecialchars($Modele_valeur[6]),
				'ID_Modele_Service' => htmlspecialchars($Modele_valeur[0])
				)) or die(print_r($upd_Modele_valeur->errorInfo()));
			
			echo "Le modèle de service [" . htmlspecialchars($Modele_valeur[1]) . "] ID=[" . htmlspecialchars($Modele_valeur[0]) . "] a été correctement mis à jour.";
			addlog("Le modèle de service [" . htmlspecialchars($Modele_valeur[1]) . "] ID=[" . htmlspecialchars($Modele_valeur[0]) . "] a été correctement mis à jour.");
			//addlog(print_r($Modele_valeur)); // pollue le message de sortie
		};
	};
	$bdd_supervision->commit();
} catch (Exception $e) {
	$bdd_supervision->rollBack();
	die('Erreur insertion modele de service: ' . $e->getMessage());
};