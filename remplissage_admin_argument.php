<?php
session_start();
//$ID_Modele = (isset($_POST["ID_Modele"])) ? $_POST["ID_Modele"] : NULL;
$sModele_Service = (isset($_POST["Modele_Service"])) ? $_POST["Modele_Service"] : NULL;
// récupération de la ref demande

include_once('connexion_sql_supervision.php');
try {
//$req_liste_valeur = $bdd_supervision->prepare('SELECT Modele_Service, MS_Description, MS_Libelles, MS_Arguments FROM Modele_Service WHERE ID_Modele_Service= :ID_Modele');
$req_liste_valeur = $bdd_supervision->prepare('SELECT Modele_Service, MS_Description, MS_Libelles, MS_Arguments, MS_Macro, MS_EST_MACRO FROM modele_service WHERE Modele_Service= :Modele_Service');
$req_liste_valeur->execute(Array(
//	'ID_Modele' => $ID_Modele
	'Modele_Service' => $sModele_Service
	)) or die(print_r($req_liste_valeur->errorInfo()));
} catch (Exception $e) {
	die('Erreur remplissage admin argument: ' . $e->getMessage());
};

//$Nb_Hote = count($req_liste_hote);
//$tableau[$j]=$liste_hote;
//$chaine=implode(";",$liste_hote[$j]);
$liste_valeur = "";
while ($res_liste_valeur = $req_liste_valeur->fetch())
{ 
	$liste_valeur = $liste_valeur . htmlspecialchars($res_liste_valeur['Modele_Service']) . '$'; // Modele_Service
	$liste_valeur = $liste_valeur . htmlspecialchars($res_liste_valeur['MS_Description']) . '$'; // MS_Description
	$liste_valeur = $liste_valeur . htmlspecialchars($res_liste_valeur['MS_Libelles']) . '$'; // MS_Libelles
	$liste_valeur = $liste_valeur . htmlspecialchars($res_liste_valeur['MS_Arguments']) . '$'; // MS_Arguments
	$liste_valeur = $liste_valeur . htmlspecialchars($res_liste_valeur['MS_Macro']) . '$'; // MS_Macro
	$liste_valeur = $liste_valeur . htmlspecialchars($res_liste_valeur['MS_EST_MACRO']) . '$'; // MS_EST_MACRO
};
$liste_valeur = rtrim($liste_valeur,'$');
$result=$liste_valeur;
echo $liste_valeur;
