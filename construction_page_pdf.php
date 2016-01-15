<?php
echo '<h2 style="text-align:center">Prestation: ' . $prestation . '</h2>';
echo '<h3 style="text-align:center">Date d\'extraction: ' . date("d/m/Y H:i:s") . '</h3>';

echo '<fieldset id=f_extraction_hote">';

echo '<h3 style="text-align:center">Liste des hôtes</h3>';

if ($Nb_Hote == 0) {
	echo '<p>Aucun résultat trouvé. Cette prestation semble vide.</p>';
	exit;
} else {
	echo '<table id="T_Liste_Hote" class="extraction_hote" style="width:100%;border-collapse:collapse">';
	echo '<tr>';
	echo '<th style="border:1px solid #888888;width:20%;text-align:center;">Hôte</th>';
	echo '<th style="border:1px solid #888888;width:25%;text-align:center;">Description</th>';
	echo '<th style="border:1px solid #888888;width:8%;text-align:center;">Adresse IP</th>';
	echo '<th style="border:1px solid #888888;width:3%;text-align:center;">Type</th>';
	echo '<th style="border:1px solid #888888;width:8%;text-align:center;">Localisation</th>';
	echo '<th style="border:1px solid #888888;width:7%;text-align:center;">OS</th>';
	echo '<th style="border:1px solid #888888;width:8%;text-align:center;">Architecture</th>';
	echo '<th style="border:1px solid #888888;width:5%;text-align:center;">Langue</th>';
	echo '<th style="border:1px solid #888888;width:8%;text-align:center;">Fonction</th>';
	echo '<th style="border:1px solid #888888;width:6%;text-align:center;">Controle</th>';
	echo '</tr>';
	$i = 1;
	foreach ( $r_hote as $res_hote ) // on boucle sur les valeurs remontée par la requête
	{
		
		if (htmlspecialchars ( $res_hote ['Controle_Actif'] ) == "inactif")
		{
			echo '<tr style="background:#FFF168;">';
		} else
		{
			echo '<tr>';
		};
		echo '<td style="border:1px solid #888888;">' . htmlspecialchars ( $res_hote ['Nom_Hote'] ) . '</td>';
		echo '<td style="border:1px solid #888888;">' . htmlspecialchars ( $res_hote ['Description'] ) . '</td>';
		echo '<td style="border:1px solid #888888;">' . htmlspecialchars ( $res_hote ['IP_Hote'] ) . '</td>';
		echo '<td style="border:1px solid #888888;">' . htmlspecialchars ( $res_hote ['Type_Hote'] ) . '</td>';
		echo '<td style="border:1px solid #888888;">' . htmlspecialchars ( $res_hote ['ID_Localisation'] ) . '</td>';
		echo '<td style="border:1px solid #888888;">' . htmlspecialchars ( $res_hote ['OS'] ) . '</td>';
		echo '<td style="border:1px solid #888888;">' . htmlspecialchars ( $res_hote ['Architecture'] ) . '</td>';
		echo '<td style="border:1px solid #888888;">' . htmlspecialchars ( $res_hote ['Langue'] ) . '</td>';
		echo '<td style="border:1px solid #888888;">' . htmlspecialchars ( $res_hote ['Fonction'] ) . '</td>';
		//if (htmlspecialchars ( $res_hote ['Controle_Actif'] ) == "inactif") {
			//echo '<td style="border:1px solid #888888;background:#FFF168;">' . htmlspecialchars ( $res_hote ['Controle_Actif'] ) . '</td>';
		//} else {
			echo '<td style="border:1px solid #888888;">' . htmlspecialchars ( $res_hote ['Controle_Actif'] ) . '</td>';
		//};
		echo '</tr>';
		$i ++;
	};
	echo '</table>';
};
echo '</fieldset>';

// ///////////////////
// affichage service
// ///////////////////
echo '<fieldset id="f_extraction_service" style="width:98%;">';
echo '<h3 style="text-align:center">Liste des services</h3>';

while ( $res_liste_service = $SEL_tmp_service->fetch () ) {
	if ($res_liste_service ['Controle_Actif'] == "actif")
	{
		echo '<fieldset id="Service' . $NbFieldset_Service . '" class="extraction_service" style="width:100%;">';
		echo '<h4 id="Num_Service" style="text-decoration: underline;font-weight: bold;text-align:center">Service n°' . $NbFieldset_Service . '</h4>';
	} else 
	{
		echo '<fieldset id="Service' . $NbFieldset_Service . '" class="extraction_service inactif" style="width:100%;background:#FFF168;">';
		echo '<h4 id="Num_Service" style="text-decoration: underline;font-weight: bold;text-align:center">Service n°' . $NbFieldset_Service . ' inactif</h4>';
	};
	//echo '<br />';
	
	echo '<!-- Nom service -->';
	echo '<span id="Lbl_Nom_Service' . $NbFieldset_Service . '" style="text-decoration: underline">Nom du service:</span>';
	echo '<span id="Nom_service' . $NbFieldset_Service . '" style="font-weight: bold"> ' . htmlspecialchars ( $res_liste_service ['Nom_Service'] ) . '</span>';
	echo '<span id="span_espace"> - </span>';
	echo '<span id="Lbl_Hote_Service' . $NbFieldset_Service . '" style="text-decoration: underline">Hôte du service:</span>';
	echo '<span id="Hote_service' . $NbFieldset_Service . '" style="font-weight: bold"> ' . htmlspecialchars ( $res_liste_service ['Nom_Hote'] ) . '</span>';
	echo '<br />';
	echo '<br />';
	echo '<span id="Lbl_Service_Plage' . $NbFieldset_Service . '" style="text-decoration: underline">Plage horaire de contrôle:</span>';
	echo '<span id="Service_Plage' . $NbFieldset_Service . '" style="font-weight: bold"> ' . htmlspecialchars ( $res_liste_service ['Nom_Periode'] ) . '</span>';
	echo '<span id="span_espace"> - </span>';
	echo '<span id="Lbl_Service_Modele' . $NbFieldset_Service . '" style="text-decoration: underline">Modèle:</span>';
	echo '<span id="Service_Modele' . $NbFieldset_Service . '" style="font-weight: bold"> ' . htmlspecialchars ( $res_liste_service ['MS_Modele_Service'] ) . '</span>';
	echo '<span id="span_espace"> - </span>';
	echo '<span id="Lbl_Service_Frequence' . $NbFieldset_Service . '" style="text-decoration: underline">Fréquence de contrôle:</span>';
	echo '<span id="Service_Frequence' . $NbFieldset_Service . '" style="font-weight: bold"> ' . htmlspecialchars ( $res_liste_service ['Frequence'] ) . '</span>';
	echo '<br />';
	echo '<br />';
		
	echo '<!-- Arguments -->';
	echo '<fieldset id="Arg_Service_Modele' . $NbFieldset_Service . '" style="width:100%;">';
	// gestion des arguments
	include ('gestion_arguments.php');
	echo '</fieldset>';
	//echo '<br />';
	if (htmlspecialchars ( $res_liste_service ['Consigne'] ) != "") // s'il n'y a pas de consigne on n'affiche pas le champ
	{
		echo '<!-- Service Consigne -->';
		// $LongueurArg= strlen(htmlspecialchars($res_liste_service['Consigne'])) + 20*strlen(htmlspecialchars($res_liste_service['Consigne']))/100;
//		echo '<span id="Lbl_Service_Consigne' . $NbFieldset_Service . '" style="text-decoration: underline">Lien vers la consigne:</span>';
//		echo '<span id="Service_Consigne' . $NbFieldset_Service . '" style="font-weight: bold"> ' . htmlspecialchars ( $res_liste_service ['Consigne'] ) . '</span>';
		echo '<span id="Service_Consigne' . $NbFieldset_Service . '" class="service' . $NbFieldset_Service . '">Lien vers la consigne :<a href="' . htmlspecialchars($res_liste_service['Consigne']) . '" target="_blank">' . htmlspecialchars($res_liste_service['Consigne']) . '</a></span>';
		echo '<br />';
	};
	echo '</fieldset>';
	$NbFieldset_Service ++;
};
$Statut_Service = true;
echo '</fieldset>';

// ///////////////////
// affichage periode
// ///////////////////

echo '<fieldset id="f_extraction_periode" style="width:100%;">';
echo '<h3 style="text-align:center;">Liste des périodes temporelles</h3>';

// if ($nb_plage == 0)
// {
// 	echo '<p>Aucun résultat trouvé.</p>';
// } else
// {
	echo '<table id="T_Liste_Plage" class="extraction_periode" style="width:100%;border-collapse:collapse">';
	echo '<tr>';
	echo '<th style="border:1px solid #888888;width:30%;text-align:center;">Plage Horaire</th>';
	echo '<th style="border:1px solid #888888;width:10%;text-align:center;">Lundi</th>';
	echo '<th style="border:1px solid #888888;width:10%;text-align:center;">Mardi</th>';
	echo '<th style="border:1px solid #888888;width:10%;text-align:center;">Mercredi</th>';
	echo '<th style="border:1px solid #888888;width:10%;text-align:center;">Jeudi</th>';
	echo '<th style="border:1px solid #888888;width:10%;text-align:center;">Vendredi</th>';
	echo '<th style="border:1px solid #888888;width:10%;text-align:center;">Samedi</th>';
	echo '<th style="border:1px solid #888888;width:10%;text-align:center;">Dimanche</th>';
	echo '</tr>';
	$i = 1;
	foreach ( $r_plage as $res_plage ) 
	{
		echo '<tr>';
		echo '<td style="border:1px solid #888888;">' . htmlspecialchars ( $res_plage ['Nom_Periode'] ) . '</td>';
		echo '<td style="border:1px solid #888888;">' . htmlspecialchars ( $res_plage ['Lundi'] ) . '</td>';
		echo '<td style="border:1px solid #888888;">' . htmlspecialchars ( $res_plage ['Mardi'] ) . '</td>';
		echo '<td style="border:1px solid #888888;">' . htmlspecialchars ( $res_plage ['Mercredi'] ) . '</td>';
		echo '<td style="border:1px solid #888888;">' . htmlspecialchars ( $res_plage ['Jeudi'] ) . '</td>';
		echo '<td style="border:1px solid #888888;">' . htmlspecialchars ( $res_plage ['Vendredi'] ) . '</td>';
		echo '<td style="border:1px solid #888888;">' . htmlspecialchars ( $res_plage ['Samedi'] ) . '</td>';
		echo '<td style="border:1px solid #888888;">' . htmlspecialchars ( $res_plage ['Dimanche'] ) . '</td>';
		echo '</tr>';
		$i ++;
	};
	echo '</table>';
// };
echo '</fieldset>';