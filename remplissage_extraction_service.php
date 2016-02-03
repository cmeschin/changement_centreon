<?php
/*
if (session_id () == '') {
	session_start ();
}
;
if ($_SESSION ['R_ID_Demande'] == NULL) {
	$ID_Demande = (isset ( $_POST ["ID_Dem"] )) ? $_POST ["ID_Dem"] : NULL;
} else {
	$ID_Demande = $_SESSION ['R_ID_Demande'];
}
*/
//include_once ('connexion_sql_supervision.php');
$SEL_tmp_service = $bdd_supervision->prepare ( 'SELECT
		 Nom_Service,
		 Nom_Hote,
		 Nom_Periode,
		 MS_Modele_Service,
		 Frequence,
		 Consigne,
		 Detail_Consigne,
		 Commentaire,
		 Type_Action,
		 MS_Libelles,
		 MS_Arguments,
		 Parametres
		 FROM ' . $tbl_tmp_service . '' );
$SEL_tmp_service->execute ( Array () ) or die ( print_r ( $SEL_tmp_service->errorInfo () ) );

// Selection de tous les services de la demande
// Detail de la requête
// Nom_Service 0
// Nom_Hote 1
// IP_Hote 2
// ID_Localisation 3
// Nom_Periode 4
// Frequence 5
// Consigne 6
// Controle_Actif 7
// MS_Modele_Service 8
// MS_Libelles 9
// Parametres 10
// Detail_Consigne 11
// Type_Action 12
// Etat_Parametrage 13
// ID_Service 14
// Commentaire 15
// MS_Description 16
// MS_Arguments 17
// MS_Macro 18
// MS_EST_MACRO 19
// ID_Hote 20

$liste_service = "";
$NbFieldset_Service = 1;
while ( $res_liste_service = $SEL_tmp_service->fetch () ) {
	echo '<fieldset id="Service' . $NbFieldset_Service . '" class="service">';
	echo '<legend>Service n°' . $NbFieldset_Service . '</legend>';
	echo '<!-- Nom service -->';
	// $LongueurArg= strlen(htmlspecialchars($res_liste_service['Nom_Service'])) + 20*strlen(htmlspecialchars($res_liste_service['Nom_Service']))/100;
	$LongueurArg = strlen ( htmlspecialchars ( $res_liste_service ['Nom_Service'] ) ) + 10;
	echo '<label for="Nom_Service' . $NbFieldset_Service . '">Nom de la sonde:</label>';
	echo '<input Readonly type="text" id="Nom_Service' . $NbFieldset_Service . '" name="Nom_Service' . $NbFieldset_Service . '" value="' . htmlspecialchars ( $res_liste_service ['Nom_Service'] ) . '" size="' . $LongueurArg . '" maxlength="100"/>';
	echo ' ';
	echo '<!-- Hote du service -->';
	$LongueurArg = strlen ( htmlspecialchars ( $res_liste_service ['Nom_Hote'] ) ) + 10;
	echo '<label for="Hote_Service' . $NbFieldset_Service . '">Hôte de la sonde:</label>';
	echo '<input Readonly name="Hote_Service' . $NbFieldset_Service . '" id="Hote_Service' . $NbFieldset_Service . '" value="' . htmlspecialchars ( $res_liste_service ['Nom_Hote'] ) . '" size="' . $LongueurArg . '" title="' . htmlspecialchars ( $res_liste_service ['IP_Hote'] ) . ' - ' . htmlspecialchars ( $res_liste_service ['ID_Localisation'] ) . '"/>  <!-- Liste Hote disponibles -->';
	echo ' ';
	echo '<br />';
	echo '<!-- Plage Horaire -->';
	$LongueurArg = strlen ( htmlspecialchars ( $res_liste_service ['Nom_Periode'] ) ) + 10;
	echo '<label for="Service_Plage' . $NbFieldset_Service . '">Plage horaire de contrôle:</label>';
	echo '<input Readonly name="Service_Plage' . $NbFieldset_Service . '" id="Service_Plage' . $NbFieldset_Service . '" value="' . htmlspecialchars ( $res_liste_service ['Nom_Periode'] ) . '" size="' . $LongueurArg . '"/>  <!-- Liste Service_Plage -->';
	echo ' ';
	echo '<!-- Modele service -->';
	$LongueurArg = strlen ( htmlspecialchars ( $res_liste_service ['MS_Modele_Service'] ) ) + 10;
	echo '<label for="Service_Modele' . $NbFieldset_Service . '">Modèle:</label>';
	echo '<input Readonly name="Service_Modele' . $NbFieldset_Service . '" id="Service_Modele' . $NbFieldset_Service . '" value="' . htmlspecialchars ( $res_liste_service ['MS_Modele_Service'] ) . '" size="' . $LongueurArg . '"/>  <!-- Liste Type_Service -->';
	echo ' ';
	echo '<!-- Frequence -->';
	echo '<label for="Frequence_Service' . $NbFieldset_Service . '">Fréquence du contrôle:</label>';
	echo '<input Readonly type="text" id="Frequence_Service' . $NbFieldset_Service . '" name="Frequence_Service' . $NbFieldset_Service . '" value="' . htmlspecialchars ( $res_liste_service ['Frequence'] ) . '" size="20" maxlength="20"/> <br />';
	echo ' ';
	echo '<!-- Arguments -->';
	echo '<fieldset id="Arg_Service_Modele' . $NbFieldset_Service . '">';
	// echo '<legend>Arguments du service</legend>';
	// gestion des arguments
	include ('gestion_arguments.php');
	echo '</fieldset> <br /> ';
	/*
	 * echo '<fieldset id="Inactif_Arg_Service_Modele' . $NbFieldset_Service . '">';
	 * echo '<legend>Arguments du service initial</legend>';
	 * //gestion des arguments
	 * include('gestion_arguments.php');
	 * echo '</fieldset>';
	 */
	echo '<br />';
	echo '<!-- Service Consigne -->';
	// $LongueurArg= strlen(htmlspecialchars($res_liste_service['Consigne'])) + 20*strlen(htmlspecialchars($res_liste_service['Consigne']))/100;
	$LongueurArg = strlen ( htmlspecialchars ( $res_liste_service ['Consigne'] ) );
	echo '<label for="Service_Consigne' . $NbFieldset_Service . '">Lien vers la consigne :</label>';
	echo '<input Readonly type="text" id="Service_Consigne' . $NbFieldset_Service . '" name="Service_Consigne' . $NbFieldset_Service . '" value="' . htmlspecialchars ( $res_liste_service ['Consigne'] ) . '" size="' . $LongueurArg . '" maxlength="255"/> <br />';
	echo '<!-- Service Consigne Description-->';
	echo '<label for="Consigne_Service_Detail' . $NbFieldset_Service . '">Description consigne :</label>';
	echo '<textarea Readonly id="Consigne_Service_Detail' . $NbFieldset_Service . '" name="Consigne_Service_Detail' . $NbFieldset_Service . '" rows="3" cols="50">' . htmlspecialchars ( $res_liste_service ['Detail_Consigne'] ) . '</textarea> <br />';
	echo '<!-- Action à effectuer -->';
	echo '<label>Action à effectuer</label>';
	if (htmlspecialchars ( $res_liste_service ['Type_Action'] ) == "Creer") {
		echo '<input readonly name="Service_action' . $NbFieldset_Service . '" id="Service_action' . $NbFieldset_Service . '" value="Créer"/>';
	} else if (htmlspecialchars ( $res_liste_service ['Type_Action'] ) == "Modifier") {
		echo '<input readonly name="Service_action' . $NbFieldset_Service . '" id="Service_action' . $NbFieldset_Service . '" value="Modifier"/>';
	} else if (htmlspecialchars ( $res_liste_service ['Type_Action'] ) == "Desactiver") {
		echo '<input readonly name="Service_action' . $NbFieldset_Service . '" id="Service_action' . $NbFieldset_Service . '" value="Désactiver"/>';
	} else if (htmlspecialchars ( $res_liste_service ['Type_Action'] ) == "Supprimer") {
		echo '<input readonly name="Service_action' . $NbFieldset_Service . '" id="Service_action' . $NbFieldset_Service . '" value="Supprimer"/>';
	} else if (htmlspecialchars ( $res_liste_service ['Type_Action'] ) == "Activer") {
		echo '<input readonly name="Service_action' . $NbFieldset_Service . '" id="Service_action' . $NbFieldset_Service . '" value="Activer"/>';
	}
	;
	echo '<br />';
	echo '<!-- Service Commentaire -->';
	echo '<label for="Service_Commentaire' . $NbFieldset_Service . '">Commentaire :</label>';
	echo '<textarea id="Service_Commentaire' . $NbFieldset_Service . '" name="Service_Commentaire' . $NbFieldset_Service . '" rows="3" cols="50" class="service' . $NbFieldset_Service . '">' . htmlspecialchars ( $res_liste_service ['Commentaire'] ) . '</textarea> <br />';
	
	if ($_SESSION ['Admin'] == True) // si admin affichage liste déroulante etat + bouton enregistrer
{
		$ID_Service = htmlspecialchars ( $res_liste_service ['ID_Service'] );
		include ('insere_fieldset_Admin_Service.php');
	}
	;
	echo '</fieldset>';
	
	$NbFieldset_Service ++;
}
;
$Statut_Service = true;
