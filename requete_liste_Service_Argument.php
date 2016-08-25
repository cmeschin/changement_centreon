<?php
 header("Content-Type: text/plain"); // Utilisation d'un header pour spécifier le type de contenu de la page. Ici, il s'agit juste de texte brut (text/plain).
include_once('connexion_sql_supervision.php');
$Modele = (isset($_POST["sModele"])) ? $_POST["sModele"] : NULL;
$Modele_id = (isset($_POST["sModele_id"])) ? $_POST["sModele_id"] : NULL;
/**
 *  extraction numéro service pour class
 */
$NbFieldset_Service = substr($Modele_id,14); // récupère le chiffre après Modele
$T_Argument[0]="";
if (($Modele) && ($Modele_id)) // Si un modèle est transmis
{
    /**
     *  récupérer la liste des libelles et des arguments et générer le formulaire
     */
	try 
	{
		$req_Service_Arg = $bdd_supervision->prepare('SELECT
				 MS_Libelles,
				 MS_Arguments,
				 MS_Description
				 FROM modele_service
				 WHERE ID_Modele_Service= :Modele');
		$req_Service_Arg->execute(array(
				'Modele' => htmlspecialchars($Modele)
		)) or die(print_r($req_Service_Arg->errorInfo()));
		
	} catch (Exception $e) 
	{
		die('Erreur requete_liste_Service_Argument: ' . $e->getMessage());
	};
	
	while ($res_Service_Arg = $req_Service_Arg->fetch())
	{
		$T_Libelle = explode('!',$res_Service_Arg[0]);
		$T_Argument_Mod = explode('!',$res_Service_Arg[1]);
		$Description = $res_Service_Arg[2];
	};
	/**
	 *  on compte le nb enregistrement
	 *  et on génère un tableau vide
	 */
	$nbLibelle=count($T_Libelle); // vérifier la nécessité
	for ( $i=0;$i<$nbLibelle;$i++)
	{
		$T_Argument[$i]="";
	};
	
	include('gestion_affichage_arguments.php');
} else // Si aucun modèle n'est transmis
{
	$T_Libelle[0] = "Libellé 1";
	$T_Argument_Mod[0] = "Exemple argument";
};
