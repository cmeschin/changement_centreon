<?php
if (session_id()=='')
{
session_start();
};
// si la chaine à décoder contient [A-Za-z]:%5C ou [A-Za-z]:\ (le début d'un chemin windows) alors on remplace à l'affichage tous les %5C (l'antislash) par un slash pour simplifier le copié/collé dans la paramétrage centreon
// à l'enregistrement suivant tous les \ auront été remplacés par des /.
if (preg_match("#^[a-zA-Z][%3A][%5C|/]#",$Valeur_Champ))
//if (ereg("^[:alpha:]:%5C",substr($Valeur_Champ,0,5)))
{
	$Valeur_Champ = str_replace("%5C","/",$Valeur_Champ);
	$Valeur_Champ = str_replace("\\","/",$Valeur_Champ);
};
if ($_SESSION['Admin']==False)
{
	if (preg_match("#_PIPE_#",$Valeur_Champ))
	{
		$Valeur_Champ = str_replace("_PIPE_","|",$Valeur_Champ);
	};
	if (preg_match("#_ESP_#",$Valeur_Champ))
	{
		$Valeur_Champ = str_replace("_ESP_"," ",$Valeur_Champ);
	};
	if (preg_match("#_DOLLAR_#",$Valeur_Champ))
	{
		$Valeur_Champ = str_replace("_DOLLAR_","$",$Valeur_Champ);
	};
	if (preg_match("#_PEX_#",$Valeur_Champ))
	{
		$Valeur_Champ = str_replace("_PEX_","!",$Valeur_Champ);
	};
	if (preg_match("#_PO_#",$Valeur_Champ))
	{
		$Valeur_Champ = str_replace("_PO_","(",$Valeur_Champ);
	};
	if (preg_match("#_PF_#",$Valeur_Champ))
	{
		$Valeur_Champ = str_replace("_PF_",")",$Valeur_Champ);
	};
	if (preg_match("#_DIESE_#",$Valeur_Champ))
	{
		$Valeur_Champ = str_replace("_DIESE_","#",$Valeur_Champ);
	};
	if (preg_match("#_ETOIL_#",$Valeur_Champ))
	{
		$Valeur_Champ = str_replace("_ETOIL_","*",$Valeur_Champ);
	};
	if (preg_match("#_SQUOTE_#",$Valeur_Champ))
	{
		$Valeur_Champ = str_replace("_SQUOTE_","'",$Valeur_Champ);
	};
	if (preg_match("#_DQUOTE_#",$Valeur_Champ))
	{
		$Valeur_Champ = str_replace("_DQUOTE_","\"",$Valeur_Champ);
	};
	
// /**
//  * Gestion antislash en cours de test
//  */
// 	if (preg_match("#_BCKSL_#",$Valeur_Champ))
// 	{
// 		$Valeur_Champ = str_replace("_BCKSL_","\\",$Valeur_Champ);
// 	};
};
$Valeur_Champ = urldecode ($Valeur_Champ); // décodage des caractères spéciaux (encodés dans le fichier fonctions_enregistrer.js via la commande escape())
