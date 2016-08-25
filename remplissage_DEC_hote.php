<?php
if (session_id()=='')
{
	session_start();
};
$ID_Demande = (isset($_POST["ID_Demande"])) ? $_POST["ID_Demande"] : $ID_Demande;

include_once('connexion_sql_supervision.php');
try 
{
	include_once('requete_liste_hote_demande.php');
} catch (Exception $e) {
	die('Erreur requete liste hote_demande: ' . $e->getMessage());
};

/**
 * #21 meilleure gestion de la coloration par ajout de l'id_dem dans les id des balises
 * $NbFieldset est désormais construit avec l'id_demande + le numéro de fieldset courant
 * // $NbFieldset = 1;
 */

$NumFieldset = 1;

while ($res_liste_hote = $req_liste_hote->fetch())
{ 
	/**
	 * #21
	 */
	$NbFieldset = $ID_Demande . "_" . $NumFieldset;
	echo '<fieldset id="Hote' . $NbFieldset . '" class="hote">';
		echo '<legend>Hôte n°' . $NumFieldset . '</legend>';
		echo '<!-- Hote -->';
		echo '<div id="model_param_hote">';
			echo '<label for="Nom_Hote' . $NbFieldset . '">Nom de l\'hôte :</label>';
			echo '<input readonly type="text" id="Nom_Hote' . $NbFieldset . '" name="Nom_Hote' . $NbFieldset . '" value="' . htmlspecialchars($res_liste_hote['Nom_Hote']) . '" size="20" length="20"/> <br/>';
			echo '<!-- Adresse IP -->';
			echo '<label for="IP_Hote' . $NbFieldset . '">Adresse IP :</label>';
			echo '<input readonly type="text" id="IP_Hote' . $NbFieldset . '" name="IP_Hote' . $NbFieldset . '" value="' . htmlspecialchars($res_liste_hote['IP_Hote']) . '"/>';
		echo '</div>';
		echo '<!-- Description -->';
		echo '<div id="model_param_hote">';
			echo '<label for="Hote_Description' . $NbFieldset . '">Description :</label>';
			echo '<textarea readonly id="Hote_Description' . $NbFieldset . '" name="Hote_Description' . $NbFieldset . '" rows="2" cols="40" >' . htmlspecialchars($res_liste_hote['Description']) . '</textarea>';
		echo '</div> <br />';
		echo '<!-- Localisation -->';
		echo '<label for="Localisation' . $NbFieldset . '">Localisation :</label>';
		echo '<input readonly name="Localisation' . $NbFieldset . '" id="Localisation' . $NbFieldset . '" value="' . htmlspecialchars($res_liste_hote['ID_Localisation']) . '"/>  <!-- Liste Localisation -->';
		echo '<!-- Type Hote -->';
		echo '<label for="Type_Hote' . $NbFieldset . '">Type :</label>';
		echo '<input readonly name="Type_Hote' . $NbFieldset . '" id="Type_Hote' . $NbFieldset . '" value="' . htmlspecialchars($res_liste_hote['Type_Hote']) . '"/>  <!-- Liste Type_Hote -->';
		echo '<!-- OS -->';
		echo '<label for="Type_OS' . $NbFieldset . '">Système d\'exploitation :</label>';
		echo '<input readonly name="Type_OS' . $NbFieldset . '" id="Type_OS' . $NbFieldset . '" value="' . htmlspecialchars($res_liste_hote['OS']) . '"/> </br>  <!-- Liste Type_OS -->';
		echo '<!-- Architecture -->';
		echo '<label for="Architecture' . $NbFieldset . '">Architecture :</label>';
		echo '<input readonly name="Architecture' . $NbFieldset . '" id="Architecture' . $NbFieldset . '" value="' . htmlspecialchars($res_liste_hote['Architecture']) . '"/> <!-- Liste Architecture -->';
		echo '<!-- Langue -->';
		echo '<label for="Langue' . $NbFieldset . '">Langue :</label>';
		echo '<input readonly name="Langue' . $NbFieldset . '" id="Langue' . $NbFieldset . '" value="' . htmlspecialchars($res_liste_hote['Langue']) . '"/> <!-- Liste Langue -->';
		echo '<!-- Fonction -->';
		echo '<label for="Fonction' . $NbFieldset . '">Fonction(s) :</label>';
		echo '<input readonly type="text" id="Fonction' . $NbFieldset . '" name="Fonction' . $NbFieldset . '" value="' . htmlspecialchars($res_liste_hote['Fonction']) . '" size="30" maxlength="50"/> </br>';
		echo '<!-- Consigne -->';
/**
 * Modification consigne obligatoire
 */
		echo '<span id="Consigne_Hote' . $NbFieldset . '" class="hote' . $NbFieldset . '">Lien vers la consigne :<a href="' . htmlspecialchars($res_liste_hote['Consigne']) . '" target="_blank">' . htmlspecialchars($res_liste_hote['Consigne']) . '</a></span> <br />';
		echo '<!-- Detail consigne -->';
		echo '<label for="Consigne_Hote_Detail' . $NbFieldset . '">Description consigne:</label>';
		echo '<textarea readonly id="Consigne_Hote_Detail' . $NbFieldset . '" name="Consigne_Hote_Detail' . $NbFieldset . '" rows="3" cols="50">' . htmlspecialchars($res_liste_hote['Detail_Consigne']) . '</textarea> <br/>';
		echo '<!-- Action à effectuer -->';
		echo '<label>Action à effectuer:</label>';
		if (htmlspecialchars($res_liste_hote['Type_Action']) == "Creer")
		{
			echo '<input readonly name="Hote_action' . $NbFieldset . '" id="Hote_action' . $NbFieldset . '" value="Creer"/>';
		} else if  (htmlspecialchars($res_liste_hote['Type_Action']) == "Modifier")
		{
			echo '<input readonly name="Hote_action' . $NbFieldset . '" id="Hote_action' . $NbFieldset . '" value="Modifier"/>';
		} else if  (htmlspecialchars($res_liste_hote['Type_Action']) == "Desactiver")
		{
			echo '<input readonly name="Hote_action' . $NbFieldset . '" id="Hote_action' . $NbFieldset . '" value="Desactiver"/>';
		} else if  (htmlspecialchars($res_liste_hote['Type_Action']) == "Supprimer")
		{
			echo '<input readonly name="Hote_action' . $NbFieldset . '" id="Hote_action' . $NbFieldset . '" value="Supprimer"/>';
		} else if  (htmlspecialchars($res_liste_hote['Type_Action']) == "Activer")
		{
			echo '<input readonly name="Hote_action' . $NbFieldset . '" id="Hote_action' . $NbFieldset . '" value="Activer"/>';
		};
		echo  '<br />';
		echo '<!-- Commentaire -->';
		echo '<label for="Hote_Commentaire' . $NbFieldset . '">Commentaire :</label>';
		echo '<textarea readonly id="Hote_Commentaire' . $NbFieldset . '" name="Hote_Commentaire' . $NbFieldset . '" rows="2" cols="50">' . htmlspecialchars($res_liste_hote['Commentaire']) . '</textarea> <br />';
		$ID_Hote = htmlspecialchars($res_liste_hote['ID_Hote']);
		include('insere_fieldset_Admin_Hote.php');
	echo '</fieldset>';

	/**
	 * #21
	 *$NbFieldset ++; 
	 */
	$NumFieldset ++;
};
$Statut_Hote=true;
