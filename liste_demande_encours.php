<?php
include_once('connexion_sql_supervision.php');
include('log.php'); // chargement de la fonction de log
 
try {
	include_once('requete_liste_demande_encours.php');
} catch (Exception $e) {
	http_response_code(500);
	echo '<p>Erreur requete liste demande encours: ' . $e->getMessage() . '<p/>';
	die('Erreur requete liste demande encours: ' . $e->getMessage());
};
try {
	include_once('requete_liste_demande_encours_temps_global.php');
} catch (Exception $e) {
	http_response_code(500);
	echo '<p>Erreur requete liste demande encours temps global: ' . $e->getMessage() . '<p/>';
	die('Erreur requete liste demande encours temps global: ' . $e->getMessage());
};

$res_dem_tg = $req_dem_tg->fetchall();
foreach($res_dem_tg as $element)
{
	$Temps_Global = $element['Temps_Global'];
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
	echo "<th>Nombre d'Hôtes</th>";
	echo '<th>Nombre de Services</th>';
	echo '<th>Nombre de Plages</th>';
	echo '<th>Etat de la Demande</th>';
	echo '<th onclick="alert(\'Cette estimation purement indicative est basée sur les valeurs suivantes:\nPour les hôtes:\n - Création => 30 minutes\n - Modification => 5 minutes\n - Désactivation ou Suppression => 2 minutes\nPour les services:\n - Création => 5 minutes\n - Modification => 3 minutes\n - Désactivation ou Suppression => 2 minutes\');">Temps estimé ' . htmlspecialchars($Temps_Global) . '<img alt="point_interrogation" src="images/point-interrogation-16.png"></th>';

	if ( $_SESSION['Admin'] == True)
	{
		echo '<th>Admin</th>';
	};
	echo '</tr>'; 

	$i = 1;
	while ($res_dem = $req_dem->fetch())
	{ 
		$couleur_demande = "date_p10";
		if (htmlspecialchars($res_dem['Etat_Demande']) != "Brouillon")
		{
			/**
			 *  calcul de la couleur de la demande
			 */
			$date_jour=date_create(date("Y-m-d"));
			$date_supervision=date_create(date("Y-m-d",strtotime($res_dem['Date_Supervision_Demandee'])));
			$diff=date_diff($date_jour,$date_supervision);
			$diff = $diff->format("%R%a"); // convertit le résultat en numérique pour la comparaison
			if ($diff <= 0)
			{
				$couleur_demande = "dateJ";
			} else if (($diff > 0) AND ($diff <= 4))
			{
				$couleur_demande = "date_m4";
			} else if (($diff > 4) AND ($diff <= 10))
			{
				$couleur_demande = "date_m10";
			};
		};	
		$couleur_type = "type_MiseAJour";
		if (htmlspecialchars($res_dem['Type_Demande']) == "Demarrage")
		{
			$couleur_type = "type_Demarrage";
		};
		echo '<div id="Demande">';
			echo '<tr>';
			echo '<td><button id="DEC_Afficher' . htmlspecialchars($res_dem['ID_Demande']) . '" onclick="Afficher_Masquer_DEC(' . htmlspecialchars($res_dem['ID_Demande']) . ')">Afficher / Masquer</button></td>';
			echo '<td>' . htmlspecialchars($res_dem['Ref_Demande']) . '</td>';
			echo '<td>' . htmlspecialchars($res_dem['Date_Demande']) . '</td>';
			echo '<td>' . htmlspecialchars($res_dem['Demandeur']) . '</td>';
			echo '<td class="' . $couleur_demande . '">' . htmlspecialchars($res_dem['Date_Supervision_Demandee']) . '</td>';
			echo '<td class="' . $couleur_type . '">' . htmlspecialchars($res_dem['Type_Demande']) . '</td>';
			echo '<td>' . htmlspecialchars($res_dem['Code_Client']) . '</td>';
			echo '<td>' . htmlspecialchars($res_dem['NbHote']) . '</td>';
			echo '<td>' . htmlspecialchars($res_dem['NbService']) . '</td>';
			echo '<td>' . htmlspecialchars($res_dem['NbPlage']) . '</td>';
			if ((htmlspecialchars($res_dem['Etat_Demande']) == "Brouillon") && ($_SESSION['user_changement_centreon'] == htmlspecialchars($res_dem['Demandeur']))) // si brouillon et user=demandeur => lien édition
			{ // on charge la page reprise_demande sur le modèle d'une nouvelle demande
				echo '<td><ul class="Etat_Demande">
						<li>
						<a href="reprise_demande.php?demandeur=' . htmlspecialchars($res_dem['Demandeur']) . '&amp;id_demande=' . htmlspecialchars($res_dem['ID_Demande']) . '">' . htmlspecialchars($res_dem['Etat_Demande']) .'</a>
						</li>
					</ul></td>';
			} else // pas de lien cliquable pour tous les autres
			{
				echo '<td>' . htmlspecialchars($res_dem['Etat_Demande']) .'</td>';
			};
			
			/**
			 *  le formatage du temps est fait directement dans la requête d'extraction.
			 */
			echo '<td>' . htmlspecialchars($res_dem['Temps']) . '</td>';
			if ( $_SESSION['Admin'] == True)
			{
				$bouton_ID="DEC_Enregistrer_Etat" . htmlspecialchars ( $res_dem ['ID_Demande'] );
                echo '<td>';
				echo 'ID_Dem=' .  htmlspecialchars($res_dem['ID_Demande']);
				echo '<select onChange="set_focus_bouton(\'' . $bouton_ID . '\');" name="Liste_DEC_Enregistrer_Etat' . htmlspecialchars($res_dem['ID_Demande']) . '" id="Liste_DEC_Enregistrer_Etat' . htmlspecialchars($res_dem['ID_Demande']) . '">';
				try {
					$etat_dem = $res_dem['Etat_Demande'];
					include('requete_liste_Etat_Demande.php');
				} catch (Exception $e) {
					echo '</select>';
					die('Erreur requete liste etat demande: ' . $e->getMessage());
				};
                                while ($res_etat = $req_etat->fetch())
                                {
									if (htmlspecialchars($res_etat['Etat_Dem']) != "En cours") // on ne peut pas supprimer un élément unitaire de la demande, donc il n'est pas ajouté à la liste.
									{
										if (htmlspecialchars($res_dem['Etat_Demande']) == htmlspecialchars($res_etat['Etat_Dem']))
										{
											echo '<option Selected="Selected" value="' . htmlspecialchars($res_etat['Etat_Dem']) . '">' . htmlspecialchars($res_etat['Etat_Dem']) . '</option> ';
										} else
										{
											echo '<option value="' . htmlspecialchars($res_etat['Etat_Dem']) . '">' . htmlspecialchars($res_etat['Etat_Dem']) . '</option> ';
										};
									} elseif (htmlspecialchars($res_dem['Etat_Demande']) != "A Traiter")
									{
										if (htmlspecialchars($res_dem['Etat_Demande']) == htmlspecialchars($res_etat['Etat_Dem']))
										{
											echo '<option Selected="Selected" value="' . htmlspecialchars($res_etat['Etat_Dem']) . '">' . htmlspecialchars($res_etat['Etat_Dem']) . '</option> ';
										} else
										{
											echo '<option value="' . htmlspecialchars($res_etat['Etat_Dem']) . '">' . htmlspecialchars($res_etat['Etat_Dem']) . '</option> ';
										};
									};
                                };
                                echo '</select>';
                                echo '';
                                echo '<button id="DEC_Enregistrer_Etat' . htmlspecialchars($res_dem['ID_Demande']) . '" onclick="DEC_enregistre_Etat_Demande(this,' . htmlspecialchars($res_dem['ID_Demande']) . ')">Forcer</button>';
			echo '</td>';
			};
			echo '</tr>';
			/**
			 *  Ajuster la valeur au nombre de colonnes
			 */
			if ( $_SESSION['Admin'] == True)
			{
				echo '<td colspan="13">'; // Si profil admin 13 colonnes 
			} else 
			{
				echo '<td colspan="12">'; // sinon seulement 12
			};
			echo '<div id="DEC_Detail' . htmlspecialchars($res_dem['ID_Demande']) . '">
					<div id="DEC_infos' . htmlspecialchars($res_dem['ID_Demande']) . '">
					</div>
					<div id="DEC_hote' . htmlspecialchars($res_dem['ID_Demande']) . '">
					</div>
					<div id="DEC_service' . htmlspecialchars($res_dem['ID_Demande']) . '">
					</div>
					<div id="DEC_plage' . htmlspecialchars($res_dem['ID_Demande']) . '">
					</div>
				</div>
			</td>';
		echo '</div>';
		$i ++;
	};
echo '</table>';
