<?php
if (session_id()=='')
{
session_start();
};
if ($_SESSION['R_ID_Demande'] == NULL)
{
	$ID_Demande = (isset($_POST["ID_Dem"])) ? $_POST["ID_Dem"] : NULL;
} else 
{
	$ID_Demande = $_SESSION['R_ID_Demande'];
}

include_once('connexion_sql_supervision.php');

try {
// Selection des infos de la demande
	include_once('requete_liste_infos_demande.php');
} catch (Exception $e) {
	die('Erreur requete_liste_infos_demande: ' . $e->getMessage());
};

$NbFieldset_Infos = 1;
while ($res_liste_infos = $req_liste_infos->fetch())
{ 
	echo '<fieldset id="Infos' . $NbFieldset_Infos . '" class="infos">';
		echo '<legend>Infos</legend>';
		echo '<!-- Liste diffusion -->';
		echo '<label for="Email' . $NbFieldset_Infos . '">Liste de diffusion :</label>';
		$LongueurArg=  strlen(htmlspecialchars($res_liste_infos['email']));
		echo '<input readonly type="text" id="Email' . $NbFieldset_Infos . '" name="Email' . $NbFieldset_Infos . '" value="' . htmlspecialchars($res_liste_infos['email']) . '" size="'. $LongueurArg . '"/>';
		echo '<!-- Date Prise en compte -->';
		echo '<label for="Date_PEC' . $NbFieldset_Infos . '">Date de prise en compte :</label>';
		//$LongueurArg=  strlen(htmlspecialchars($res_liste_infos[9])) + 10;
		echo '<input readonly type="text" id="Date_PEC' . $NbFieldset_Infos . '" name="Date_PEC' . $NbFieldset_Infos . '" value="' . htmlspecialchars($res_liste_infos['Date_PEC']) . '" size="15"/>';
		echo '<!-- Date Fin de traitement -->';
		echo '<label for="Date_Fin_Traitement' . $NbFieldset_Infos . '">Date de fin de traitement :</label>';
		//$LongueurArg=  strlen(htmlspecialchars($res_liste_infos[9])) + 10;
		echo '<input readonly type="text" id="Date_Fin_Traitement' . $NbFieldset_Infos . '" name="Date_Fin_Traitement' . $NbFieldset_Infos . '" value="' . htmlspecialchars($res_liste_infos['Date_Fin_Traitement']) . '" size="15"/> <br/>';
		echo '<!-- Commentaire -->';
		echo '<label for="Commentaire_DEM' . $NbFieldset_Infos . '">Commentaire :</label>';
		//$LongueurArg=  strlen(htmlspecialchars($res_liste_infos[9])) + 10;
		echo '<textarea readonly type="text" id="Commentaire_DEM' . $NbFieldset_Infos . '" name="Commentaire_DEM' . $NbFieldset_Infos . '" rows="3" cols="50">' . htmlspecialchars($res_liste_infos['Commentaire']) . '</textarea> <br/>';
		
	echo '</fieldset>';
	$NbFieldset_Infos++;
};
$Statut_Infos=true;
