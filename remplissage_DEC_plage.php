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
	
// Selection de toutes les plages de la demande
include_once('requete_liste_periode_demande.php');
} catch (Exception $e) {
	die ('Erreur requete_liste_periode_demande: ' . $e->getMessage());
};

/**
 * #21 meilleure gestion de la coloration par ajout de l'id_dem dans les id des balises
 * $NbFieldset_plage est désormais construit avec l'id_demande + le numéro de fieldset courant
 * // $NbFieldset_plage = 1;
 */

$NumFieldset = 1;
while ($res_liste_plage = $req_liste_plage->fetch())
{ 
	/**
	 * #21
	 */
	$NbFieldset_plage = $ID_Demande . "_" . $NumFieldset;
	echo '<fieldset id="Plage' . $NbFieldset_plage . '" class="plage">';
	echo '<legend>Plage horaire n°' . $NumFieldset . '</legend>';
		echo '<div id="model_param_plage">';
			echo '<!-- Nom_Période -->';
			$LongueurArg=  strlen(htmlspecialchars($res_liste_plage['nom_periode']));// + 10;
			echo '<label for="Nom_Plage' . $NbFieldset_plage . '" class="jour">Nom de la plage horaire :</label>';
			echo '<input readonly type="text" id="Nom_Plage' . $NbFieldset_plage . '" name="Nom_Plage' . $NbFieldset_plage . '" value="' . $res_liste_plage['nom_periode'] . '" size="'. $LongueurArg . '" maxlength="30"/>';
			echo ' ';
			$LongueurArg=  strlen(htmlspecialchars($res_liste_plage['type_action']));// + 10;
			echo '<label>Action à effectuer :</label>';
			echo '<input readonly name="Plage_action' . $NbFieldset_plage . '" id="Plage_action' . $NbFieldset_plage . '" value="' . $res_liste_plage['type_action'] . '" size="'. $LongueurArg . '"><br/>';
			echo ' ';
			$LongueurArg=  strlen(htmlspecialchars($res_liste_plage['lundi']));// + 10;
			echo '<!-- Lundi -->';
			echo '<label for="Lundi' . $NbFieldset_plage . '" class="jour">Lundi :</label>';
			echo '<input readonly type="text" id="Lundi' . $NbFieldset_plage . '" name="Lundi' . $NbFieldset_plage . '" value="' . $res_liste_plage['lundi'] . '" size="'. $LongueurArg . '" maxlength="30"/>';
			echo ' ';
			$LongueurArg=  strlen(htmlspecialchars($res_liste_plage['mardi']));// + 10;
			echo '<!-- Mardi -->';
			echo '<label for="Mardi' . $NbFieldset_plage . '" class="jour">Mardi :</label>';
			echo '<input readonly type="text" id="Mardi' . $NbFieldset_plage . '" name="Mardi' . $NbFieldset_plage . '" value="' . $res_liste_plage['mardi'] . '" size="'. $LongueurArg . '" maxlength="30"/>';
			echo ' ';
			$LongueurArg=  strlen(htmlspecialchars($res_liste_plage['mercredi']));// + 10;
			echo '<!-- Mercredi -->';
			echo '<label for="Mercredi' . $NbFieldset_plage . '" class="jour">Mercredi :</label>';
			echo '<input readonly type="text" id="Mercredi' . $NbFieldset_plage . '" name="Mercredi' . $NbFieldset_plage . '" value="' . $res_liste_plage['mercredi'] . '" size="'. $LongueurArg . '" maxlength="30"/><br/>';
			echo ' ';
			$LongueurArg=  strlen(htmlspecialchars($res_liste_plage['jeudi']));// + 10;
			echo '<!-- Jeudi -->';
			echo '<label for="Jeudi' . $NbFieldset_plage . '" class="jour">Jeudi :</label>';
			echo '<input readonly type="text" id="Jeudi' . $NbFieldset_plage . '" name="Jeudi' . $NbFieldset_plage . '" value="' . $res_liste_plage['jeudi'] . '" size="'. $LongueurArg . '" maxlength="30"/>';
			echo ' ';
			$LongueurArg=  strlen(htmlspecialchars($res_liste_plage['vendredi']));// + 10;
			echo '<!-- Vendredi -->';
			echo '<label for="Vendredi' . $NbFieldset_plage . '" class="jour">Vendredi :</label>';
			echo '<input readonly type="text" id="Vendredi' . $NbFieldset_plage . '" name="Vendredi' . $NbFieldset_plage . '" value="' . $res_liste_plage['vendredi'] . '" size="'. $LongueurArg . '" maxlength="30"/>';
			echo ' ';
			$LongueurArg=  strlen(htmlspecialchars($res_liste_plage['samedi']));// + 10;
			echo '<!-- Samedi -->';
			echo '<label for="Samedi' . $NbFieldset_plage . '" class="jour">Samedi :</label>';
			echo '<input readonly type="text" id="Samedi' . $NbFieldset_plage . '" name="Samedi' . $NbFieldset_plage . '" value="' . $res_liste_plage['samedi'] . '" size="'. $LongueurArg . '" maxlength="30"/><br/>';
			echo ' ';
			$LongueurArg=  strlen(htmlspecialchars($res_liste_plage['dimanche']));// + 10;
			echo '<!-- Dimanche -->';
			echo '<label for="Dimanche' . $NbFieldset_plage . '" class="jour">Dimanche :</label>';
			echo '<input readonly type="text" id="Dimanche' . $NbFieldset_plage . '" name="Dimanche' . $NbFieldset_plage . '" value="' . $res_liste_plage['dimanche'] . '" size="'. $LongueurArg . '" maxlength="30"/><br/>';
			echo '<!-- Commentaire -->';
			echo '<label for="Commentaire_Demande' . $NbFieldset_plage . '">Commentaire :</label>';
			echo '<textarea readonly id="Commentaire_Demande' . $NbFieldset_plage . '" name="Commentaire_Demande' . $NbFieldset_plage . '" rows="3" cols="50"> </textarea> <br />';
		echo '</div>';
		if ($_SESSION['Admin'] == True) // si admin affichage liste déroulante etat + bouton enregistrer
		{
			$ID_Plage = htmlspecialchars($res_liste_plage['id_periode_temporelle']);
			include('insere_fieldset_Admin_Plage.php');
		};
	echo '</fieldset>';

	/**
	 * #21
	 *$NbFieldset_plage ++; 
	 */
	$NumFieldset ++;
};
$Statut_Plage=true;
