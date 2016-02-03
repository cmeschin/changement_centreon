<?php
if (session_id()=='')
{
session_start();
};
header("Content-Type: text/plain"); // Utilisation d'un header pour spécifier le type de contenu de la page. Ici, il s'agit juste de texte brut (text/plain).
include('log.php'); // chargement de la fonction de log
addlog("Chargement MAJ_Etat_Parametrage.php");

include_once('connexion_sql_supervision.php');
try {
	$bdd_supervision->beginTransaction();
	$ID_Demande = (isset($_POST["ID_Demande"])) ? htmlspecialchars($_POST["ID_Demande"]) : NULL;
	$ID_Hote = (isset($_POST["ID_Hote"])) ? htmlspecialchars($_POST["ID_Hote"]) : NULL;
	$ID_Service = (isset($_POST["ID_Service"])) ? htmlspecialchars($_POST["ID_Service"]) : NULL;
	$ID_Plage = (isset($_POST["ID_Plage"])) ? htmlspecialchars($_POST["ID_Plage"]) : NULL;
	$Etat_Param = (isset($_POST["Etat_Param"])) ? htmlspecialchars($_POST["Etat_Param"]) : NULL;
	$Annulation = (isset($_POST["Annulation"])) ? htmlspecialchars($_POST["Annulation"]) : NULL;
	
	$MAJ_OK=False;
	addlog("MAJ Paramétrage à effectuer: ID_Demande=[" . $ID_Demande . "], ID_Hote=[" . $ID_Hote . "], ID_Service=[" . $ID_Service . "], ID_Plage=[" . $ID_Plage . "], Etat_Param=[" . $Etat_Param . "], Motif_Annulation=[" . $Annulation . "]!");
	if ($ID_Hote != NULL) 
	{
		$MAJ_Hote = $bdd_supervision->prepare('UPDATE hote SET Etat_Parametrage= :Etat_Param, motif_annulation= :motif_annulation WHERE ID_Hote = :ID_Hote AND ID_Demande= :ID_Demande;');
		$MAJ_Hote->execute(array(
			'Etat_Param' => $Etat_Param,
			'motif_annulation' => $Annulation,
			'ID_Hote' => $ID_Hote,
			'ID_Demande' => $ID_Demande
			)) or die(print_r($MAJ_Hote->errorInfo()));
		$MAJ_OK=True;
	} else if ($ID_Service != NULL) 
	{
		$MAJ_Service = $bdd_supervision->prepare('UPDATE service SET Etat_Parametrage= :Etat_Param, motif_annulation= :motif_annulation WHERE ID_Service = :ID_Service AND ID_Demande= :ID_Demande;');
		$MAJ_Service->execute(array(
			'Etat_Param' => $Etat_Param,
			'motif_annulation' => $Annulation,
			'ID_Service' => $ID_Service,
			'ID_Demande' => $ID_Demande
			)) or die(print_r($MAJ_Service->errorInfo()));
		$MAJ_OK=True;
	} else if ($ID_Plage != NULL) 
	{
		$MAJ_Plage = $bdd_supervision->prepare('UPDATE periode_temporelle SET Etat_Parametrage= :Etat_Param, motif_annulation= :motif_annulation WHERE ID_Periode_Temporelle = :ID_Plage AND ID_Demande= :ID_Demande;');
		$MAJ_Plage->execute(array(
			'Etat_Param' => $Etat_Param,
			'motif_annulation' => $Annulation,
			'ID_Plage' => $ID_Plage,
			'ID_Demande' => $ID_Demande
			)) or die(print_r($MAJ_Plage->errorInfo()));
		$MAJ_OK=True;
	} else
	{
		echo "echec MAJ Paramétrage: ID_Demande=[" . $ID_Demande . "], ID_Hote=[" . $ID_Hote . "], ID_Service=[" . $ID_Service . "], ID_Plage=[" . $ID_Plage . "], Etat_Param=[" . $Etat_Param . "], Motif_Annulation=[" . $Annulation . "]!";
		$MAJ_OK=False;
	};
	
	if (($MAJ_OK == True) && htmlspecialchars($Etat_Param)=="En cours")
	{ // si on a mis à jour une sonde dans le statut "En cours", on passe automatiquement la demande en statut "En cours" si elle est "A Traiter"
		$MAJ_Demande = $bdd_supervision->prepare('UPDATE demande SET Etat_Demande= :Etat_Param, Date_PEC = :Date_PEC WHERE ID_Demande= :ID_Demande AND Etat_Demande="A Traiter";');
		$MAJ_Demande->execute(array(
			'Etat_Param' => htmlspecialchars($Etat_Param),
			'Date_PEC' => htmlspecialchars(date("Y-m-d H:i:s")),
			'ID_Demande' => htmlspecialchars($ID_Demande)
			)) or die(print_r($MAJ_Demande->errorInfo()));
		// vérification si mail <en cours> est à envoyer
		$req_demande = $bdd_supervision->prepare('SELECT mail_encours FROM demande WHERE ID_Demande= :ID_Demande');
		$req_demande->execute(Array(
			'ID_Demande' => htmlspecialchars($ID_Demande)
			)) or die(print_r($req_demande->errorInfo()));
		while ($res_demande = $req_demande->fetch())
		{
			if (htmlspecialchars($res_demande['mail_encours']) == False)
			{ // si mail_encours n'est pas coché, on envoie le mail
				addlog("MAJ_ETAT_PARAMETRAGE_DEM:" . $ID_Demande . " Envoi du mail encours.");
				include('envoi_mail_encours.php');		
			};
		};
	};
	$bdd_supervision->commit();
} catch (Exception $e) {
	$bdd_supervision->rollBack();
	http_response_code(500);
	die('Erreur MAJ Etat parametrage: ' . $e->getMessage());
};