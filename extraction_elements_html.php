<?php
if (session_id () == '') {
	session_start ();
};

// header("Content-Type: text/plain"); // Utilisation d'un header pour spécifier le type de contenu de la page. Ici, il s'agit juste de texte brut (text/plain).
$prestation = (isset ( $_POST ["prestation"] )) ? $_POST ["prestation"] : NULL;
// $PDF = (isset ( $_POST ["PDF"] )) ? $_POST ["PDF"] : NULL;

// if ($PDF == NULL)
// {
 	$_SESSION['PDF'] = "Non";
// } else
// {
// 	$_SESSION['PDF'] = $PDF;
// };

//echo '<p>prestation=' . $prestation . '</p>';
//echo '<p>PDF=' . $_SESSION['PDF'] . '</p>';

include('requete_extraction_elements.php'); // préparation des données
// ///////////////////
// affichage des elements
// ///////////////////
// addlog("creation tableau hote...");
echo '<h2> Prestation ' . $prestation . '</h2>';
echo '<fieldset id=f_extraction_hote">';
// if ($_SESSION['PDF'] == "Non")
// {
	echo '<legend>Liste des hôtes</legend>';
// } else
// {
// 	echo '<h3 style="text-align:center">Liste des hôtes</h3>';
// };

if ($Nb_Hote == 0) {
	echo '<p>Aucun résultat trouvé.</p>';
} else {
	echo '<table id="T_Liste_Hote" class="extraction_hote">';
	echo '<tr>';
	echo '<th>Hôte</th>';
	echo '<th>Description</th>';
	echo '<th>Adresse IP</th>';
	echo '<th>Type</th>';
	echo '<th>Localisation</th>';
	echo '<th>OS</th>';
	echo '<th>Architecture</th>';
	echo '<th>Langue</th>';
	echo '<th>Fonction</th>';
	echo '<th>Controle</th>';
	echo '</tr>';
	$i = 1;
	foreach ( $r_hote as $res_hote ) // on boucle sur les valeurs remontée par la requête
	{
		echo '<tr>';
		echo '<td>' . htmlspecialchars ( $res_hote ['Nom_Hote'] ) . '</td>';
		echo '<td>' . htmlspecialchars ( $res_hote ['Description'] ) . '</td>';
		echo '<td>' . htmlspecialchars ( $res_hote ['IP_Hote'] ) . '</td>';
		echo '<td>' . htmlspecialchars ( $res_hote ['Type_Hote'] ) . '</td>';
		echo '<td>' . htmlspecialchars ( $res_hote ['ID_Localisation'] ) . '</td>';
		echo '<td>' . htmlspecialchars ( $res_hote ['OS'] ) . '</td>';
		echo '<td>' . htmlspecialchars ( $res_hote ['Architecture'] ) . '</td>';
		echo '<td>' . htmlspecialchars ( $res_hote ['Langue'] ) . '</td>';
		echo '<td>' . htmlspecialchars ( $res_hote ['Fonction'] ) . '</td>';
		if (htmlspecialchars ( $res_hote ['Controle_Actif'] ) == "inactif")
		{
			echo '<td class="inactif">' . htmlspecialchars ( $res_hote ['Controle_Actif'] ) . '</td>';
		} else
		{
			echo '<td>' . htmlspecialchars ( $res_hote ['Controle_Actif'] ) . '</td>';
		};
		echo '</tr>';
		$i ++;
	};
	echo '</table>';
};
echo '</fieldset>';
// addlog("creation tableau hote... OK.");

// ///////////////////
// affichage service
// ///////////////////
// addlog("creation tableau service...");
echo '<fieldset id="f_extraction_service">';
// if ($_SESSION['PDF'] == "Non")
// {
	echo '<legend>Liste des services</legend>';
// } else
// {
// 	echo '<h3 style="text-align:center">Liste des services</h3>';
// };

// include_once('remplissage_extraction_service.php');

while ( $res_liste_service = $SEL_tmp_service->fetch () ) {
	if ($res_liste_service ['Controle_Actif'] == "actif")
	{
		echo '<fieldset id="Service' . $NbFieldset_Service . '" class="extraction_service">';
	} else
	{
		echo '<fieldset id="Service' . $NbFieldset_Service . '" class="extraction_service inactif">';
	};
// 	if ($_SESSION['PDF'] == "Non")
// 	{
		echo '<legend>Service n°' . $NbFieldset_Service . '</legend>';
// 	} else
// 	{
// 		echo '<h4 id="Num_Service" style="text-decoration: underline;font-weight: bold;text-align:center">Service n°' . $NbFieldset_Service . '</h4>';
// 		//echo '<br />';
// 	};

	echo '<!-- Nom service -->';
	// $LongueurArg= strlen(htmlspecialchars($res_liste_service['Nom_Service'])) + 20*strlen(htmlspecialchars($res_liste_service['Nom_Service']))/100;

// 	if ($_SESSION['PDF'] == "Non")
// 	{
		$LongueurArg = strlen ( htmlspecialchars ( $res_liste_service ['Nom_Service'] ) ) . 'em';
		echo '<label for="Nom_Service' . $NbFieldset_Service . '">Nom du service:</label>';
		echo '<input Readonly type="text" id="Nom_Service' . $NbFieldset_Service . '" name="Nom_Service' . $NbFieldset_Service . '" value="' . htmlspecialchars ( $res_liste_service ['Nom_Service'] ) . '" size="' . $LongueurArg . '"/>';
		echo ' ';
		echo '<!-- Hote du service -->';
		$LongueurArg = strlen ( htmlspecialchars ( $res_liste_service ['Nom_Hote'] ) )+5 . 'em';
		echo '<label for="Hote_Service' . $NbFieldset_Service . '">Hôte du service:</label>';
		echo '<input Readonly name="Hote_Service' . $NbFieldset_Service . '" id="Hote_Service' . $NbFieldset_Service . '" value="' . htmlspecialchars ( $res_liste_service ['Nom_Hote'] ) . '" size="' . $LongueurArg . '" title="' . htmlspecialchars ( $res_liste_service ['IP_Hote'] ) . ' - ' . htmlspecialchars ( $res_liste_service ['ID_Localisation'] ) . '"/>  <!-- Liste Hote disponibles -->';
		echo ' ';
		echo '<br />';
		echo '<!-- Plage Horaire -->';
		$LongueurArg = strlen ( htmlspecialchars ( $res_liste_service ['Nom_Periode'] ) ) . 'em';
		echo '<label for="Service_Plage' . $NbFieldset_Service . '">Plage horaire de contrôle:</label>';
		echo '<input Readonly name="Service_Plage' . $NbFieldset_Service . '" id="Service_Plage' . $NbFieldset_Service . '" value="' . htmlspecialchars ( $res_liste_service ['Nom_Periode'] ) . '" size="' . $LongueurArg . '"/>  <!-- Liste Service_Plage -->';
		echo ' ';
		echo '<!-- Modele service -->';
		$LongueurArg = strlen ( htmlspecialchars ( $res_liste_service ['MS_Modele_Service'] ) ) . 'em';
		echo '<label for="Service_Modele' . $NbFieldset_Service . '">Modèle:</label>';
		echo '<input Readonly name="Service_Modele' . $NbFieldset_Service . '" id="Service_Modele' . $NbFieldset_Service . '" value="' . htmlspecialchars ( $res_liste_service ['MS_Modele_Service'] ) . '" size="' . $LongueurArg . '"/>  <!-- Liste Type_Service -->';
		echo ' ';
		echo '<!-- Frequence -->';
		echo '<label for="Frequence_Service' . $NbFieldset_Service . '">Fréquence du controle:</label>';
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
		if (htmlspecialchars ( $res_liste_service ['Consigne'] ) != "") // s'il n'y a pas de consigne on n'affiche pas le champ
		{
			echo '<br />';
			echo '<!-- Service Consigne -->';
			// $LongueurArg= strlen(htmlspecialchars($res_liste_service['Consigne'])) + 20*strlen(htmlspecialchars($res_liste_service['Consigne']))/100;
			$LongueurArg = strlen ( htmlspecialchars ( $res_liste_service ['Consigne'] ) ). 'em';
			echo '<label for="Service_Consigne' . $NbFieldset_Service . '">Lien vers la consigne :</label>';
			echo '<input Readonly type="text" id="Service_Consigne' . $NbFieldset_Service . '" name="Service_Consigne' . $NbFieldset_Service . '" value="' . htmlspecialchars ( $res_liste_service ['Consigne'] ) . '" size="' . $LongueurArg . '" maxlength="255"/> <br />';
		};
		echo '</fieldset>';
// 	} else
// 	{ // formatage de la page pour le PDF
// 		echo '<span id="Lbl_Nom_Service' . $NbFieldset_Service . '" style="text-decoration: underline">Nom du service:</span>';
// 		echo '<span id="Nom_service' . $NbFieldset_Service . '" style="font-weight: bold"> ' . htmlspecialchars ( $res_liste_service ['Nom_Service'] ) . '</span>';
// 		echo '<span id="span_espace"> - </span>';
// 		echo '<span id="Lbl_Hote_Service' . $NbFieldset_Service . '" style="text-decoration: underline">Hôte du service:</span>';
// 		echo '<span id="Hote_service' . $NbFieldset_Service . '" style="font-weight: bold"> ' . htmlspecialchars ( $res_liste_service ['Nom_Hote'] ) . '</span>';
// 		echo '<br />';
// 		echo '<br />';
// 		echo '<span id="Lbl_Service_Plage' . $NbFieldset_Service . '" style="text-decoration: underline">Plage horaire de contrôle:</span>';
// 		echo '<span id="Service_Plage' . $NbFieldset_Service . '" style="font-weight: bold"> ' . htmlspecialchars ( $res_liste_service ['Nom_Periode'] ) . '</span>';
// 		echo '<span id="span_espace"> - </span>';
// 		echo '<span id="Lbl_Service_Modele' . $NbFieldset_Service . '" style="text-decoration: underline">Modèle:</span>';
// 		echo '<span id="Service_Modele' . $NbFieldset_Service . '" style="font-weight: bold"> ' . htmlspecialchars ( $res_liste_service ['MS_Modele_Service'] ) . '</span>';
// 		echo '<span id="span_espace"> - </span>';
// 		echo '<span id="Lbl_Service_Frequence' . $NbFieldset_Service . '" style="text-decoration: underline">Fréquence de contrôle:</span>';
// 		echo '<span id="Service_Frequence' . $NbFieldset_Service . '" style="font-weight: bold"> ' . htmlspecialchars ( $res_liste_service ['Frequence'] ) . '</span>';
// 		echo '<br />';
// 		echo '<br />';


// 		//		echo '<p id="Nom_Service' . $NbFieldset_Service . '">Nom du service: ' . htmlspecialchars ( $res_liste_service ['Nom_Service'] ) . ' - Hôte du service: ' . htmlspecialchars ( $res_liste_service ['Nom_Hote'] ) . '</p>';
// 		//		echo '<!-- Hote du service -->';
// 		//		echo '<p id="Hote_Service' . $NbFieldset_Service . '">Hôte du service: ' . htmlspecialchars ( $res_liste_service ['Nom_Hote'] ) . '</p>';
// 		//echo '</span>';
// 		//echo '<span id="span2_service' . $NbFieldset_Service . '">';
// 		//		echo '<!-- Plage Horaire -->';
// 		//		echo '<p id="Service_Plage' . $NbFieldset_Service . '">Plage horaire de contrôle: ' . htmlspecialchars ( $res_liste_service ['Nom_Periode'] ) . ' - Modèle: ' . htmlspecialchars ( $res_liste_service ['MS_Modele_Service'] ) . ' - Fréquence du contrôle: ' . htmlspecialchars ( $res_liste_service ['Frequence'] ) . '</p>';
// 		//		echo '<!-- Modele service -->';
// 		//		echo '<p id="Service_Modele' . $NbFieldset_Service . '">Modèle: ' . htmlspecialchars ( $res_liste_service ['MS_Modele_Service'] ) . '</p>';
// 		//		echo '<!-- Frequence -->';
// 		//		echo '<p id="Frequence_Service' . $NbFieldset_Service . '">Fréquence du contrôle: ' . htmlspecialchars ( $res_liste_service ['Frequence'] ) . '</p>';
// 		//echo '</span>';
// 		echo '<!-- Arguments -->';
// 		echo '<fieldset id="Arg_Service_Modele' . $NbFieldset_Service . '" style="width:100%;">';
// 		// gestion des arguments
// 		include ('gestion_arguments.php');
// 		echo '</fieldset>';
// 		//echo '<br />';
// 		if (htmlspecialchars ( $res_liste_service ['Consigne'] ) != "") // s'il n'y a pas de consigne on n'affiche pas le champ
// 		{
// 			echo '<!-- Service Consigne -->';
// 			// $LongueurArg= strlen(htmlspecialchars($res_liste_service['Consigne'])) + 20*strlen(htmlspecialchars($res_liste_service['Consigne']))/100;
// 			echo '<span id="Lbl_Service_Consigne' . $NbFieldset_Service . '" style="text-decoration: underline">Lien vers la consigne:</span>';
// 			echo '<span id="Service_Consigne' . $NbFieldset_Service . '" style="font-weight: bold"> ' . htmlspecialchars ( $res_liste_service ['Consigne'] ) . '</span>';
// 			echo '<br />';
// 		};
// 		echo '</fieldset>';
// 	};
	$NbFieldset_Service ++;
};
$Statut_Service = true;
echo '</fieldset>';
// addlog("creation tableau service... OK.");

// ///////////////////
// affichage periode
// ///////////////////

// addlog("creation tableau periode...");
echo '<fieldset id="f_extraction_periode">';
// if ($_SESSION['PDF'] == "Non")
// {
	echo '<legend>Liste des périodes temporelles</legend>';
// } else
// {
// 	echo '<h3 style="text-align:center">Liste des périodes temporelles</h3>';
// };

if ($nb_plage == 0) {
	echo '<p>Aucun résultat trouvé.</p>';
} else
{
	echo '<table id="T_Liste_Plage" class="extraction_periode">';
	echo '<tr>';
	echo '<th>Plage Horaire</th>';
	echo '<th>Lundi</th>';
	echo '<th>Mardi</th>';
	echo '<th>Mercredi</th>';
	echo '<th>Jeudi</th>';
	echo '<th>Vendredi</th>';
	echo '<th>Samedi</th>';
	echo '<th>Dimanche</th>';
	echo '</tr>';
	$i = 1;
	foreach ( $r_plage as $res_plage )
	{
		echo '<tr>';
		echo '<td>' . htmlspecialchars ( $res_plage ['Nom_Periode'] ) . '</td>';
		echo '<td>' . htmlspecialchars ( $res_plage ['Lundi'] ) . '</td>';
		echo '<td>' . htmlspecialchars ( $res_plage ['Mardi'] ) . '</td>';
		echo '<td>' . htmlspecialchars ( $res_plage ['Mercredi'] ) . '</td>';
		echo '<td>' . htmlspecialchars ( $res_plage ['Jeudi'] ) . '</td>';
		echo '<td>' . htmlspecialchars ( $res_plage ['Vendredi'] ) . '</td>';
		echo '<td>' . htmlspecialchars ( $res_plage ['Samedi'] ) . '</td>';
		echo '<td>' . htmlspecialchars ( $res_plage ['Dimanche'] ) . '</td>';
		echo '</tr>';
		$i ++;
	};
	echo '</table>';
};
echo '</fieldset>';
	// addlog("creation tableau periode... OK.");
