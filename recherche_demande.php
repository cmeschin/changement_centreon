<?php
include_once ('connexion_sql_supervision.php');
if ($ID_Demande == NULL)
{
	$ID_Demande = "NULL";
};
if ($Recherche == NULL)
{
	$Recherche = "NULL";
};

try 
{
	include_once('requete_recherche_demande.php');
} catch (Exception $e) {
	die('Erreur requete_recherche_demande: '. $e->getMessage());
};
echo '<table id="T_Liste_Demande">';
	echo '<tr>';
		echo '<th>Action</th>';
		echo '<th>Réf Demande</th>';
		echo '<th>Date Demande</th>';
		echo '<th>Demandeur</th>';
		echo '<th>Date de Supervision souhaitée</th>';
		echo '<th>Type de la demande</th>';
		echo '<th>Prestation</th>';
		echo '<th>Nombre d\'Hôtes</th>';
		echo '<th>Nombre de Services</th>';
		echo '<th>Nombre de Plages</th>';
		echo '<th>Etat de la Demande</th>';
		echo '<th onclick="alert(\'Cette estimation purement indicative est basée sur les valeurs suivantes:\nPour les hôtes:\n - Création => 30 minutes\n - Modification => 5 minutes\n - Désactivation ou Suppression => 2 minutes\nPour les services:\n - Création => 5 minutes\n - Modification => 3 minutes\n - Désactivation ou Suppression => 2 minutes\');">Temps estimé <img alt="point_interrogation" src="images/point-interrogation-16.png"></th>';
		if ($_SESSION ['Admin'] == True)
		{
			echo '<th>Admin</th>';
		};
	echo '</tr>'; 
	$tab_req_dem = $req_dem->fetchall();
	$nblignes=count($tab_req_dem);
	if ($tab_req_dem[0] == NULL )
	{
		echo '<p>Aucun élément trouvé pour cette recherche</p>';
	} else 
	{
		$i = 1;
		foreach($tab_req_dem as $res_dem)
		{
			$ID_Demande = htmlspecialchars($res_dem['ID_Demande']);
			echo '<div id="Demande">';
			echo '<tr>';
			echo '<td><button id="DEC_Afficher' . $ID_Demande . '" onclick="Afficher_Masquer_DEC(' . $ID_Demande . ')">Afficher / Masquer</button></td>';
			echo '<td>' . htmlspecialchars ( $res_dem ['Ref_Demande'] ) . '</td>';
			echo '<td>' . htmlspecialchars ( $res_dem ['Date_Demande'] ) . '</td>';
			echo '<td>' . htmlspecialchars ( $res_dem ['Demandeur'] ) . '</td>';
			echo '<td>' . htmlspecialchars ( $res_dem ['Date_Supervision_Demandee'] ) . '</td>';
			echo '<td>' . htmlspecialchars($res_dem['Type_Demande']) . '</td>';
			if (substr ( htmlspecialchars ( $res_dem ['Code_Client'] ), 0, 4 ) == "NEW_") 
			{
				echo '<td>' . substr ( htmlspecialchars ( $res_dem ['Code_Client'] ), 4 ) . '</td>';
			} else
			{
				echo '<td>' . htmlspecialchars ( $res_dem ['Code_Client'] ) . '</td>';
			};
			echo '<td>' . htmlspecialchars ( $res_dem ['NbHote'] ) . '</td>';
			echo '<td>' . htmlspecialchars ( $res_dem ['NbService'] ) . '</td>';
			echo '<td>' . htmlspecialchars ( $res_dem ['NbPlage'] ) . '</td>';
			if ((htmlspecialchars ( $res_dem ['Etat_Demande'] ) == "Brouillon") && ($_SESSION ['user_changement_centreon'] == htmlspecialchars ( $res_dem ['Demandeur'] ))) 		// si brouillon et user=demandeur => lien édition
			{ // on charge la page reprise_demande sur le modèle d'une nouvelle demande
				echo '<td><ul class="Etat_Demande">
							<li>
							<a href="reprise_demande.php?demandeur=' . htmlspecialchars ( $res_dem ['Demandeur'] ) . '&amp;id_demande=' . $ID_Demande . '">' . htmlspecialchars ( $res_dem ['Etat_Demande'] ) . '</a>
							</li>
						</ul></td>';
			} else 		// pas de lien cliquable pour tous les autres
			{
				echo '<td>' . htmlspecialchars ( $res_dem ['Etat_Demande'] ) . '</td>';
			};
			echo '<td>' . htmlspecialchars(floor($res_dem['Temps']/60) . 'h' . ($res_dem['Temps']%60)) . '</td>';
			if ($_SESSION ['Admin'] == True) 
			{
				echo '<td>';
				echo 'ID_Dem=' . $ID_Demande;
				echo '<select name="Liste_DEC_Enregistrer_Etat' . $ID_Demande . '" id="Liste_DEC_Enregistrer_Etat' . $ID_Demande . '">';
				try 
				{
					$etat_dem = $res_dem['Etat_Demande'];
					include ('requete_liste_Etat_Demande.php');
				} catch (Exception $e) 
				{
					echo '</select>';
					die('Erreur requete_liste_etat_demande: ' . $e->getMessage());
				};
				while ( $res_etat = $req_etat->fetch () ) 
				{
					if (htmlspecialchars ( $res_dem ['Etat_Demande'] ) != htmlspecialchars ( $res_etat ['Etat_Dem'] )) 
					{
						echo '<option value="' . htmlspecialchars ( $res_etat ['Etat_Dem'] ) . '">' . htmlspecialchars ( $res_etat ['Etat_Dem'] ) . '</option> ';
					};
				};
				echo '</select>';
				echo '';
				echo '<button id="DEC_Enregistrer_Etat' . $ID_Demande . '" onclick="DEC_enregistre_Etat_Demande(this,' . $ID_Demande . ')">Forcer</button>';
				echo '</td>';
			};
			echo '</tr>';
			// Ajuster la valeur au nombre de colonnes
			if ( $_SESSION['Admin'] == True)
			{
				echo '<td colspan="13">'; // Si profil admin 13 colonnes 
			} else 
			{
				echo '<td colspan="12">'; // sinon seulement 12
			};
				echo '<div id="DEC_Detail' . $ID_Demande . '">';
					echo '<div id="DEC_infos' . $ID_Demande . '">';
					include ('remplissage_DEC_infos.php');
					echo '</div>';
					echo '<div id="DEC_hote' . $ID_Demande . '">';
					include ('remplissage_DEC_hote.php');
					echo '</div>';
					echo '<div id="DEC_service' . $ID_Demande . '">';
					include ('remplissage_DEC_service.php');
					echo '</div>';
					echo '<div id="DEC_plage' . $ID_Demande . '">';
					include ('remplissage_DEC_plage.php');
					echo '</div>';
				echo '</div>';
			echo '</td>';
		echo '</div>';
		$i ++;
		};
	};
echo '</table>';