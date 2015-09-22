<?php
if (session_id()=='')
{
	session_start();
};

// header("Content-Type: text/plain"); // Utilisation d'un header pour spécifier le type de contenu de la page. Ici, il s'agit juste de texte brut (text/plain).
include_once('connexion_sql_supervision.php');
 /* echo $_POST['monclient']; */
//$sMonClient = (isset($_POST["monclient"])) ? $_POST["monclient"] : NULL;
 
//if ($sMonClient ) {
// récupérer la liste de toutes les demandes à traiter et en cours
try {
	include_once('requete_liste_demande_traite_groupee.php');
	//include_once('requete_liste_demande_traite.php');
} catch (Exception $e) {
	die('Erreur requete liste demande traite: ' . $e->getMessage());
};

//	$req_hote = $bdd_centreon->prepare('SELECT Distinct(host_name), H.host_id, host_alias ,host_address ,if(host_activate=2,"actif","inactif") AS Controle ,Hote from vInventaireServices AS vIS inner join host AS H on vIS.host_id=H.host_id where Code_Client= :Code_Client ORDER BY H.host_name');
//	$req_hote->execute(array('Code_Client' => htmlspecialchars($sMonClient))) or die(print_r($req_hote->errorInfo()));

echo '<table id="T_Liste_Demande">';
	echo '<tr>';
		echo '<th>Action</th>';
		echo '<th onclick="alert(\'Le regroupement est effectué par date de rédaction des demandes. Le tri est décroissant.\nLa date de supervision souhaitée et la date de traitement effective n\\\'est pas prise en compte pour ce regroupement.\')" title="Cliquez pour plus d\'informations.">Date des demandes <img alt="point_interrogation" src="images/point-interrogation-16.png"></th>';
		echo '<th>Nombre de demande</th>';
	echo '</tr>';
	
	$i = 1;
	while ($res_dem_groupee = $req_dem_groupee->fetch())
	{ 
//					$nom_hote = substr(stristr(substr(stristr($res_hote['host_name'],'-'),1),'-'),1); // enlève la localisation et la fonction et les deux -
		//$hote_Hote=$res_hote['Hote'];
		echo '<div id="Demande">';
			echo '<tr>';
	//					echo '<td><input type="checkbox" name="seletion_hote" id="h' . htmlspecialchars($res_hote['host_id']) . '"/></td>';
	//					echo '<td><input type="checkbox" name="selection_hote" id="' . $i . '"/></td>';
	//		echo '<td>' . htmlspecialchars($nom_hote) . '</td>';
			echo '<td><button id="DEC_Afficher_groupee' . htmlspecialchars($res_dem_groupee['ID_Date']) . '" onclick="Afficher_Masquer_DEC_groupee(' . htmlspecialchars($res_dem_groupee['ID_Date']) . ')">Afficher / Masquer</button></td>';
			echo '<td>' . htmlspecialchars($res_dem_groupee['Date']) . '</td>';
			echo '<td>' . htmlspecialchars($res_dem_groupee['Nombre']) . '</td>';
			echo '</tr>';
			if ( $_SESSION['Admin'] == True)
			{
				echo '<td colspan="12">'; // Si profil admin 12 colonnes
			} else
			{
				echo '<td colspan="11">'; // sinon seulement 11
			};
 			echo '<div id="DEC_Detail_groupee' . htmlspecialchars($res_dem_groupee['ID_Date']) . '">';
 				//include('liste_demande_traitees_par_mois.php');
	 			echo '<div id="DEC_liste_groupee' . htmlspecialchars($res_dem_groupee['ID_Date']) . '">';
	 			echo '</div>';
 	// 			echo '<div id="DEC_infos' . htmlspecialchars($res_dem['ID_Demande']) . '">';
	// 			echo '</div>';
	// 			echo '<div id="DEC_hote' . htmlspecialchars($res_dem['ID_Demande']) . '">';
	// 			echo '</div>';
	// 			echo '<div id="DEC_service' . htmlspecialchars($res_dem['ID_Demande']) . '">';
	// 			echo '</div>';
	// 			echo '<div id="DEC_plage' . htmlspecialchars($res_dem['ID_Demande']) . '">';
	// 			echo '</div>';
 			echo '</div>';

			echo '</td>';
		echo '</div>';
		$i ++;
	};
echo '</table>';


 
