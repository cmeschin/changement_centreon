<?php
if (session_id()=='')
{
	session_start();
};
include_once('connexion_sql_supervision.php');
try {
	include_once('requete_liste_ttes_demandes_traite_groupee.php');
} catch (Exception $e) {
	die('Erreur requete liste ttes demandes traite: ' . $e->getMessage());
};

echo '<table id="T_Liste_Demande">';
	echo '<tr>';
		echo '<th>Action</th>';
		echo '<th onclick="alert(\'Le regroupement est effectué par date de rédaction des demandes. Le tri est décroissant.\nLa date de supervision souhaitée et la date de traitement effective n\\\'est pas prise en compte pour ce regroupement.\')" title="Cliquez pour plus d\'informations.">Date des demandes <img alt="point_interrogation" src="images/point-interrogation-16.png"></th>';
		echo '<th>Nombre de demande</th>';
	echo '</tr>';
	
	$i = 1;
	while ($res_dem_groupee = $req_dem_groupee->fetch())
	{ 
		echo '<div id="Demande">';
			echo '<tr>';
			echo '<td><button id="DEC_Afficher_groupee' . htmlspecialchars($res_dem_groupee['ID_Date']) . '" onclick="Afficher_Masquer_DEC_ttes_demandes_groupee(' . htmlspecialchars($res_dem_groupee['ID_Date']) . ')">Afficher / Masquer</button></td>';
			echo '<td>' . htmlspecialchars($res_dem_groupee['Date']) . '</td>';
			echo '<td>' . htmlspecialchars($res_dem_groupee['Nombre']) . '</td>';
			echo '</tr>';
			if ( $_SESSION['Admin'] == True)
			{
				echo '<td colspan="13">'; // Si profil admin 13 colonnes
			} else
			{
				echo '<td colspan="12">'; // sinon seulement 12
			};
 			echo '<div id="DEC_Detail_groupee' . htmlspecialchars($res_dem_groupee['ID_Date']) . '">';
	 			echo '<div id="DEC_liste_groupee' . htmlspecialchars($res_dem_groupee['ID_Date']) . '">';
	 			echo '</div>';
 			echo '</div>';
			echo '</td>';
		echo '</div>';
		$i ++;
	};
echo '</table>';
