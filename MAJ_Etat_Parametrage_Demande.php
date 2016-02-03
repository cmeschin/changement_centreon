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
	$Etat_Param = (isset($_POST["Etat_Param"])) ? htmlspecialchars($_POST["Etat_Param"]) : NULL;
	$Annulation = (isset($_POST["Annulation"])) ? htmlspecialchars($_POST["Annulation"]) : NULL;
	addlog("Etat_Param=".$Etat_Param . "\nMotif_Annulation=". $Annulation . "...");
	
	if (($ID_Demande != NULL) && ($Etat_Param != NULL) && ($Etat_Param != "Supprimer"))
	{
		if (($Etat_Param == "Traité") || ($Etat_Param == "Annulé"))
		{
			$MAJ_Demande = $bdd_supervision->prepare('UPDATE demande SET Etat_Demande= :Etat_Param, Date_Fin_Traitement = :Date_Fin_Traitement, motif_annulation = :motif_annulation WHERE ID_Demande= :ID_Demande;');
			$MAJ_Demande->execute(array(
				'Etat_Param' => $Etat_Param,
				'Date_Fin_Traitement' => date("Y-m-d H:i:s"),
				'motif_annulation' => $Annulation,
				'ID_Demande' => $ID_Demande
			)) or die(print_r($MAJ_Demande->errorInfo()));
			
		} else
		{
			$MAJ_Demande = $bdd_supervision->prepare('UPDATE demande SET Etat_Demande= :Etat_Param WHERE ID_Demande= :ID_Demande;');
			$MAJ_Demande->execute(array(
				'Etat_Param' => $Etat_Param,
				'ID_Demande' => $ID_Demande
			)) or die(print_r($MAJ_Demande->errorInfo()));
		};
		$MAJ_Hote = $bdd_supervision->prepare('UPDATE hote SET Etat_Parametrage= :Etat_Param WHERE ID_Demande= :ID_Demande AND Etat_Parametrage<>"Annulé";');
		$MAJ_Hote->execute(array(
			'Etat_Param' => $Etat_Param,
			'ID_Demande' => $ID_Demande
			)) or die(print_r($MAJ_Hote->errorInfo()));
	
		$MAJ_Service = $bdd_supervision->prepare('UPDATE service SET Etat_Parametrage= :Etat_Param WHERE ID_Demande= :ID_Demande AND Etat_Parametrage<>"Annulé";');
		$MAJ_Service->execute(array(
			'Etat_Param' => $Etat_Param,
			'ID_Demande' => $ID_Demande
			)) or die(print_r($MAJ_Service->errorInfo()));
	
		$MAJ_Plage = $bdd_supervision->prepare('UPDATE periode_temporelle SET Etat_Parametrage= :Etat_Param WHERE ID_Demande= :ID_Demande AND Etat_Parametrage<>"Annulé";');
		$MAJ_Plage->execute(array(
			'Etat_Param' => $Etat_Param,
			'ID_Demande' => $ID_Demande
			)) or die(print_r($MAJ_Plage->errorInfo()));
		// traitement pour envoi du mail en fonction du statut de la demande
		// vérification si mail <en cours> est à envoyer
		
		$req_demande = $bdd_supervision->prepare('SELECT mail_creation, mail_encours, mail_finalise, mail_traite, mail_annule FROM demande WHERE ID_Demande= :ID_Demande');
		$req_demande->execute(Array(
				'ID_Demande' => $ID_Demande
		)) or die(print_r($req_demande->errorInfo()));
		while ($res_demande = $req_demande->fetch())
		{
			if ((htmlspecialchars($res_demande['mail_creation']) == False) && ($Etat_Param == "A Traiter"))
			{ // si mail_creation n'est pas coché, on envoie le mail
				addlog("MAJ_ETAT_PARAMETRAGE_DEM:" . $ID_Demande . " Envoi du mail creation.");
				// A activer lorsque l'automate SUSI sera en place
							include('envoi_mail.php');
			}else if ((htmlspecialchars($res_demande['mail_encours']) == False) && ($Etat_Param == "En cours"))
			{
				addlog("MAJ_ETAT_PARAMETRAGE_DEM:" . $ID_Demande . " Envoi du mail encours.");
				// A activer lorsque l'automate SUSI sera en place
							include('envoi_mail_encours.php');
			}else if ((htmlspecialchars($res_demande['mail_finalise']) == False) && ($Etat_Param == "Validation"))
			{
				addlog("MAJ_ETAT_PARAMETRAGE_DEM:" . $ID_Demande . " Envoi du mail finalise.");
				// A activer lorsque l'automate SUSI sera en place
							include('envoi_mail_finalise.php');
			}else if ((htmlspecialchars($res_demande['mail_traite']) == False) && ($Etat_Param == "Traité"))
			{
				addlog("MAJ_ETAT_PARAMETRAGE_DEM:" . $ID_Demande . " Envoi du mail traite.");
				// A activer lorsque l'automate SUSI sera en place
							include('envoi_mail_traite.php');
			}else if ((htmlspecialchars($res_demande['mail_annule']) == False) && ($Etat_Param == "Annulé"))
			{
				addlog("MAJ_ETAT_PARAMETRAGE_DEM:" . $ID_Demande . " Envoi du mail annule.");
				// A activer lorsque l'automate SUSI sera en place
							include('envoi_mail_annule.php');
			};
		};
		
	} else if (($ID_Demande != NULL) && ($Etat_Param == "Supprimer"))
	{
			$DEL_Dem_Service = $bdd_supervision->prepare('DELETE FROM service WHERE ID_Demande= :ID_Demande;');
			$DEL_Dem_Service->execute(array(
					'ID_Demande' => $ID_Demande
			)) or die(print_r($DEL_Dem_Service->errorInfo()));
		
			$DEL_Dem_Plage = $bdd_supervision->prepare('DELETE FROM periode_temporelle WHERE ID_Demande= :ID_Demande;');
			$DEL_Dem_Plage->execute(array(
					'ID_Demande' => $ID_Demande
			)) or die(print_r($DEL_Dem_Plage->errorInfo()));
		
			$DEL_Dem_Hote = $bdd_supervision->prepare('DELETE FROM hote WHERE ID_Demande= :ID_Demande;');
			$DEL_Dem_Hote->execute(array(
					'ID_Demande' => $ID_Demande
			)) or die(print_r($DEL_Dem_Hote->errorInfo()));
		
			$DEL_Dem_HoteTmp = $bdd_supervision->prepare('DELETE FROM hote_temp WHERE ID_Demande= :ID_Demande;');
			$DEL_Dem_HoteTmp->execute(array(
					'ID_Demande' => $ID_Demande
			)) or die(print_r($DEL_Dem_HoteTmp->errorInfo()));
		
			$DEL_Demande = $bdd_supervision->prepare('DELETE FROM demande WHERE ID_Demande= :ID_Demande;');
			$DEL_Demande->execute(array(
					'ID_Demande' => $ID_Demande
			)) or die(print_r($DEL_Demande->errorInfo()));
	} else 
	{
		echo "echec MAJ Paramétrage demande: ID_Demande=[" . $ID_Demande . "], Etat_Param=[" . $Etat_Param . "]!";
	};
	$bdd_supervision->commit();
} catch (Exception $e) {
	$bdd_supervision->rollBack();
	die('Erreur MAJ etat parametrage demande: ' . $e->getMessage());
};
